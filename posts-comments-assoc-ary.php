<!-- 1. piesledzamies pie datubazes
2. ar left join nemam posts pie comments
3. dabuju no datubazes plakanu masivu(nav hierarhijas)
4. parveidojam flat masivu par associativu
5. izvadit datus html -->

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
    // 2. SQL vaicājums ar LEFT JOIN (iegūstam plakano masīvu)
    $sql = "
        SELECT 
            posts.post_id,
            posts.title,
            posts.content,
            comments.comment_id,
            comments.comment_text
        FROM posts
        LEFT JOIN comments ON posts.post_id = comments.post_id
    ";

    // Veicam vaicājumu
    $stmt = $pdo->query($sql);

    // Iegūstam datus kā plakano masīvu
    $flatData = $stmt->fetchAll();

    // Ja dati ir iegūti veiksmīgi, varam turpināt apstrādāt
    echo "Dati veiksmīgi iegūti.";
} catch (PDOException $e) {
    // Ja rodas kļūda savienojumā, parādām ziņojumu
    die("Savienojuma kļūda: " . $e->getMessage());
}
?>
