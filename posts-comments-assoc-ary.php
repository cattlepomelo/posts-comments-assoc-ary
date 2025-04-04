<!-- 1. piesledzamies pie datubazes
2. ar left join nemam posts pie comments
3. dabuju no datubazes plakanu masivu(nav hierarhijas)
4. parveidojam flat masivu par associativu
5. izvadit datus html -->
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>Posti un komentāri</title>
    <style>
        body {
            font-family: sans-serif;
            background: #f7f7f7;
            padding: 30px;
        }
        .post {
            background: #fff;
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .post h2 {
            margin-top: 0;
        }
        .comments {
            margin-top: 15px;
        }
        .comments ul {
            padding-left: 20px;
        }
    </style>
</head>
<body>
<h1>Posti un komentāri</h1>

<div id="posts-container"></div>

<?php
// Pieslēgšanās datubāzei
$host = 'localhost';
$db   = 'blog_12032025';
$user = 'TripiTropi';
$pass = 'password';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // SQL vaicājums
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

    $stmt = $pdo->query($sql);
    $flatData = $stmt->fetchAll();

    // Pārveidojam uz asociatīvo struktūru
    $structuredData = [];

    foreach ($flatData as $row) {
        $postId = $row['post_id'];

        if (!isset($structuredData[$postId])) {
            $structuredData[$postId] = [
                'post_id' => $postId,
                'title' => $row['title'],
                'content' => $row['content'],
                'comments' => []
            ];
        }

        if (!empty($row['comment_id'])) {
            $structuredData[$postId]['comments'][] = [
                'comment_id' => $row['comment_id'],
                'comment_text' => $row['comment_text']
            ];
        }
    }

    // Saglabājam datus JSON formātā
    $jsonData = json_encode($structuredData);

} catch (PDOException $e) {
    die("Savienojuma kļūda: " . $e->getMessage());
}


echo '<div class="posts-container">';

foreach ($structuredData as $post) {
    // Veidojam HTML, izmantojot PHP
    echo '<div class="post">';
    echo '<h2>' . htmlspecialchars($post['title']) . '</h2>';
    echo '<p>' . nl2br(htmlspecialchars($post['content'])) . '</p>';

    echo '<div class="comments">';
    echo '<h4>Komentāri:</h4>';

    if (!empty($post['comments'])) {
        echo '<ul>';
        foreach ($post['comments'] as $comment) {
            echo '<li>' . htmlspecialchars($comment['comment_text']) . '</li>';
        }
        echo '</ul>';
    } else {
        echo '<p><em>Nav komentāru.</em></p>';
    }

    echo '</div>'; // .comments
    echo '</div>'; // .post
}

echo '</div>'; // .posts-container
?>

</body>
</html>