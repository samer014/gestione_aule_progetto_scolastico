<?php
session_start();
require_once __DIR__ . '/../API/auth/token.php';
include 'db_connect.php';

// Aggiungere CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    header('Location: loginErrato.php');
    exit();
}

$user = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
$pass = $_POST['password'];

if (empty($user) || empty($pass)) {
    header('Location: loginErrato.php');
    exit();
}

try {
    $stmt = $con->prepare(
        "SELECT id, username, amministratore 
         FROM utenti 
         WHERE username = :user 
         AND password = :pass"
    );
    $stmt->execute([
        ':user' => $user,
        ':pass' => password_hash($pass, PASSWORD_DEFAULT)
    ]);
    
    if ($stmt->rowCount() > 0) {
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        $tokenController = new TokenController();
        $result = $tokenController->generateToken($userData['id']);
        
        if (isset($result['access_token'])) {
            $_SESSION['token'] = $result['access_token'];
            $_SESSION['refresh_token'] = $result['refresh_token'];
            $_SESSION['username'] = $userData['username'];
            header('Location: index.php');
            exit();
        }
    }
    header('Location: loginErrato.php');
    exit();
} catch(PDOException $ex) {
    error_log("Login error: " . $ex->getMessage());
    header('Location: loginErrato.php');
    exit();
}
?>