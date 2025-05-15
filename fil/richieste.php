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
    <title>Gestione Richieste</title>
    <link rel="stylesheet" href="../sty/InterfaceIndex.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <h1>PRENOTAZIONI AULE</h1>
    </header>
    
    <div class="main-container">
        <div class="requests-container">
            <h2 class="page-title">
                <i class="fas fa-inbox"></i> Gestione Richieste
                <span class="admin-badge">Amministratore</span>
            </h2>
            
            <?php
            $query = "SELECT prenotazioni.id, prenotazioni.dataPrenotazione, 
                             DATE_FORMAT(prenotazioni.oraInizio, '%Y-%m-%d') as giorno,
                             DATE_FORMAT(prenotazioni.oraInizio, '%H:%i') as inizio,
                             DATE_FORMAT(prenotazioni.oraFine, '%H:%i') as fine,
                             prenotazioni.aula, utenti.username 
                      FROM prenotazioni 
                      JOIN utenti ON utenti.id = prenotazioni.IdUtente
                      WHERE prenotazioni.accettata IS NULL
                      ORDER BY prenotazioni.oraInizio";
            
            try {
                $stmt = $con->prepare($query);
                $stmt->execute();
                $num = $stmt->rowCount();
            } catch(PDOException $ex) {
                die("<div class='error-message'>Errore nel recupero delle richieste: ".htmlspecialchars($ex->getMessage())."</div>");
            }
            
            if($num == 0): ?>
                <div class="no-requests">
                    <i class="fas fa-check-circle" style="font-size: 2rem; color: #4CAF50; margin-bottom: 1rem;"></i>
                    <p>Nessuna richiesta in attesa di approvazione</p>
                </div>
            <?php else: ?>
                <table class="requests-table">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Orario</th>
                            <th>Aula</th>
                            <th>Utente</th>
                            <th>Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td class="date-time"><?= htmlspecialchars($row['giorno']) ?></td>
                                <td class="date-time"><?= htmlspecialchars($row['inizio']) ?> - <?= htmlspecialchars($row['fine']) ?></td>
                                <td><?= htmlspecialchars($row['aula']) ?></td>
                                <td><?= htmlspecialchars($row['username']) ?></td>
                                <td>
                                    <form action='accettaPrenotazione.php' method='post' class="action-form">
                                        <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']) ?>">
                                        <button type="submit" class="accept-btn">
                                            <i class="fas fa-check"></i> Accetta
                                        </button>
                                    </form>
                                    <form action='rifiutaPrenotazione.php' method='post' class="action-form">
                                        <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']) ?>">
                                        <button type="submit" class="reject-btn">
                                            <i class="fas fa-times"></i> Rifiuta
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php endif; ?>
            
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