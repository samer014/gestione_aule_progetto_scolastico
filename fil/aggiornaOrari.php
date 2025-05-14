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
    $nuovaOraInizio = $_POST['oraInizio'];
	$query = "UPDATE orari SET oraInizio = :oraInizio WHERE ora = :ora;"; //query per aggiornare l'ora
	
	try {
		$stmt = $con->prepare( $query );
		$stmt->bindParam(':ora', $ora, PDO::PARAM_STR);
		$stmt->bindParam(':oraInizio', $nuovaOraInizio, PDO::PARAM_STR);
		$stmt->execute();
	} catch(PDOException $ex) {
		print($ex);
		exit();
	}
	header('Location: orari.php'); //torna alla pagina che elenca gli orari (aggiornati)
?>