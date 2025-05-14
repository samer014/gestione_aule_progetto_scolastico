<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Seleziona Orario</title>
</head>
<body>


<?php
	include "./db_connect.php";
	session_start();
	$user=$_SESSION["username"]??"";
	$user=$_SESSION["username"]??"";
	if ($user == ""){
		header('Location: index.php');
		exit();
	}
	$qualeId = "SELECT id FROM utenti WHERE username = :username;";
	try {
		$stmt = $con->prepare( $qualeId );
		$stmt->bindParam(':username', $user, PDO::PARAM_STR);
		$stmt->execute();
	} catch(PDOException $ex) {
		print($ex);
		exit();
	}
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$idUtente = $row['id'];
	ini_set('display_errors', 'On');
	error_reporting(E_ALL);
?>
<?php
// 1) Recupero data da GET (clic sul calendario) o da POST (submit del form)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['day'], $_GET['month'], $_GET['year'])) {
    $day   = str_pad($_GET['day'],   2, "0", STR_PAD_LEFT);
    $month = str_pad($_GET['month'], 2, "0", STR_PAD_LEFT);
    $year  = $_GET['year'];
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['day'], $_POST['month'], $_POST['year'])) {
    $day   = str_pad($_POST['day'],   2, "0", STR_PAD_LEFT);
    $month = str_pad($_POST['month'], 2, "0", STR_PAD_LEFT);
    $year  = $_POST['year'];
} else {
    die("Parametri di data non validi");
}

// Mappatura PHP day→enum aule_libere
$mapG = ['Mon'=>'LUN','Tue'=>'MAR','Wed'=>'MER','Thu'=>'GIO','Fri'=>'VEN'];
$dShort = date('D', mktime(0,0,0,$month,$day,$year));
if (!isset($mapG[$dShort])) die("Non è un giorno prenotabile");
$giornoEnum = $mapG[$dShort];

// Connessione al database
include "./db_connect.php";

// Carico array di tutti gli orari
$orari = $con->query("SELECT oraInizio FROM orari ORDER BY ora")->fetchAll(PDO::FETCH_COLUMN);

// 2) Se ho cliccato “Trova Aule”
if (isset($_POST['conferma'])) {
    $orario_inizio = $_POST['orario_inizio'];
    $orario_fine   = $_POST['orario_fine'];

    // Validazione orari
    $dtIn = DateTime::createFromFormat('H:i', $orario_inizio);
    $dtFi = DateTime::createFromFormat('H:i', $orario_fine);
    if (!$dtIn || !$dtFi || $dtFi <= $dtIn) {
        echo "<p style='color:red'>Errore: l'orario di fine deve essere successivo all'inizio.</p>";
    } else {
        // 2.1) Recupero gli indici interi degli slot
        $q = "SELECT ora FROM orari WHERE oraInizio = ?";
        $s = $con->prepare($q);
        $s->execute([$orario_inizio]);
        $i1 = $s->fetchColumn();
        $s->execute([$orario_fine]);
        $i2 = $s->fetchColumn();
        if (!$i1 || !$i2) {
            die("Slot non trovato in tabella orari.");
        }

        // 2.2) Creo array di slot interi e poi li pado a due cifre
        $slots_int = range($i1, $i2 - 1);
        $slots_str = array_map(fn($n) => str_pad($n, 2, '0', STR_PAD_LEFT), $slots_int);

        // 2.3) Preparo placeholders "?, ?, ..."
        $ph = implode(',', array_fill(0, count($slots_str), '?'));

        // 2.4) Query per le aule che compaiono in tutti gli slot
        $sql = "SELECT aula
                FROM aule_libere
                WHERE giorno = ?
                  AND ora IN ($ph)
                GROUP BY aula
                HAVING COUNT(*) = ?";
        $stmt = $con->prepare($sql);
        $params = array_merge([$giornoEnum], $slots_str, [count($slots_str)]);
        $stmt->execute($params);

        echo "<h2>Aule libere il $day/$month/$year dalle $orario_inizio alle $orario_fine</h2>";
        echo "<table border='1'>
                <tr><th>Aula</th><th>Prenota</th></tr>";

        if ($stmt->rowCount() === 0) {
            echo "<tr><td colspan='2'>Nessuna aula libera in questo orario</td></tr>";
        } else {
            while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $aula = $r['aula'];
                // Verifica sovrapposizioni in prenotazioni
                $inizio = "$year-$month-$day $orario_inizio:00";
                $fine   = "$year-$month-$day $orario_fine:00";
                $q2 = "SELECT 1 FROM prenotazioni
                       WHERE aula = ?
                         AND (oraInizio < ? AND oraFine > ?)";
                $s2 = $con->prepare($q2);
                $s2->execute([$aula, $fine, $inizio]);
                if ($s2->rowCount() === 0) {
                    echo "<tr>
                            <td>".htmlspecialchars($aula)."</td>
                            <td>
                              <form method='post'>
                                <input type='hidden' name='day'             value='$day'>
                                <input type='hidden' name='month'           value='$month'>
                                <input type='hidden' name='year'            value='$year'>
                                <input type='hidden' name='orario_inizio'   value='$orario_inizio'>
                                <input type='hidden' name='orario_fine'     value='$orario_fine'>
                                <input type='hidden' name='aula'            value='$aula'>
                                <button name='seleziona_aula'>Seleziona</button>
                              </form>
                            </td>
                          </tr>";
                }
            }
        }
        echo "</table><br>";
    }
}

