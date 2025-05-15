<?php
session_start();
$user = $_SESSION["username"] ?? "";
if ($user != "adm") {
    header('Location: index.php');
    exit();
}
ini_set('display_errors', 'On');
error_reporting(E_ALL);
include "./db_connect.php";
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Creazione Utente</title>
    <link rel="stylesheet" href="../sty/InterfaceIndex.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <h1>PRENOTAZIONI AULE</h1>
    </header>
    
    <div class="main-container">
        <div class="user-create-container">
            <h2 class="page-title">
                <i class="fas fa-user-plus"></i> Crea Nuovo Utente
                <span class="admin-badge">Amministratore</span>
            </h2>
            
            <form action='inserimentoUtente.php' method='post' class="create-form">
                <div class="form-group">
                    <label for="newUser">Username</label>
                    <input type="text" id="newUser" name="newUser" class="form-input" placeholder="Inserisci username" required>
                </div>
                
                <div class="form-group">
                    <label for="newPass">Password</label>
                    <input type="password" id="newPass" name="newPass" class="form-input" placeholder="Inserisci password" required>
                </div>
                
                <button type="submit" class="submit-btn">
                    <i class="fas fa-save"></i> Crea Utente
                </button>
            </form>
            
            <div style="text-align: center;">
                <a href="index.php" class="home-link">
                    <i class="fas fa-home"></i> Torna alla Home
                </a>
            </div>
        </div>
    </div>
    
    <footer>
        <p>Email: <a href="mailto:vrtf03000v@istruzione.it">vrtf03000v@istruzione.it</a> | Tel: <a href="tel:+390458101428">+39 045 810 1428</a></p>
        <p>&copy; 2025 Prenotazioni Aule.</p>
        <p>&copy; Realizzato da: Corrazzini Riccardo Samer, Palumbo Antonio e Tezza Pietro</p>
    </footer>
</body>
</html>