<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Calendario Settimanale</title>
</head>
<body>

    <?php
    $day;
    $month;
    $year;
    
    // Ricava da calendario.php il giorno, mese e anno della prenotazione
    if (isset($_GET['day'], $_GET['month'], $_GET['year'])) {
        $day = $_GET['day'];
        $month = $_GET['month'];
        $year = $_GET['year'];

        $day = str_pad($day, 2, "0", STR_PAD_LEFT);
        $month = str_pad($month, 2, "0", STR_PAD_LEFT);

        print($day . $month . $year);
    }
    ?>

    <form method="post" action="">
        <label for="orario_inizio">Seleziona un orario scolastico da:</label>
        <select id="orario_inizio" name="orario_inizio">
            <?php
            include "./db_connect.php";

            $query = "SELECT oraInizio FROM orari"; // Recupera tutti gli orari dal db
            try {
                $stmt = $con->prepare($query);
                $stmt->execute();
            } catch (PDOException $ex) {
                die("Errore: " . $ex->getMessage());
            }

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<option value='" . htmlspecialchars($row['oraInizio']) . "'>" . htmlspecialchars($row['oraInizio']) . "</option>"; // lista a discesa che sceglie orari inizio
            }
            ?>
        </select>

        <label for="orario_fine">a:</label>
        <select id="orario_fine" name="orario_fine">
            <?php
            $stmt->execute(); // Ri-esegui la query per il secondo select

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<option value='" . htmlspecialchars($row['oraInizio']) . "'>" . htmlspecialchars($row['oraInizio']) . "</option>"; // lista a discesa che sceglie orari fine
            }
            ?>
        </select>

        <button type="submit" name="conferma">Conferma</button>
    </form>

    <?php
    if (isset($_POST['conferma'])) { // quando clicco conferma...
        $orario_inizio = $_POST['orario_inizio'];
        $orario_fine = $_POST['orario_fine'];
        print($orario_inizio . "-" . $orario_fine);

        // controllo che orario inizio sia minore di quello di fine 
        $orario_inizio_dt = DateTime::createFromFormat('H:i', $orario_inizio);
        $orario_fine_dt = DateTime::createFromFormat('H:i', $orario_fine);

        if ($orario_inizio_dt && $orario_fine_dt && $orario_fine_dt > $orario_inizio_dt) {
            // trovo le aule disponibili dell'orario scelto
            getAuleDisponibili($orario_inizio, $orario_fine, $year, $month, $day);
        } else {
            echo "Errore: l'orario di fine deve essere maggiore dell'orario di inizio.";
        }
    }

    // Funzione per ottenere le aule disponibili
    function getAuleDisponibili($orario_inizio, $orario_fine, $year, $month, $day) {
        include "./db_connect.php";

        // Recuperiamo tutte le aule dalla tabella delle aule libere
        $query = "SELECT aula FROM aule_libere";
        try {
            $stmt = $con->prepare($query);
            $stmt->execute();

            // Tabella HTML per visualizzare le aule disponibili
            print("<table border='1'>\n".
                "<tr>\n".
                "<th>Aula</th>\n".
                "<th>Prenota</th>\n".
                "</tr>\n");

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // Controlliamo se l'aula è libera per l'orario selezionato
                $aula = $row['aula'];
                $disponibile = aLibera($aula, $year, $month, $day, $orario_inizio, $orario_fine);

                if ($disponibile) {
                    print("<tr>".
                        "<td>" . htmlspecialchars($aula) . "</td>\n".
                        "<td>".
                            "<form method='post'>".
                                "<input type='hidden' name='orario_inizio' value='".htmlspecialchars($orario_inizio)."'>".
                                "<input type='hidden' name='orario_fine' value='".htmlspecialchars($orario_fine)."'>".
                                "<input type='hidden' name='aula' value='".htmlspecialchars($aula)."'>".
                                "<button class='fixed-button' value='".htmlspecialchars($aula)."' name='seleziona_aula'>Seleziona</button>".
                            "</form>".
                        "</td>".
                    "</tr>\n");
                }
            }

            echo "</table><br><br>";
            print("<a href='index.php'>Home</a>");
        } catch(PDOException $ex) {
            print("Errore: " . $ex->getMessage());
            exit;
        }
    }

    // Funzione che verifica se l'aula è libera
    function aLibera($aula, $year, $month, $day, $orario_inizio, $orario_fine) {
        include "./db_connect.php";

        // Mappa dei giorni
        $giorni = [
            'Monday'    => 'LUN',
            'Tuesday'   => 'MAR',
            'Wednesday' => 'MER',
            'Thursday'  => 'GIO',
            'Friday'    => 'VEN',
            'Saturday'  => 'SAB',
            'Sunday'    => 'DOM'
        ];

        // Calcolare il giorno della settimana
        $date = new DateTime("$year-$month-$day");
        $gSettEng = $date->format('l');  // Ottenere il giorno in inglese
        $gSett = $giorni[$gSettEng]; // Tradurre in italiano

        // Mappa degli orari
        $ore = [
            "08:50" => 1,
            "09:40" => 2,
            "10:50" => 3,
            "11:45" => 4,
            "12:50" => 5,
            "13:40" => 6,
            "14:30" => 7
        ];

        // Calcolare l'ora corrispondente all'orario di fine
        $ora = $ore[$orario_fine];

        // Creiamo le stringhe complete per orario_inizio e orario_fine
        $orario_inizio_completo = "$year-$month-$day $orario_inizio:00";
        $orario_fine_completo = "$year-$month-$day $orario_fine:00";

        // Verifica se l'aula è disponibile
        $query = "SELECT * FROM prenotazioni WHERE oraInizio = :orario_inizio AND oraFine = :orario_fine AND aula = :aula";
        try {
            $stmt = $con->prepare($query);
            $stmt->execute([
                ':orario_inizio' => $orario_inizio_completo,
                ':orario_fine' => $orario_fine_completo,
                ':aula' => $aula
            ]);

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($row) {
                return false; // Aula non disponibile
            } else {
                return true;  // Aula disponibile
            }
        } catch(PDOException $ex) {
            print("Errore: " . $ex->getMessage());
            exit;
        }
    }

    // Funzione per selezionare un'aula e inserire la prenotazione
    if (isset($_POST['seleziona_aula'])) {
        $orario_inizio = $_POST['orario_inizio'];
        $orario_fine = $_POST['orario_fine'];
        $aula_selezionata = $_POST['aula']; // Aula selezionata
        $idUtente = 1; // Sostituisci con l'ID dell'utente attualmente autenticato

        // Inserire la prenotazione nel database
        inserisciPrenotazione($orario_inizio, $orario_fine, $aula_selezionata, $idUtente, $year, $month, $day);

        // Mostra un messaggio di conferma
        echo "<h2>La prenotazione è stata completata con successo!</h2>";
        echo "<p>Orario di inizio: $orario_inizio</p>";
        echo "<p>Orario di fine: $orario_fine</p>";
        echo "<p>Aula: $aula_selezionata</p>";
        echo "<p><a href='index.php'>Torna alla home</a></p>";
    }

    // Funzione per inserire una prenotazione nel database
    function inserisciPrenotazione($orario_inizio, $orario_fine, $aula, $idUtente, $year, $month, $day) {
        include "./db_connect.php";

        // Creare la data di inizio e di fine
        $orario_inizio_completo = "$year-$month-$day $orario_inizio:00";
        $orario_fine_completo = "$year-$month-$day $orario_fine:00";

        // Data della prenotazione (adesso)
        $dataPrenotazione = date('Y-m-d H:i:s'); // Data e ora attuali

        // Query per inserire la prenotazione
        $query = "INSERT INTO prenotazioni (dataPrenotazione, oraInizio, oraFine, aula, idUtente) 
                  VALUES (:dataPrenotazione, :oraInizio, :oraFine, :aula, :idUtente)";
        
        try {
            $stmt = $con->prepare($query);
            $stmt->execute([
                ':dataPrenotazione' => $dataPrenotazione,
                ':oraInizio' => $orario_inizio_completo,
                ':oraFine' => $orario_fine_completo,
                ':aula' => $aula,
                ':idUtente' => $idUtente
            ]);
        } catch(PDOException $ex) {
            print("Errore: " . $ex->getMessage());
            exit;
        }
    }
    ?>

</body>
</html>
