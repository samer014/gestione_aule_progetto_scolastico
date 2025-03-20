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
<html>
	<head>
		<link rel="stylesheet" href="stile.css">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
	</head>
</html>
<?php
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
			  "<th>Numero ora</th>\n".
			  "<th>Orario di inizio dell'ora</th>\n".
			  "</tr>\n");
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
			print("<tr>".
				"<td>".$row['ora']."</td>\n".
				"<td>".$row['oraInizio']."</td>\n". //visualizzazione orari attuali
				"<td>".
					"<form action='modificaOrari.php' method='post'>". //modifica orari attuali
						"<a href='modificaOrari.php'><button class='fixed-button' value=".$row['ora']." name='ora'>Modifica</button></a>".
					"</form>".
				"</td>".
				"</tr>\n");
		}
		echo "</table> <br><br>";
		print("<a href='index.php'> Home </a>");
?>