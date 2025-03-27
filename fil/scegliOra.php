<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Calendario Settimanale</title>
</head>
<body>

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
                    "<form action='modificaOrari.php' method='post'>".
                        "<a href='modificaOrari.php'><button class='fixed-button' value=".$row['ora']." name='ora'>Modifica</button></a>".
                    "</form>".
                "</td>".
                "</tr>\n");
        }
        echo "</table> <br><br>";
        print("<a href='index.php'> Home </a>");
    }
    ?>

</body>
</html>
