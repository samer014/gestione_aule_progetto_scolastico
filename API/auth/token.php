<?php
require_once 'vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class TokenController {
    private $secretKey;
    private $algorithm;
    
    public function __construct() {
        $this->secretKey = getenv('JWT_SECRET_KEY');
        $this->algorithm = 'HS256';
    }

    public function generateToken($clientId, $clientSecret) {
        // Validate client credentials
        if (!$this->validateCredentials($clientId, $clientSecret)) {
            http_response_code(401);
            return json_encode(['error' => 'Invalid credentials']);
        }

        $issuedAt = time();
        $expire = $issuedAt + 3600; // Token expires in 1 hour

        $payload = [
            'iss' => 'your-app-name',
            'aud' => $clientId,
            'iat' => $issuedAt,
            'exp' => $expire,
            'scope' => ['read', 'write'] // Define allowed scopes
        ];

        try {
            $token = JWT::encode($payload, $this->secretKey, $this->algorithm);
            return json_encode([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => 3600
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            return json_encode(['error' => 'Token generation failed']);
        }
    }

    private function validateCredentials($clientId, $clientSecret) {
        // Implement your credential validation logic here
        return true; // Placeholder
    }
}
?>