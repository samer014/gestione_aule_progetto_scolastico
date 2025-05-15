<?php
require_once __DIR__ . '/../../vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware {
    private $secretKey;
    private $algorithm = 'HS256';
    private $cache;
    
    public function __construct() {
        $this->secretKey = getenv('JWT_SECRET_KEY');
        if (!$this->secretKey) {
            throw new Exception('JWT_SECRET_KEY non configurata');
        }
        $this->cache = new RedisCacheAdapter();
    }
    
    public function validateToken() {
        $headers = apache_request_headers();
        
        if (!isset($headers['Authorization'])) {
            throw new Exception('Token non presente', 401);
        }

        $token = str_replace('Bearer ', '', $headers['Authorization']);
        
        try {
            $decoded = JWT::decode(
                $token, 
                new Key($this->secretKey, $this->algorithm)
            );
            
            // Verifica se il token è nella blacklist
            if ($this->isTokenBlacklisted($decoded->jti)) {
                throw new Exception('Token revocato', 401);
            }

            // Verifica rate limiting
            $this->checkRateLimit($decoded->sub);
            
            return $decoded;
            
        } catch (\Firebase\JWT\ExpiredException $e) {
            throw new Exception('Token scaduto', 401);
        } catch (Exception $e) {
            throw new Exception('Token non valido', 401);
        }
    }
    
    private function isTokenBlacklisted($jti) {
        global $con;
        $stmt = $con->prepare(
            "SELECT 1 FROM token_blacklist 
             WHERE jti = ? AND expiry > NOW()"
        );
        $stmt->execute([$jti]);
        return $stmt->rowCount() > 0;
    }

    private function checkRateLimit($userId) {
        $key = "rate_limit:$userId";
        $count = $this->cache->increment($key);
        
        if ($count === 1) {
            $this->cache->expire($key, 60); // 1 minuto
        }
        
        if ($count > 100) { // 100 richieste/minuto
            throw new Exception('Troppe richieste', 429);
        }
    }
}
?>
