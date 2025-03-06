<?php
	$user=$_POST["username"]??"";
	$pass=$_POST["password"]??"";
	if ($user=="" ){
		header('Location: index.php');
		exit();
	}
	session_start();
	include 'db_connect.php';
	$log=0;
	$query = "select * from utenti where username = :user;";
	try {
		$stmt = $con->prepare( $query );
		$stmt->bindParam(':user', $user, PDO::PARAM_STR);
		$stmt->execute();
		$num = $stmt->rowCount();
		if ($num>0){
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
		}
		if ($pass==$row["password"]){
			$log=1;
		}
	} catch(PDOException $ex) {
		print($ex);
		exit();
	}
	if ($log==1){
		$_SESSION["username"] = $row["username"];
		$ref=$_SERVER["referer"]??"";
		if ($ref=="")
		header('Location: index.php');
		else
		header('Location:'. $ref);
	}else {
		session_destroy();
		header('Location: loginErrato.php');
	}
?>