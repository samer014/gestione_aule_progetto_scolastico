<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Calendario Settimanale</title>
</head>
<body>

    <?php
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
                echo "<option value='" . htmlspecialchars($row['oraInizio']) . "'>" . htmlspecialchars($row['oraInizio']) . "</option>";
            }
            ?>
        </select>

        <label for="orario_fine">a:</label>
        <select id="orario_fine" name="orario_fine">
            <?php
            $stmt->execute(); // Ri-esegui la query per il secondo select

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<option value='" . htmlspecialchars($row['oraInizio']) . "'>" . htmlspecialchars($row['oraInizio']) . "</option>";
            }
            ?>
        </select>

        <button type="submit" name="conferma">Conferma</button>
    </form>

    <?php
    if (isset($_POST['conferma'])) {
        $orario_inizio = $_POST['orario_inizio'];
        print($orario_inizio);
        $orario_fine = $_POST['orario_fine'];

        // conversione in datetime per verificare che l'orario fine sia minore di orario inizo
        $orario_inizio_dt = DateTime::createFromFormat('H:i', $orario_inizio);
        $orario_fine_dt = DateTime::createFromFormat('H:i', $orario_fine);

        if ($orario_inizio_dt && $orario_fine_dt && $orario_fine_dt > $orario_inizio_dt) {
            $auleDisponibili = getAuleDisponibili($orario_inizio, $orario_fine);
        } else {
            echo "Errore: l'orario di fine deve essere maggiore dell'orario di inizio.";
        }
        // echo "<h2>Aule disponibili per l'orario da $orario_inizio a $orario_fine:</h2>";
        // echo "<form method='post' action=''>";
        // echo "<label for='giorno'>Seleziona il giorno:</label>";
        // echo "<input type='date' id='giorno' name='giorno' required>";
        // echo "<button type='submit' name='conferma_giorno'>Conferma Giorno</button>";
        // echo "</form>";

        // if (isset($_POST['conferma_giorno'])) {
        //     $giorno = $_POST['giorno'];
        //     echo "<h3>Giorno selezionato: $giorno</h3>";
        // }
        /*echo "<h2>Aule disponibili per l'orario da $orario_inizio a $orario_fine:</h2>";
        echo "<ul>";
        foreach ($auleDisponibili as $aula) {
        $orario_inizio_dt = DateTime::createFromFormat('H:i', $orario_inizio);
        $orario_fine_dt = DateTime::createFromFormat('H:i', $orario_fine);

        if ($orario_inizio_dt && $orario_fine_dt && $orario_fine_dt > $orario_inizio_dt) {
        echo "</ul>";*/
    }

    function getAuleDisponibili($orario_inizio, $orario_fine) {
        include "./db_connect.php";
        // Simuliamo il recupero delle aule disponibili
        $query = "SELECT * FROM orari"; // tutti gli orari dal db
        try {
            $stmt = $con->prepare($query);
            $stmt->execute();
        } catch(PDOException $ex) {
            print("Errore !".$ex->getMessage());
            exit;
        }
        print("<table border='1'>\n".
            "<tr>\n".
            "<th>aula</th>\n".
            "<th>prenota</th>\n".
            "</tr>\n"); 
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){

            $query = "SELECT aula FROM prenotazioni WHERE oraInizio = '$orario_inizio' AND oraFine = '$orario_fine';"; // tutti gli orari dal db
            try {
                $stmt = $con->prepare($query);
                $stmt->execute();
            } catch(PDOException $ex) {
                print("Errore !".$ex->getMessage());
                exit;
            }

            print("<tr>".
                "<td>".$row['ora']."</td>\n".
                "<td>".$row['oraInizio']."</td>\n".
                "<td>".
                    "<form method='post'>".
                        "<input type='hidden' name='orario_inizio' value='".htmlspecialchars($orario_inizio)."'>".
                        "<input type='hidden' name='orario_fine' value='".htmlspecialchars($orario_fine)."'>".
                        "<button class='fixed-button' value='".htmlspecialchars($row['ora'])."' name='seleziona_aula'>Seleziona</button>".
                    "</form>".
                "</td>".
                "</tr>\n");
        }
        echo "</table> <br><br>";
        print("<a href='index.php'> Home </a>");
    }
    ?>

    <?php
        function selezionaAula($orario_inizio, $orario_fine) {
            include "./db_connect.php";
            $query = "SELECT aula FROM prenotazioni WHERE oraInizio = :orario_inizio AND oraFine = :orario_fine;";
            try {
            $stmt = $con->prepare($query);
            $stmt->bindParam(':orario_inizio', $orario_inizio);
            $stmt->bindParam(':orario_fine', $orario_fine);
            $stmt->execute();
            $aule = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $aule;
            } catch (PDOException $ex) {
            print("Errore: " . $ex->getMessage());
            exit;
            }
        }

        if (isset($_POST['seleziona_aula'])) {
            $orario_inizio = $_POST['orario_inizio'];
            $orario_fine = $_POST['orario_fine'];
            $auleDisponibili = selezionaAula($orario_inizio, $orario_fine);

            echo "<h2>Aule disponibili:</h2>";
            echo "<ul>";
            foreach ($auleDisponibili as $aula) {
            echo "<li>" . htmlspecialchars($aula['aula']) . "</li>";
            }
            echo "</ul>";
        }
		print("<a href='index.php'> Home </a>");
    ?>

</body>
</html>