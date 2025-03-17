
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Calendario Settimanale</title>
</head>
<body>
    <form method="post" action="">
        <label for="orario">Seleziona un orario scolastico:</label>
        <select id="orario" name="orario">
            <option value="8:00-8:50">8:00-8:50</option>
            <option value="8:50-9:40">8:50-9:40</option>
            <option value="9:50-10:50">9:50-10:50</option>
            <option value="10:50-11:45">10:50-11:45</option>
            <option value="12:00-12:50">12:00-12:50</option>
            <option value="12:50-13:40">12:50-13:40</option>
            <option value="13:40-14:30">13:40-14:30</option>
        </select>
        <button type="submit" name="conferma">Conferma</button>
    </form>

    <?php
    if (isset($_POST['conferma'])) {
        $orario = $_POST['orario'];
        $auleDisponibili = getAuleDisponibili($orario);

        echo "<h2>Aule disponibili per l'orario $orario:</h2>";
        echo "<ul>";
        foreach ($auleDisponibili as $aula) {
            echo "<li>$aula</li>";
        }
        echo "</ul>";
    }

    function getAuleDisponibili($orario) {
        return ["Aula 1", "Aula 2", "Aula 3"];
    }
    ?>
</body>
</html>