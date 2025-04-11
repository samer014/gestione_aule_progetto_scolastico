<?php
	session_start();
	$user=$_SESSION["username"]??"";
	if ($user == ""){
		header('Location: index.php');
		exit();
	}
	ini_set('display_errors', 'On');
	error_reporting(E_ALL);
	include "./db_connect.php";
?>

<?php
	$query = "SELECT prenotazioni.oraInizio, prenotazioni.oraFine, prenotazioni.aula, prenotazioni.accettata " .
		"FROM prenotazioni " .
		"WHERE prenotazioni.IdUtente = (" .
			"SELECT id " .
			"FROM utenti " .
			"WHERE username = :user" .
		")";
		
	try {
		$stmt = $con->prepare($query);
		$stmt->bindParam(':user', $user, PDO::PARAM_STR);
		$stmt->execute();
		$num = $stmt->rowCount();
	} catch(PDOException $ex) {
		print("Errore !".$ex->getMessage());
		exit;
	}
	
	if($num == 0){
		print("<h2>Nessuna prenotazione effettuata</h2>");
	}else{
		print("<table border='3'>\n".
			"<tr>\n".
			"<th>Giorno</th>\n".
			"<th>Inizio</th>\n".
			"<th>Fine</th>\n".
			"<th>Aula</th>\n".
			"<th>Esito</th>\n".
			"</tr>\n");
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
			$giorno = explode(" ", $row['oraInizio']);
			$oraInizio = explode(" ", $row['oraInizio']);
			$oraFine = explode(" ", $row['oraFine']);
			$accettata = "In revisione";
			if($row['accettata']==1){
				$accettata = "accettata";
			}
			if($row['accettata']==0){
				$accettata = "rifiutata";
			}
			print("<tr>".
				"<td>".$giorno[0]."</td>\n".
				"<td>".$oraInizio[1].	"</td>\n".
				"<td>".$oraFine[1].	"</td>\n".
				"<td>".$row['aula'].	"</td>\n".
				"<td>".$accettata.	"</td>\n"
			);
		}
		echo "</table> <br><br>";
	}
	print("<a href='index.php'> Home </a>");
?>