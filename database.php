<?php
// database.php

// Iestatījumi datubāzes pieslēgumam
$host = 'localhost';      // Datubāzes hosta nosaukums
$db   = 'blog_12032025';  // Datubāzes nosaukums
$user = 'TripiTropi';     // Lietotājvārds
$pass = 'password';       // Parole
$charset = 'utf8mb4';     // Rakstzīmju kodējums

// Izveidojam DSN (Data Source Name)
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// Iestatījumi PDO opcijām
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Kļūdu ziņojumi
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC // Noklusējuma datu iegūšanas režīms
];

// Mēģinām izveidot savienojumu
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    // Ja viss ir veiksmīgi, izvada ziņojumu
    // echo "Savienojums izveidots!";
} catch (PDOException $e) {
    // Ja notiek kļūda, pārtrauc izpildi un izvada kļūdu
    die("Savienojuma kļūda: " . $e->getMessage());
}
?>