<?php
session_start();
include 'db_connect.php'; // $con deve essere definito qui
require_once __DIR__ . '/../API/auth/token.php';

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
    // Recupera l'utente dal DB
    $stmt = $con->prepare(
        "SELECT id, username, password, amministratore 
         FROM utenti 
         WHERE username = :user"
    );
    $stmt->execute([':user' => $user]);
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verifica password
    if ($userData && $pass === $userData['password']) { // Usa hash se necessario
        $controller = new TokenController($con);
        $result = json_decode($controller->generateToken($userData['username'], $pass), true);

        if (isset($result['access_token'])) {
            $_SESSION['token'] = $result['access_token'];
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