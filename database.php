<?php
// Database.php

class Database {
    private $host;
    private $db;
    private $user;
    private $pass;
    private $charset;
    private $pdo;

    // Konstruktoram jāpieņem datubāzes iestatījumi no ārpuses
    public function __construct($host, $db, $user, $pass, $charset = 'utf8mb4') {
        $this->host = $host;
        $this->db = $db;
        $this->user = $user;
        $this->pass = $pass;
        $this->charset = $charset;

        $dsn = "mysql:host=$this->host;dbname=$this->db;charset=$this->charset";
        
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Kļūdu ziņojumi
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC // Noklusējuma datu iegūšanas režīms
        ];

        try {
            // Izveidojam PDO savienojumu
            $this->pdo = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (PDOException $e) {
            // Ja ir kļūda, pārtrauc izpildi un izvada kļūdu
            die("Savienojuma kļūda: " . $e->getMessage());
        }
    }

    // Funkcija, kas atgriež PDO objektu
    public function getPDO() {
        return $this->pdo;
    }
}
?>