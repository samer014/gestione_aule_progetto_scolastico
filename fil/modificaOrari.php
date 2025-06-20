<?php
	session_start();
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
	$ora = $_POST['ora'];
	$query = "SELECT oraInizio FROM orari WHERE ora = :ora;"; // seleziona l'orario da modificare
	
	try {
		$stmt = $con->prepare( $query );
		$stmt->bindParam(':ora', $ora, PDO::PARAM_STR);
		$stmt->execute();
	} catch(PDOException $ex) {
		print($ex);
		exit();
	}
	
	print("<table border='1'>\n".
				  "<tr>\n".
				  "<th>Ora</th>\n".
				  "<th>Nuovo orario inizio</th>\n".
				  "<th>Conferma</th>\n".
				  "</tr>\n");
	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
		print("<tr>".
				"<td>".$ora."</td>\n".
				"<form action='aggiornaOrari.php' method='post'>".
					"<td><input name='oraInizio' value=".$row['oraInizio']."></td>\n".
					"<td>".
						"<a href='orari.php'><button class='fixed-button' value=".$ora." name='ora'>Conferma</button></a>".
				"</form>".
					"</td><br>".
			"</tr>\n"
		);
	}
	print("</table><br>\n");
	print("<a href=\"orari.php\">Annulla</a><br>"); //annulla modifiche
?>