<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../src/middleware/AuthMiddleware.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$auth = new AuthMiddleware();
$db = Database::getInstance()->getConnection();

try {
    $userData = $auth->validateToken();

    switch($method) {
        case 'GET':
            // Get prenotazioni
            $stmt = $db->prepare(
                "SELECT p.*, u.username 
                 FROM prenotazioni p 
                 JOIN utenti u ON p.IdUtente = u.id 
                 WHERE p.IdUtente = ?"
            );
            $stmt->execute([$userData->sub]);
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            break;

        case 'POST':
            // Create prenotazione
            $data = json_decode(file_get_contents('php://input'), true);
            $stmt = $db->prepare(
                "INSERT INTO prenotazioni 
                 (dataPrenotazione, oraInizio, oraFine, aula, IdUtente) 
                 VALUES (NOW(), ?, ?, ?, ?)"
            );
            $stmt->execute([
                $data['oraInizio'],
                $data['oraFine'], 
                $data['aula'],
                $userData->sub
            ]);
            http_response_code(201);
            echo json_encode(['id' => $db->lastInsertId()]);
            break;

        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
