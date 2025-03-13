<h1 style="text-align: center;">Home</h1>
<?php
	session_start();
	$user=$_SESSION["username"]??"";

	ini_set('display_errors', 'On');
	error_reporting(E_ALL);
	include "./db_connect.php";
?>
<?php
	
	if ($user==""){ //utente non loggato
		print("<a href=\"login.php\">LOGIN</a><br>");+
		print("<a href=\"register.php\">SIGNUP</a>");
		exit(0);
	}else{
		$query = "SELECT amministratore FROM utenti WHERE username = :user";
		try {
			$stmt = $con->prepare( $query );
			$stmt->bindParam(':user', $user, PDO::PARAM_STR);
			$stmt->execute();
			
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($row["amministratore"] == 1){ //se amministratore può aggiungere utenti
				print("<h3> Bentornato Boss </h3>");
				print("<a href=\"logout.php\">LOGOUT</a><br>");
				print("<a href=\"creaUtente.php\">CREA UTENTE</a><br>");
				print("<a href=\"prenota.php\">PRENOTA AULE</a><br>");
				print("<a href=\"orari.php\">MODIFICA ORARI</a><br>");
			}else { // se utente normale
				print("<h3> Bentornato $user </h3>");
				print("<a href=\"logout.php\">LOGOUT</a><br>");
				print("<a href=\"prenota.php\">PRENOTA AULE</a><br>");
			}
		} catch(PDOException $ex) {
			print($ex);
			exit();
		}
	}
	/*
	utente:
	prenotare
	visualizzare l’esito della prenotazione
	visualizzare lista aule e orari disponibili

	amministratore:
	creazione utenti
	accettazione / rifiuto prenotazioni
	modifica orari
	prenotazioni autonome
	cancellazione prenotazioni
	*/
?>