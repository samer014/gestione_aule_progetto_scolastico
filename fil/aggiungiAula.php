<?php
    session_start();
    $user = $_SESSION["username"] ?? "";
    if ($user != "adm") {
        header('Location: index.php');
        exit();
    }
    ini_set('display_errors', 'On');
    error_reporting(E_ALL);
    include "./db_connect.php";
?>

<?php
	$aula = $_POST['aula']??"";
	print($aula);
	
	$query = "INSERT INTO aule(nome) values(:aula)";
	
	try {
		$stmt = $con->prepare( $query );
		$stmt->bindParam(':aula', $aula, PDO::PARAM_STR);
		$stmt->execute();
	} catch(PDOException $ex) {
		print($ex);
		exit();
	}
	header('Location: indexAule.php');
?>