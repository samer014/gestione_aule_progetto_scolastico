<?php
			session_start();
			$user=$_SESSION["username"]??"";

			ini_set('display_errors', 'On');
			error_reporting(E_ALL);
			include "./db_connect.php";
?>
<?php 
    $username = $_POST['newUser']; //dati da creaUtente.php
    $password = $_POST['newPass'];
	
	$query = "INSERT INTO utenti(username, password, amministratore) values(:username, :password, 0);"; //query
    $stmt = $con->prepare($query);
	
	if ($stmt->execute([':username' => $username, ':password' => $password])) { //inserimento dei dati nel db
        header('Location: index.php');
        exit;
	}
?>