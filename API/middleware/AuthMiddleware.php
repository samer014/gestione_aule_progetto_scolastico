<?php

require_once 'vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware {
    private $secretKey;
    private $tokenBlacklist;

    public function __construct() {
        $this->secretKey = getenv('JWT_SECRET_KEY');
        $this->tokenBlacklist = new TokenBlacklist(); // Implement this class
        
        // Set security headers
        header("Content-Security-Policy: default-src 'self'");
        header("X-Content-Type-Options: nosniff");
        header("X-Frame-Options: DENY");
        header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
    }

    public function validateToken() {
        if (!$this->isHttps()) {
            http_response_code(403);
            return json_encode(['error' => 'HTTPS required']);
        }

        $headers = apache_request_headers();
        
        if (!isset($headers['Authorization'])) {
            http_response_code(401);
            return json_encode(['error' => 'No token provided']);
        }

        $token = str_replace('Bearer ', '', $headers['Authorization']);

        // Prevent token scanning attacks
        if (strlen($token) > 1024) {
            http_response_code(400);
            return json_encode(['error' => 'Invalid token format']);
        }

        try {
            // Check if token is blacklisted
            if ($this->tokenBlacklist->isBlacklisted($token)) {
                throw new Exception('Token has been revoked');
            }

            $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));
            
            // Validate additional claims
            $this->validateClaims($decoded);
            
            return $decoded;
        } catch (\Firebase\JWT\ExpiredException $e) {
            $this->logSecurityEvent('Token expired', $token);
            http_response_code(401);
            return json_encode(['error' => 'Token expired']);
        } catch (Exception $e) {
            $this->logSecurityEvent('Invalid token: ' . $e->getMessage(), $token);
            http_response_code(401);
            return json_encode(['error' => 'Invalid token']);
        }
    }

    private function isHttps() {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') 
            || $_SERVER['SERVER_PORT'] == 443;
    }

    private function validateClaims($decoded) {
        // Validate issuer
        if ($decoded->iss !== 'your-app-name') {
            throw new Exception('Invalid issuer');
        }

        // Validate audience if needed
        // Add additional custom validations
    }

    private function logSecurityEvent($message, $token) {
        // Implement secure logging
        // Don't log full tokens, only masked versions
        $maskedToken = substr($token, 0, 10) . '...';
        // Log security event
    }
}

class TokenBlacklist {
    public function isBlacklisted($token) {
        // Implement token blacklist check using Redis or database
        return false;
    }
}
?>