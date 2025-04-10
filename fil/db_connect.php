<?php
    $host = "localhost";
    $db_name = "dbprenotazioniaule";
    $username = "root";
    $password = "";

    try {
        $con = new PDO("mysql:host={$host};dbname={$db_name}", $username, $password, array(PDO::ATTR_PERSISTENT => true));
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }  
    // per gestire errori di connessione
    catch(PDOException $exception){
        echo "Errore di connessione: " . $exception->getMessage();
        exit;
    }
?>
