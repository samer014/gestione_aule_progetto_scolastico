<?php
session_start();
$user = $_SESSION["username"] ?? "";
if ($user === "") {
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
    <title>Le Mie Prenotazioni</title>
    <link rel="stylesheet" href="../sty/InterfaceIndex.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <h1>PRENOTAZIONI AULE</h1>
    </header>
    
    <div class="main-container">
        <div class="bookings-container">
            <h2 class="page-title"><i class="fas fa-list"></i> Le Mie Prenotazioni</h2>
            
            <?php
            $query = "SELECT prenotazioni.oraInizio, prenotazioni.oraFine, prenotazioni.aula, prenotazioni.accettata, 
                             DATE_FORMAT(prenotazioni.dataPrenotazione, '%d/%m/%Y %H:%i') as data_prenotazione
                      FROM prenotazioni 
                      WHERE prenotazioni.IdUtente = (
                          SELECT id 
                          FROM utenti 
                          WHERE username = :user
                      )
                      ORDER BY prenotazioni.oraInizio DESC";
                      
            try {
                $stmt = $con->prepare($query);
                $stmt->bindParam(':user', $user, PDO::PARAM_STR);
                $stmt->execute();
                $num = $stmt->rowCount();
            } catch(PDOException $ex) {
                die("<div class='error-message'>Errore nel recupero delle prenotazioni: ".htmlspecialchars($ex->getMessage())."</div>");
            }
            
            if($num === 0): ?>
                <div class="no-bookings">
                    <i class="fas fa-calendar-times" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                    <p>Nessuna prenotazione effettuata</p>
                </div>
            <?php else: ?>
                <table class="bookings-table">
                    <thead>
                        <tr>
                            <th>Data Prenotazione</th>
                            <th>Giorno</th>
                            <th>Orario</th>
                            <th>Aula</th>
                            <th>Stato</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): 
                            $giorno = explode(" ", $row['oraInizio']);
                            $oraInizio = explode(" ", $row['oraInizio']);
                            $oraFine = explode(" ", $row['oraFine']);
                            
                            if($row['accettata'] === null || $row['accettata'] === ""){
                                $accettata = "In revisione";
                                $statusClass = "status-pending";
                            } elseif($row['accettata'] == 0){
                                $accettata = "Rifiutata";
                                $statusClass = "status-rejected";
                            } else {
                                $accettata = "Accettata";
                                $statusClass = "status-approved";
                            }
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($row['data_prenotazione']) ?></td>
                                <td><?= htmlspecialchars($giorno[0]) ?></td>
                                <td><?= htmlspecialchars(substr($oraInizio[1], 0, 5)) ?> - <?= htmlspecialchars(substr($oraFine[1], 0, 5)) ?></td>
                                <td><?= htmlspecialchars($row['aula']) ?></td>
                                <td class="<?= $statusClass ?>">
                                    <i class="fas <?= 
                                        $statusClass == 'status-pending' ? 'fa-clock' : 
                                        ($statusClass == 'status-approved' ? 'fa-check-circle' : 'fa-times-circle') 
                                    ?>"></i> 
                                    <?= $accettata ?>
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