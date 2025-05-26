<?php
require_once __DIR__ . '/../config/error_handler.php';
require_once __DIR__ . '/middleware/AuthMiddleware.php';

// Inizializza error handler
ErrorHandler::init();

// Headers CORS
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    $auth = new AuthMiddleware();
    $method = $_SERVER['REQUEST_METHOD'];
    $requestUri = $_SERVER['REQUEST_URI'];
    
    // Helper functions
    function getRequestBody() {
        $input = file_get_contents('php://input');
        return json_decode($input, true) ?? [];
    }
    
    function sendResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        echo json_encode([
            'success' => $statusCode < 400,
            'data' => $data,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        exit();
    }
    
    function sendError($message, $statusCode = 400) {
        http_response_code($statusCode);
        echo json_encode([
            'success' => false,
            'error' => $message,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        exit();
    }
    
    // Routing
    switch ($method) {
        case 'POST':
            if (strpos($requestUri, '/generate') !== false) {
                // Genera nuovo token
                $data = getRequestBody();
                
                $userId = $data['user_id'] ?? null;
                $clientId = $data['client_id'] ?? 'web_app';
                $userData = $data['user_data'] ?? [];
                
                if (!$userId) {
                    sendError('user_id è richiesto', 400);
                }
                
                // Qui puoi aggiungere validazione utente (verificare se esiste)
                // $userExists = $auth->validateUser($userId);
                
                $tokenData = $auth->generateToken($userId, $clientId, $userData);
                sendResponse($tokenData, 201);
                
            } elseif (strpos($requestUri, '/validate') !== false) {
                // Valida token corrente
                try {
                    $validation = $auth->validateToken();
                    sendResponse($validation);
                } catch (Exception $e) {
                    sendError($e->getMessage(), 401);
                }
                
            } elseif (strpos($requestUri, '/refresh') !== false) {
                // Refresh token (implementazione futura)
                sendError('Refresh token non ancora implementato', 501);
            }
            break;
            
        case 'DELETE':
            if (strpos($requestUri, '/revoke') !== false) {
                // Revoca token corrente
                try {
                    $revoked = $auth->revokeToken();
                    if ($revoked) {
                        sendResponse(['message' => 'Token revocato con successo']);
                    } else {
                        sendError('Token non trovato', 404);
                    }
                } catch (Exception $e) {
                    sendError($e->getMessage(), 400);
                }
                
            } elseif (strpos($requestUri, '/revoke-all') !== false) {
                // Revoca tutti i token dell'utente
                $data = getRequestBody();
                $userId = $data['user_id'] ?? null;
                
                if (!$userId) {
                    sendError('user_id è richiesto', 400);
                }
                
                $revoked = $auth->revokeAllUserTokens($userId);
                sendResponse(['message' => 'Tutti i token revocati', 'affected' => $revoked]);
            }
            break;
            
        case 'GET':
            if (strpos($requestUri, '/stats') !== false) {
                // Statistiche token
                $userId = $_GET['user_id'] ?? null;
                $stats = $auth->getTokenStats($userId);
                sendResponse($stats);
                
            } elseif (strpos($requestUri, '/check') !== false) {
                // Quick check del token corrente
                try {
                    $validation = $auth->validateToken();
                    sendResponse(['valid' => true, 'user_id' => $validation['user_id']]);
                } catch (Exception $e) {
                    sendResponse(['valid' => false, 'error' => $e->getMessage()]);
                }
            }
            break;
            
        default:
            sendError('Metodo non consentito', 405);
    }
    
} catch (Exception $e) {
    error_log("Token API Error: " . $e->getMessage());
    sendError('Errore interno del server', 500);
}