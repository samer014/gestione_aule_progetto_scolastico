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

// Verifica se è stato passato il parametro ora
if (!isset($_POST['ora'])) {
    header('Location: orari.php');
    exit();
}

$ora = $_POST['ora'];
$query = "SELECT oraInizio FROM orari WHERE ora = :ora";
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifica Orario</title>
    <link rel="stylesheet" href="../sty/InterfaceIndex.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <h1>PRENOTAZIONI AULE</h1>
    </header>
    
    <div class="main-container">
        <div class="edit-container">
            <h2 class="page-title">
                <i class="fas fa-clock"></i> Modifica Orario
                <span class="admin-badge">Amministratore</span>
            </h2>
            
            <?php
            try {
                $stmt = $con->prepare($query);
                $stmt->bindParam(':ora', $ora, PDO::PARAM_STR);
                $stmt->execute();
                
                if ($stmt->rowCount() === 0) {
                    echo "<p>Orario non trovato</p>";
                } else {
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
            ?>
            
            <form action='aggiornaOrari.php' method='post' class="edit-form">
                <table class="form-table">
                    <thead>
                        <tr>
                            <th>Ora</th>
                            <th>Nuovo Orario di Inizio</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= htmlspecialchars($ora) ?></td>
                            <td>
                                <input type="time" name="oraInizio" value="<?= htmlspecialchars($row['oraInizio']) ?>" 
                                       class="time-input" required step="300"> <!-- step 300 = intervalli di 5 minuti -->
                                <input type="hidden" name="ora" value="<?= htmlspecialchars($ora) ?>">
                            </td>
                        </tr>
                    </tbody>
                </table>
                
                <div class="button-group">
                    <button type="submit" class="confirm-btn">
                        <i class="fas fa-check"></i> Conferma Modifica
                    </button>
                    <a href="orari.php" class="cancel-btn">
                        <i class="fas fa-times"></i> Annulla
                    </a>
                </div>
            </form>
            
            <?php
                }
            } catch(PDOException $ex) {
                echo "<div class='error-message'>Errore nel recupero dell'orario: ".htmlspecialchars($ex->getMessage())."</div>";
            }
            ?>
        </div>
    </div>
    
    <footer>
        <p>Email: <a href="mailto:vrtf03000v@istruzione.it">vrtf03000v@istruzione.it</a> | Tel: <a href="tel:+390458101428">+39 045 810 1428</a></p>
        <p>&copy; 2025 Prenotazioni Aule.</p>
        <p>&copy; Realizzato da: Corrazzini Riccardo Samer, Palumbo Antonio e Tezza Pietro</p>
    </footer>
</body>
</html>