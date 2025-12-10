<?php
class Database {
    private $host = "localhost";
    private $db = "perfume_shop"; // you can change to your DB name
    private $user = "root";
    private $pass = "";

    public function connect() {
        try {
            $pdo = new PDO("mysql:host=$this->host;dbname=$this->db;charset=utf8", $this->user, $this->pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            die("DB Connection failed: " . $e->getMessage());
        }
    }
}
