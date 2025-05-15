<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../config/database.php'; // Modifica il percorso se necessario
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class TokenController {
    private $secretKey;
    private $algorithm;
    private $maxLoginAttempts = 5;
    private $lockoutTime = 900; // 15 minutes in seconds
    private $con;
    
    public function __construct($con) {
        $this->con = $con;
        if (file_exists(__DIR__ . '/../../.env')) {
            $lines = file(__DIR__ . '/../../.env');
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0 || trim($line) === '') continue;
                putenv(trim($line));
            }
        }
        $this->secretKey = getenv('JWT_SECRET_KEY');
        $this->algorithm = 'HS256';
        
        // Set security headers
        header("Content-Security-Policy: default-src 'self'");
        header("X-Content-Type-Options: nosniff");
        header("X-Frame-Options: DENY");
        header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
    }

    public function generateToken($clientId, $clientSecret) {
        // Rate limiting check
        if ($this->isRateLimited($clientId)) {
            http_response_code(429);
            return json_encode(['error' => 'Too many attempts. Please try again later.']);
        }

        // Recupera id utente e valida credenziali
        $stmt = $this->con->prepare("SELECT id FROM utenti WHERE username = ? AND password = ?");
        $stmt->execute([$clientId, hash('sha256', $clientSecret)]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user) {
            $this->logFailedAttempt($clientId);
            http_response_code(401);
            return json_encode(['error' => 'Invalid credentials']);
        }
        $userId = $user['id'];

        $issuedAt = time();
        $expire = $issuedAt + 3600;
        $jti = bin2hex(random_bytes(16));
        $payload = [
            'iss' => 'your-app-name',
            'aud' => $clientId,
            'iat' => $issuedAt,
            'exp' => $expire,
            'jti' => $jti,
            'scope' => ['read', 'write'],
            'nbf' => $issuedAt
        ];

        try {
            $this->rotateKeysIfNeeded();
            $token = JWT::encode($payload, $this->secretKey, $this->algorithm);
            $this->storeTokenMetadata($jti, $userId, $expire);
            return json_encode([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => 3600
            ]);
        } catch (Exception $e) {
            $this->logError('Token generation failed: ' . $e->getMessage());
            http_response_code(500);
            return json_encode(['error' => 'Token generation failed']);
        }
    }

    public function generateRefreshToken($userId) {
        $issuedAt = time();
        $expire = $issuedAt + (7 * 24 * 60 * 60); // 7 giorni
        
        $payload = [
            'iss' => 'your-app-name',
            'sub' => $userId,
            'iat' => $issuedAt,
            'exp' => $expire,
            'jti' => bin2hex(random_bytes(16))
        ];
        
        return JWT::encode($payload, $this->secretKey, $this->algorithm);
    }

    private function isRateLimited($clientId) {
        // Puoi personalizzare la logica, qui un esempio base:
        $stmt = $this->con->prepare(
            "SELECT COUNT(*) FROM login_attempts 
             WHERE client_id = ? 
             AND attempt_time > DATE_SUB(NOW(), INTERVAL 15 MINUTE)"
        );
        $stmt->execute([$clientId]);
        return $stmt->fetchColumn() >= $this->maxLoginAttempts;
    }

    private function logFailedAttempt($clientId) {
        $stmt = $this->con->prepare(
            "INSERT INTO login_attempts (client_id, ip_address, attempt_time) 
             VALUES (?, ?, NOW())"
        );
        $stmt->execute([$clientId, $_SERVER['REMOTE_ADDR']]);
    }

    private function rotateKeysIfNeeded() {
        // Implement key rotation logic
    }

    private function storeTokenMetadata($jti, $userId, $expire) {
        $issuedAt = time();
        $stmt = $this->con->prepare(
            "INSERT INTO jwt_tokens (user_id, access_token, jti, issued_at, expires_at) 
             VALUES (?, '', ?, FROM_UNIXTIME(?), FROM_UNIXTIME(?))"
        );
        $stmt->execute([$userId, $jti, $issuedAt, $expire]);
    }

    private function logError($message) {
        // Implement secure error logging
    }

    private function validateCredentials($clientId, $clientSecret) {
        $stmt = $this->con->prepare(
            "SELECT 1 FROM utenti 
            WHERE id = ? AND password = ?"
        );
        $stmt->execute([$clientId, hash('sha256', $clientSecret)]);
        return $stmt->rowCount() > 0;
    }

    private function revokeToken($jti) {
        $stmt = $this->con->prepare("UPDATE jwt_tokens SET revoked = 1 WHERE jti = ?");
        $stmt->execute([$jti]);
    }

    private function isTokenValid($jti) {
        $stmt = $this->con->prepare("SELECT revoked FROM jwt_tokens WHERE jti = ?");
        $stmt->execute([$jti]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row && $row['revoked'] == 0;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $clientId = $data['clientId'] ?? null;
    $clientSecret = $data['clientSecret'] ?? null;
    $controller = new TokenController($con);
    header('Content-Type: application/json');
    echo $controller->generateToken($clientId, $clientSecret);
} else {
    header('Content-Type: application/json');
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
}
?>