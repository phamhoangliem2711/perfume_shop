<?php
class Database {
    private $host = "localhost";
    private $db = "perfume_shop"; // you can change to your DB name
    private $user = "root";
    private $pass = "";

    //=== Hosting db config
    // private $host = "sql211.infinityfree.com";
    // private $db   = "if0_40652179_shop";
    // private $user = "if0_40652179";
    // private $pass = "KQUVg60oDK";

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
