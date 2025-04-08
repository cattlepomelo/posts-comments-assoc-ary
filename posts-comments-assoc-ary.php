<!-- 1. piesledzamies pie datubazes
2. ar left join nemam posts pie comments
3. dabuju no datubazes plakanu masivu(nav hierarhijas)
4. parveidojam flat masivu par associativu
5. izvadit datus html
6. mainit masivu uz objektu un pievienot konstruktoru
7. izvadīt posts ar funkciju display();
8. pieslegšanas pie datubazes caur include un arejo failu
9. izmantot construktor lai pieslegties pie datubazes -->

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
// Iekļaujam Database.php, lai izmantotu datubāzes savienojumu
require_once 'database.php'; // Iekļaujam datubāzes savienojuma klasi

class Comment {
    public $comment_id;
    public $comment_text;

    public function __construct($comment_id, $comment_text) {
        $this->comment_id = $comment_id;
        $this->comment_text = $comment_text;
    }
}

class Post {
    public $post_id;
    public $title;
    public $content;
    public $comments = [];

    public function __construct($post_id, $title, $content) {
        $this->post_id = $post_id;
        $this->title = $title;
        $this->content = $content;
    }

    public function addComment($comment) {
        $this->comments[] = $comment;
    }

    // display funkcija, kas izvada postu un komentārus
    public function display() {
        echo '<div class="post">';
        echo '<h2>' . htmlspecialchars($this->title) . '</h2>';
        echo '<p>' . nl2br(htmlspecialchars($this->content)) . '</p>';

        echo '<div class="comments">';
        echo '<h4>Komentāri:</h4>';

        if (!empty($this->comments)) {
            echo '<ul>';
            foreach ($this->comments as $comment) {
                echo '<li>' . htmlspecialchars($comment->comment_text) . '</li>';
            }
            echo '</ul>';
        } else {
            echo '<p><em>Nav komentāru.</em></p>';
        }

        echo '</div>'; // .comments
        echo '</div>'; // .post
    }
}

// Definējam datubāzes iestatījumus
$host = 'localhost';
$db   = 'blog_12032025';
$user = 'TripiTropi';
$pass = 'password';
$charset = 'utf8mb4';

// Izveidojam Database objektu, pārsūtot datubāzes iestatījumus uz konstruktoru
$database = new Database($host, $db, $user, $pass, $charset);

// Iegūstam PDO objektu no Database klases
$pdo = $database->getPDO();

try {
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

    $posts = [];

    foreach ($flatData as $row) {
        $postId = $row['post_id'];

        if (!isset($posts[$postId])) {
            $posts[$postId] = new Post($postId, $row['title'], $row['content']);
        }

        if (!empty($row['comment_id'])) {
            $comment = new Comment($row['comment_id'], $row['comment_text']);
            $posts[$postId]->addComment($comment);
        }
    }

    // Pievienojam vēl vienu testu postu ar komentāriem
    $post = new Post(999, "Testa Post", "Šis ir testa saturs.");
    $post->addComment(new Comment(1, "Šis ir pirmais komentārs."));
    $post->addComment(new Comment(2, "Šis ir otrais komentārs."));
    $post->addComment(new Comment(3, "Šis ir trešais komentārs."));

    $posts[999] = $post;

} catch (PDOException $e) {
    die("Kļūda pieprasījumā: " . $e->getMessage());
}

echo '<div class="posts-container">';

// Izvadām visus postus, izmantojot display metodi
foreach ($posts as $post) {
    $post->display();
}

echo '</div>'; // .posts-container
?>

</body>
</html>