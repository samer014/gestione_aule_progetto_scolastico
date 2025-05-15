<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleziona Orario</title>
    <link rel="stylesheet" href="../sty/InterfaceIndex.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <h1>PRENOTAZIONI AULE</h1>
    </header>
    
    <div class="main-container">
        <div class="booking-container">
            <?php
            include "./db_connect.php";
            session_start();
            $user = $_SESSION["username"] ?? "";
            if ($user == "") {
                header('Location: index.php');
                exit();
            }
            
            // Recupera ID utente
            $qualeId = "SELECT id FROM utenti WHERE username = :username";
            try {
                $stmt = $con->prepare($qualeId);
                $stmt->bindParam(':username', $user, PDO::PARAM_STR);
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $idUtente = $row['id'];
            } catch(PDOException $ex) {
                die("Errore nel recupero dell'utente");
            }
            
            // 1) Recupero data da POST
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['day'], $_POST['month'], $_POST['year'])) {
                $day   = str_pad($_POST['day'], 2, "0", STR_PAD_LEFT);
                $month = str_pad($_POST['month'], 2, "0", STR_PAD_LEFT);
                $year  = $_POST['year'];
            } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['day'], $_POST['month'], $_POST['year'])) {
                $day   = str_pad($_POST['day'], 2, "0", STR_PAD_LEFT);
                $month = str_pad($_POST['month'], 2, "0", STR_PAD_LEFT);
                $year  = $_POST['year'];
            } else {
                die("<div class='error-message'>Parametri di data non validi</div>");
            }
            
            // Mappatura giorno
            $mapG = ['Mon'=>'LUN','Tue'=>'MAR','Wed'=>'MER','Thu'=>'GIO','Fri'=>'VEN'];
            $dShort = date('D', mktime(0,0,0,$month,$day,$year));
            if (!isset($mapG[$dShort])) die("<div class='error-message'>Non è un giorno prenotabile</div>");
            $giornoEnum = $mapG[$dShort];
            
            // Carica orari disponibili
            $orari = $con->query("SELECT oraInizio FROM orari ORDER BY ora")->fetchAll(PDO::FETCH_COLUMN);
            ?>
            
            <h2 class="page-title">Seleziona Orario</h2>
            <div class="date-display"><?= "$day/$month/$year" ?></div>
            
            <?php
            // 2) Se cliccato "Trova Aule"
            if (isset($_POST['conferma'])) {
                $orario_inizio = $_POST['orario_inizio'];
                $orario_fine   = $_POST['orario_fine'];
                
                // Validazione orari
                $dtIn = DateTime::createFromFormat('H:i', $orario_inizio);
                $dtFi = DateTime::createFromFormat('H:i', $orario_fine);
                if (!$dtIn || !$dtFi || $dtFi <= $dtIn) {
                    echo "<div class='error-message'>Errore: l'orario di fine deve essere successivo all'inizio.</div>";
                } else {
                    // Recupera slot orari
                    $q = "SELECT ora FROM orari WHERE oraInizio = ?";
                    $s = $con->prepare($q);
                    $s->execute([$orario_inizio]);
                    $i1 = $s->fetchColumn();
                    $s->execute([$orario_fine]);
                    $i2 = $s->fetchColumn();
                    
                    if (!$i1 || !$i2) {
                        die("<div class='error-message'>Slot non trovato in tabella orari.</div>");
                    }
                    
                    // Crea array di slot
                    $slots_int = range($i1, $i2 - 1);
                    $slots_str = array_map(fn($n) => str_pad($n, 2, '0', STR_PAD_LEFT), $slots_int);
                    
                    // Query per aule disponibili
                    $ph = implode(',', array_fill(0, count($slots_str), '?'));
                    $sql = "SELECT aula FROM aule_libere WHERE giorno = ? AND ora IN ($ph) GROUP BY aula HAVING COUNT(*) = ?";
                    $stmt = $con->prepare($sql);
                    $params = array_merge([$giornoEnum], $slots_str, [count($slots_str)]);
                    $stmt->execute($params);
                    
                    echo "<h3>Aule libere dalle $orario_inizio alle $orario_fine</h3>";
                    
                    if ($stmt->rowCount() === 0) {
                        echo "<p>Nessuna aula libera in questo orario</p>";
                    } else {
                        echo "<table class='classrooms-table'>
                                <tr><th>Aula</th><th>Azioni</th></tr>";
                        
                        while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $aula = $r['aula'];
                            // Verifica sovrapposizioni
                            $inizio = "$year-$month-$day $orario_inizio:00";
                            $fine   = "$year-$month-$day $orario_fine:00";
                            $q2 = "SELECT 1 FROM prenotazioni WHERE aula = ? AND (oraInizio < ? AND oraFine > ?)";
                            $s2 = $con->prepare($q2);
                            $s2->execute([$aula, $fine, $inizio]);
                            
                            if ($s2->rowCount() === 0) {
                                echo "<tr>
                                        <td>$aula</td>
                                        <td>
                                          <form method='post'>
                                            <input type='hidden' name='day' value='$day'>
                                            <input type='hidden' name='month' value='$month'>
                                            <input type='hidden' name='year' value='$year'>
                                            <input type='hidden' name='orario_inizio' value='$orario_inizio'>
                                            <input type='hidden' name='orario_fine' value='$orario_fine'>
                                            <input type='hidden' name='aula' value='$aula'>
                                            <button type='submit' name='seleziona_aula' class='select-btn'>Prenota</button>
                                          </form>
                                        </td>
                                      </tr>";
                            }
                        }
                        echo "</table>";
                    }
                }
            }
            
            // 3) Se prenotazione confermata
            if (isset($_POST['seleziona_aula'])) {
                $aula = $_POST['aula'];
                $i    = $_POST['orario_inizio'];
                $f    = $_POST['orario_fine'];
                $inizio = "$year-$month-$day $i:00";
                $fine   = "$year-$month-$day $f:00";
                $now    = date('Y-m-d H:i:s');
                
                $ins = "INSERT INTO prenotazioni (dataPrenotazione, accettata, dataEsito, oraInizio, oraFine, aula, IdUtente, IdAmministratore)
                        VALUES (?, NULL, NULL, ?, ?, ?, ?, NULL)";
                $st = $con->prepare($ins);
                $st->execute([$now, $inizio, $fine, $aula, $idUtente]);
                
                echo "<div class='success-message'>
                        <h3>Prenotazione confermata!</h3>
                        <p><strong>Aula:</strong> $aula<br>
                           <strong>Orario:</strong> $i - $f<br>
                           <strong>Data:</strong> $day/$month/$year</p>
                      </div>";
                
                echo "<a href='prenotazioni.php' class='back-link'>
                        <i class='fas fa-list'></i> Vai alle tue prenotazioni
                      </a>";
                exit;
            }
            ?>
            
            <form method="post">
                <input type="hidden" name="day" value="<?= $day ?>">
                <input type="hidden" name="month" value="<?= $month ?>">
                <input type="hidden" name="year" value="<?= $year ?>">
                
                <div class="time-selector">
                    <div class="time-group">
                        <label for="orario_inizio">Da:</label>
                        <select name="orario_inizio" id="orario_inizio" class="time-select">
                            <?php foreach ($orari as $h): ?>
                                <option value="<?= $h ?>" <?= (!empty($orario_inizio) && $orario_inizio === $h ? 'selected' : '') ?>>
                                    <?= $h ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="time-group">
                        <label for="orario_fine">A:</label>
                        <select name="orario_fine" id="orario_fine" class="time-select">
                            <?php foreach ($orari as $h): ?>
                                <option value="<?= $h ?>" <?= (!empty($orario_fine) && $orario_fine === $h ? 'selected' : '') ?>>
                                    <?= $h ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div style="text-align: center;">
                    <button type="submit" name="conferma" class="submit-btn">
                        <i class="fas fa-search"></i> Trova Aule
                    </button>
                </div>
            </form>
            
            <a href="calendario.php" class="back-link">
                <i class="fas fa-arrow-left"></i> Torna al calendario
            </a>
        </div>
    </div>
    
    <footer>
        <p>Email: <a href="mailto:vrtf03000v@istruzione.it">vrtf03000v@istruzione.it</a> | Tel: <a href="tel:+390458101428">+39 045 810 1428</a></p>
        <p>&copy; 2025 Prenotazioni Aule.</p>
        <p>&copy; Realizzato da: Corrazzini Riccardo Samer, Palumbo Antonio e Tezza Pietro</p>
    </footer>
</body>
</html>