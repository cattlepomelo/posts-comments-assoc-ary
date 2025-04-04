<!-- 1. piesledzamies pie datubazes
2. ar left join nemam posts pie comments
3. dabuju no datubazes plakanu masivu(nav hierarhijas)
4. parveidojam flat masivu par associativu
5. izvadit datus html -->

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
?>
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

<script>
    // PHP datu pārsūtīšana uz JavaScript
    const data = <?php echo $jsonData; ?>;
    
    let postsContainer = document.getElementById('posts-container');
    
    // Pārlūkojam katru postu
    for (let postId in data) {
        let post = data[postId];
        let postHtml = `
            <div class="post">
                <h2>${post.title}</h2>
                <p>${post.content}</p>
                <div class="comments">
                    <h4>Komentāri:</h4>
                    ${post.comments.length > 0 ? 
                    '<ul>' + post.comments.map(comment => `<li>${comment.comment_text}</li>`).join('') + '</ul>' 
                    : '<p><em>Nav komentāru.</em></p>'}
                </div>
            </div>
        `;
        postsContainer.innerHTML += postHtml;
    }
</script>

</body>
</html>