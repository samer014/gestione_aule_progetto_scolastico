<?php
require_once 'vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class TokenController {
    private $secretKey;
    private $algorithm;
    private $maxLoginAttempts = 5;
    private $lockoutTime = 900; // 15 minutes in seconds
    
    public function __construct() {
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

        // Validate client credentials
        if (!$this->validateCredentials($clientId, $clientSecret)) {
            $this->logFailedAttempt($clientId);
            http_response_code(401);
            return json_encode(['error' => 'Invalid credentials']);
        }

        $issuedAt = time();
        $expire = $issuedAt + 3600; // Token expires in 1 hour
        $jti = bin2hex(random_bytes(16)); // Unique token ID

        $payload = [
            'iss' => 'your-app-name',
            'aud' => $clientId,
            'iat' => $issuedAt,
            'exp' => $expire,
            'jti' => $jti,
            'scope' => ['read', 'write'],
            'nbf' => $issuedAt // Not before claim
        ];

        try {
            // Rotate keys if needed
            $this->rotateKeysIfNeeded();
            
            $token = JWT::encode($payload, $this->secretKey, $this->algorithm);
            
            // Store token metadata for revocation if needed
            $this->storeTokenMetadata($jti, $clientId, $expire);
            
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
        // Implement rate limiting logic using Redis or similar
        return false; // Placeholder
    }

    private function logFailedAttempt($clientId) {
        // Implement failed login attempt logging
    }

    private function rotateKeysIfNeeded() {
        // Implement key rotation logic
    }

    private function storeTokenMetadata($jti, $clientId, $expire) {
        global $con;
        $issuedAt = time();
        $stmt = $con->prepare(
            "INSERT INTO jwt_tokens (user_id, access_token, jti, issued_at, expires_at) 
             VALUES (?, '', ?, FROM_UNIXTIME(?), FROM_UNIXTIME(?))"
        );
        $stmt->execute([$clientId, $jti, $issuedAt, $expire]);
    }

    private function logError($message) {
        // Implement secure error logging
    }

    private function validateCredentials($clientId, $clientSecret) {
        // Implement your credential validation logic here
        return true; // Placeholder
    }

    private function revokeToken($jti) {
        global $con;
        $stmt = $con->prepare("UPDATE jwt_tokens SET revoked = 1 WHERE jti = ?");
        $stmt->execute([$jti]);
    }
}
?>
<?php
    class TokenController {
        private function isRateLimited($clientId) {
                global $con;
                $stmt = $con->prepare(
                    "SELECT COUNT(*) FROM login_attempts 
                    WHERE client_id = ? 
                    AND attempt_time > DATE_SUB(NOW(), INTERVAL 15 MINUTE)"
                );
                $stmt->execute([$clientId]);
                return $stmt->fetchColumn() >= $this->maxLoginAttempts;
            }

            private function logFailedAttempt($clientId) {
                global $con;
                $stmt = $con->prepare(
                    "INSERT INTO login_attempts (client_id, ip_address) 
                    VALUES (?, ?)"
                );
                $stmt->execute([$clientId, $_SERVER['REMOTE_ADDR']]);
            }

            private function validateCredentials($clientId, $clientSecret) {
                global $con;
                $stmt = $con->prepare(
                    "SELECT 1 FROM utenti 
                    WHERE username = ? AND password = ?"
                );
                $stmt->execute([$clientId, hash('sha256', $clientSecret)]);
                return $stmt->rowCount() > 0;
        }
    }
?>