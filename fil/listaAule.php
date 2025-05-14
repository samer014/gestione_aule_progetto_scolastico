<?php
	include "db_connect.php"; 
	//Visualizza tabella orari
	$query = "SELECT nome FROM aule";
	
	try {
		$stmt = $con->prepare($query);
		$stmt->execute();
		$num = $stmt->rowCount();
	} catch(PDOException $ex) {
		print("<div class='error'>Errore durante il recupero delle aule: ".$ex->getMessage()."</div>");
		exit;
	}

	// Mostra la tabella delle aule
	print("<table>");

	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		print("<tr> <td>" . $row['nome'] . "</td> </tr>");
	}
	print("</table>");
?>