// 3) Seleziona aula e inserisce prenotazione (con accettata, dataEsito e IdAmministratore = NULL)
if (isset($_POST['seleziona_aula'])) {
    $aula = $_POST['aula'];
    $i    = $_POST['orario_inizio'];
    $f    = $_POST['orario_fine'];
    $inizio = "$year-$month-$day $i:00";
    $fine   = "$year-$month-$day $f:00";
    $now    = date('Y-m-d H:i:s');
	
    $ins = "INSERT INTO prenotazioni
            (dataPrenotazione, accettata, dataEsito, oraInizio, oraFine, aula, IdUtente, IdAmministratore)
            VALUES (?, NULL, NULL, ?, ?, ?, ?, NULL)";
    $st = $con->prepare($ins);
    $st->execute([$now, $inizio, $fine, $aula, $idUtente]);

    echo "<h2>Prenotazione avvenuta!</h2>
          <p>Aula: <strong>$aula</strong><br>
             Orario: <strong>$i - $f</strong><br>
             Data: <strong>$day/$month/$year</strong></p>";
    exit;
}
// 4) Form di selezione orari (default o dopo POST senza prenotare)
?>
<form method="post">
  <input type="hidden" name="day"   value="<?php echo $day; ?>">
  <input type="hidden" name="month" value="<?php echo $month; ?>">
  <input type="hidden" name="year"  value="<?php echo $year; ?>">

  <label>Da:
    <select name="orario_inizio">
      <?php foreach ($orari as $h): ?>
        <option value="<?php echo $h; ?>"
          <?php if (!empty($orario_inizio) && $orario_inizio === $h) echo 'selected'; ?>>
          <?php echo $h; ?>
        </option>
      <?php endforeach; ?>
    </select>
  </label>

  <label>A:
    <select name="orario_fine">
      <?php foreach ($orari as $h): ?>
        <option value="<?php echo $h; ?>"
          <?php if (!empty($orario_fine) && $orario_fine === $h) echo 'selected'; ?>>
          <?php echo $h; ?>
        </option>
      <?php endforeach; ?>
    </select>
  </label>

  <button name="conferma">Trova Aule</button>
</form>
<a href='calendario.php'>Indietro</a><br>

<?php
	//header("Refresh:10; url='prenotazioni.php'");
	//header("Location: prenotazioni.php");
?>
</body>
</html>
