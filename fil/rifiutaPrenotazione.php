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
	$idP = $_POST['id'];
	//print("$idP");
	$query = "UPDATE prenotazioni SET accettata = 0, dataEsito = LOCALTIME(), IdAmministratore = 1 WHERE id = :id;";
	
	try {
		$stmt = $con->prepare( $query );
		$stmt->bindParam(':id', $idP, PDO::PARAM_STR);
		$stmt->execute();
	} catch(PDOException $ex) {
		print($ex);
		exit();
	}
	header('Location: richieste.php'); // ritorno alla lista di richieste
?>