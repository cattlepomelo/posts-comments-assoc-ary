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
        form {
            background: #fff;
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        form input, form textarea {
            width: 100%;
            margin-bottom: 10px;
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        form button {
            padding: 10px 15px;
            background: #007BFF;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        form button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
<h1>Posti un komentāri</h1>

<!-- Forma jauna posta pievienošanai -->
<form method="post">
    <input type="text" name="title" placeholder="Virsraksts" required>
    <textarea name="content" placeholder="Saturs" required></textarea>
    <input type="text" name="author" placeholder="Autors" required>
    <button type="submit">Pievienot postu</button>
</form>

<div id="posts-container"></div>

<?php
require_once 'database.php'; // Iekļaujam datubāzes savienojuma klasi

// ======= Klases =======
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

// ======= Datubāzes iestatījumi =======
$host = 'localhost';
$db   = 'blog_12032025';
$user = 'TripiTropi';
$pass = 'password';
$charset = 'utf8mb4';

$database = new Database($host, $db, $user, $pass, $charset);
$pdo = $database->getPDO();

// ======= Jauna posta apstrāde =======
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $author = trim($_POST['author'] ?? '');

    if ($title && $content && $author) {
        try {
            $stmt = $pdo->prepare("INSERT INTO posts (title, content, author) VALUES (?, ?, ?)");
            $stmt->execute([$title, $content, $author]);
            header("Location: " . $_SERVER['PHP_SELF']); // Pārlādējam lapu pēc pievienošanas
            exit;
        } catch (PDOException $e) {
            echo "Kļūda pievienojot postu: " . $e->getMessage();
        }
    } else {
        echo "<p style='color:red;'>Lūdzu, aizpildiet visus laukus!</p>";
    }
}

// ======= Iegūstam postus un komentārus =======
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

} catch (PDOException $e) {
    die("Kļūda pieprasījumā: " . $e->getMessage());
}

// ======= Izvadām visus postus =======
echo '<div class="posts-container">';
foreach ($posts as $post) {
    $post->display();
}
echo '</div>';
?>

</body>
</html>