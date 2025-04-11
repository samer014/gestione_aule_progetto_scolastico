<?php
	session_start();
	$user=$_SESSION["username"]??"";
	$user=$_SESSION["username"]??"";
	if ($user != "adm"){
		header('Location: index.php');
		exit();
	}
	ini_set('display_errors', 'On');
	error_reporting(E_ALL);
	include "./db_connect.php";
?>

<?php 
	$query = "SELECT prenotazioni.id, prenotazioni.dataPrenotazione, prenotazioni.oraInizio, prenotazioni.oraFine, prenotazioni.aula, utenti.username ". 
			"FROM prenotazioni JOIN utenti ON utenti.id = prenotazioni.IdUtente
			WHERE prenotazioni.accettata IS NULL;"; //visualizza le richieste senza un esito
	try {
		$stmt = $con->prepare($query);
		$stmt->execute();
		$num = $stmt->rowCount();
	} catch(PDOException $ex) {
		print("Errore !".$ex->getMessage());
		exit;
	}
	if($num == 0){
		print("<h2>Nessuna richiesta al momento</h2>"); //se tutte le richieste hanno avuto un esito
	}else{
		print("<table border='3'>\n".
			"<tr>\n".
			"<th>Giorno</th>\n".
			"<th>Inizio</th>\n".
			"<th>Fine</th>\n".
			"<th>Aula</th>\n".
			"<th>Utente</th>\n".
			"</tr>\n");
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
			$giorno = explode(" ", $row['oraInizio']);
			$oraInizio = explode(" ", $row['oraInizio']);
			$oraFine = explode(" ", $row['oraFine']);
			print("<tr>".
				"<td>".$giorno[0]."</td>\n".
				"<td>".$oraInizio[1].	"</td>\n".
				"<td>".$oraFine[1].	"</td>\n".
				"<td>".$row['aula'].	"</td>\n".
				"<td>".$row['username'].	"</td>\n". //visualizza i dettagli della prenotazione
				"<td>".
					"<form action='doAccettaP.php' method='post'>".
						"<button value=".$row['id']." name='id'>ACCETTA</button>".  //accetta la prenotazione
					"</form>".
				"</td>".
				"<td>".
					"<form action='doRifiutaP.php' method='post'>".
						"<button value=".$row['id']." name='id'>RIFIUTA</button>". //rifiuta la prenotazione
					"</form>".
				"</td>".
				"</tr>\n");
		}
		echo "</table> <br><br>";
	}
	print("<a href='index.php'> Home </a>"); //ritorno all'index
	//onclick='window.location.reload(true);'
?>