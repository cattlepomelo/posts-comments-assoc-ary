<!-- 1. piesledzamies pie datubazes
2. ar left join nemam posts pie comments
3. dabuju no datubazes plakanu masivu(nav hierarhijas)
4. parveidojam flat masivu par associativu -->

<?php
// 1. Pieslēgšanās datubāzei
$host = 'localhost'; // Datubāzes serveris
$db   = 'blog_12032025'; // Datubāzes nosaukums
$user = 'TripiTropi'; // Lietotājvārds
$pass = 'password'; // Parole
$charset = 'utf8mb4'; // Rakstzīmju komplekts

$dsn = "mysql:host=$host;dbname=$db;charset=$charset"; // DSN (Data Source Name)
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Aktivē kļūdu ziņojumus
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC // Iestatām, lai rezultāti būtu asociatīvi masīvi
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options); // Izveido savienojumu
    echo "Savienojums izveidots ar datubāzi!";
} catch (PDOException $e) {
    // Ja rodas kļūda savienojumā, parādām ziņojumu
    die("Savienojuma kļūda: " . $e->getMessage());
}
?>