<?php
class Database {
    private static $instance = null;
    private $con;
    
    private function __construct() {
        $host = "localhost";
        $db_name = "dbprenotazioniaule";
        $username = "root";
        $password = "";

        try {
            $this->con = new PDO(
                "mysql:host={$host};dbname={$db_name}", 
                $username, 
                $password,
                array(PDO::ATTR_PERSISTENT => true)
            );
            $this->con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            throw new Exception("Errore di connessione: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->con;
    }
}
?>