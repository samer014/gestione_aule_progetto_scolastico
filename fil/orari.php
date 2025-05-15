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
    <title>Gestione Orari</title>
    <link rel="stylesheet" href="../sty/InterfaceIndex.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <h1>PRENOTAZIONI AULE</h1>
    </header>
    
    <div class="main-container">
        <div class="schedule-container">
            <h2 class="page-title">
                <i class="fas fa-clock"></i> Gestione Orari
                <span class="admin-badge">Amministratore</span>
            </h2>
            
            <?php
            $query = "SELECT * FROM orari ORDER BY ora";
            try {
                $stmt = $con->prepare($query);
                $stmt->execute();
            } catch(PDOException $ex) {
                die("<div class='error-message'>Errore nel recupero degli orari: ".htmlspecialchars($ex->getMessage())."</div>");
            }
            ?>
            
            <table class="schedule-table">
                <thead>
                    <tr>
                        <th>Numero Ora</th>
                        <th>Orario di Inizio</th>
                        <th>Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['ora']) ?></td>
                            <td><?= htmlspecialchars($row['oraInizio']) ?></td>
                            <td>
                                <form action='modificaOrari.php' method='post'>
                                    <input type="hidden" name="ora" value="<?= htmlspecialchars($row['ora']) ?>">
                                    <button type="submit" class="edit-btn">
                                        <i class="fas fa-edit"></i> Modifica
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            
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