<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../API/middleware/AuthMiddleware.php';

class PrenotazioniController {
    private $db;
    private $auth;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->auth = new AuthMiddleware();
    }

    private function validatePrenotazione($data) {
        if (empty($data['oraInizio']) || empty($data['oraFine']) || empty($data['aula'])) {
            throw new Exception('Dati mancanti', 400);
        }

        // Validazione formato date
        $inizio = DateTime::createFromFormat('Y-m-d H:i:s', $data['oraInizio']);
        $fine = DateTime::createFromFormat('Y-m-d H:i:s', $data['oraFine']);
        
        if (!$inizio || !$fine || $fine <= $inizio) {
            throw new Exception('Date non valide', 400);
        }

        // Verifica sovrapposizioni
        $stmt = $this->db->prepare(
            "SELECT 1 FROM prenotazioni 
             WHERE aula = ? AND 
             ((oraInizio BETWEEN ? AND ?) OR 
              (oraFine BETWEEN ? AND ?))"
        );
        $stmt->execute([$data['aula'], $data['oraInizio'], $data['oraFine'], 
                       $data['oraInizio'], $data['oraFine']]);
        
        if ($stmt->rowCount() > 0) {
            throw new Exception('Sovrapposizione prenotazioni', 409);
        }
    }
}

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Authorization, Content-Type');

$method = $_SERVER['REQUEST_METHOD'];
$controller = new PrenotazioniController();

try {
    $userData = $controller->auth->validateToken();

    switch($method) {
        case 'GET':
            // Get prenotazioni
            $stmt = $controller->db->prepare(
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
            $controller->validatePrenotazione($data);
            $stmt = $controller->db->prepare(
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
            echo json_encode(['id' => $controller->db->lastInsertId()]);
            break;

        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
    }

} catch (Exception $e) {
    http_response_code($e->getCode());
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}
