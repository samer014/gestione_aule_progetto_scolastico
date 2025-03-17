<?php
			session_start();
			$user=$_SESSION["username"]??"";
			$user=$_SESSION["username"]??"";
			if ($user==""){
				header('Location: login.php');
				exit();
			}
			ini_set('display_errors', 'On');
			error_reporting(E_ALL);
			include "./db_connect.php";
?>

<?php 
	$query = "SELECT prenotazioni.id, prenotazioni.dataPrenotazione, prenotazioni.oraInizio, prenotazioni.oraFine, prenotazioni.aula, utenti.username ". 
			"FROM prenotazioni JOIN utenti ON utenti.id = prenotazioni.IdUtente;"; // tutti gli orari dal db
	try {
		$stmt = $con->prepare($query);
		$stmt->execute();
		} catch(PDOException $ex) {
			print("Errore !".$ex->getMessage());
			exit;
		}
		print("<table border='3'>\n".
			  "<tr>\n".
			  "<th>Data Prenotazione</th>\n".
			  "<th>Orario di inizio</th>\n".
			  "<th>Orario di fine</th>\n".
			  "<th>Aula</th>\n".
			  "<th>Utente</th>\n".
			  "</tr>\n");
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
			print("<tr>".
				"<td>".$row['dataPrenotazione']."</td>\n".
				"<td>".$row['oraInizio']."</td>\n".
				"<td>".$row['oraFine']."</td>\n".
				"<td>".$row['aula']."</td>\n".
				"<td>".$row['username']."</td>\n".
				"<td>".
					"<form action='accettaP.php' method='post'>".
						"<button class='fixed-button' value=".$row['id']." name='ora'>ACCETTA</button>".
					"</form>".
				"</td>".
				"<td>".
					"<form action='rifiutaP.php' method='post'>".
						"<button class='fixed-button' value=".$row['id']." name='ora'>RIFIUTA</button>".
					"</form>".
				"</td>".
				"</tr>\n");
		}
		echo "</table> <br><br>";
?>