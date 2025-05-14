<?php
require_once 'vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware {
    private $secretKey;

    public function __construct() {
        $this->secretKey = getenv('JWT_SECRET_KEY');
    }

    public function validateToken() {
        $headers = apache_request_headers();
        
        if (!isset($headers['Authorization'])) {
            http_response_code(401);
            return json_encode(['error' => 'No token provided']);
        }

        $token = str_replace('Bearer ', '', $headers['Authorization']);

        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));
            return $decoded;
        } catch (\Firebase\JWT\ExpiredException $e) {
            http_response_code(401);
            return json_encode(['error' => 'Token expired']);
        } catch (Exception $e) {
            http_response_code(401);
            return json_encode(['error' => 'Invalid token']);
        }
    }
}
?>