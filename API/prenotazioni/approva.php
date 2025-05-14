<?php
    session_start();
    $user = $_SESSION["username"] ?? "";
    if ($user != "adm") {
        header('Location: ../../index.php');
        exit();
    }
    ini_set('display_errors', 'On');
    error_reporting(E_ALL);
    include "../../db_connect.php";
?>

<?php 
    $idPrenotazione = $_POST['id'];
    
    $query = "UPDATE prenotazioni 
              SET accettata = 1, 
                  dataEsito = LOCALTIME(), 
                  IdAmministratore = 1 
              WHERE id = :id;";
    
    try {
        $stmt = $con->prepare($query);
        $stmt->bindParam(':id', $idPrenotazione, PDO::PARAM_STR);
        $stmt->execute();
    } catch(PDOException $ex) {
        print($ex);
        exit();
    }
    header('Location: ../../amministrazione/richieste.php');
?>