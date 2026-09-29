<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Ma Bibliothèque - Détails du livre</title>
    <link rel="stylesheet" href="style.css" />
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 800px;
            margin: 30px auto;
            padding: 0 20px;
            background-color: #f7f9fc;
            color: #2c3e50;
        }
        h2 {
            text-align: center;
            margin-bottom: 15px;
            color: #34495e;
        }
        p {
            font-size: 1.1em;
            line-height: 1.5;
            margin-bottom: 12px;
        }
        strong {
            color: #2980b9;
        }
        form {
            text-align: center;
            margin-top: 25px;
        }
        button {
            background-color: #27ae60;
            border: none;
            padding: 12px 25px;
            font-size: 1.1em;
            color: white;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #219150;
        }
        a {
            color: #2980b9;
            text-decoration: none;
            font-weight: 600;
        }
        a:hover {
            text-decoration: underline;
        }
        em {
            color: #7f8c8d;
        }
    </style>
</head>
<body>

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'config.php';
include 'navbar.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID de livre manquant.");
}

$bookId = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM Books WHERE id = ?");
$stmt->execute([$bookId]);
$book = $stmt->fetch();
$stmt3 = $pdo->prepare("
    SELECT BC.name 
    FROM Book_Categories BC
    JOIN Book_Category_Link BCL ON BC.id = BCL.category_id
    WHERE BCL.book_id = ?
");
$stmt3->execute([$bookId]);
$categories = $stmt3->fetchAll();
if (!$book) {
    die("Livre introuvable.");
}

$stmt2 = $pdo->prepare("SELECT A.first_name, A.last_name FROM Authors A 
                        JOIN Book_Author BA ON A.id = BA.author_id 
                        WHERE BA.book_id = ?");
$stmt2->execute([$bookId]);
$authors = $stmt2->fetchAll();
?>

<h2>
    <?php if (!empty($book['image_path']) && file_exists($book['image_path'])): ?>
        <img src="<?= htmlspecialchars($book['image_path']) ?>" 
             alt="Image du livre <?= htmlspecialchars($book['title']) ?>" 
             style="max-height:200px; float:right; margin-left:20px; border:1px solid #ccc; padding:5px; background:#fff;">
    <?php endif; ?>
    <?= htmlspecialchars($book['title']) ?>
</h2>
<p><?= nl2br(htmlspecialchars($book['description'])) ?></p>
<p><strong>Status :</strong> <?= htmlspecialchars($book['status']) ?></p>
<p><strong>Auteurs :</strong>
    <?php
        if ($authors) {
            $names = array_map(function($a) {
                return htmlspecialchars($a['first_name'] . ' ' . $a['last_name']);
            }, $authors);
            echo implode(', ', $names);
        } else {
            echo "Aucun auteur trouvé.";
        }
    ?>
</p>
<p><strong>Catégories :</strong>
    <?php
    $stmt3 = $pdo->prepare("
        SELECT BC.name 
        FROM Book_Categories BC
        JOIN Book_Category_Link BCL ON BC.id = BCL.category_id
        WHERE BCL.book_id = ?
    ");
    $stmt3->execute([$bookId]);
    $categories = $stmt3->fetchAll();

    if ($categories) {
        echo implode(', ', array_map(fn($c) => htmlspecialchars($c['name']), $categories));
    } else {
        echo "Aucune catégorie.";
    }
    ?>
</p>
<?php if ($book['status'] === 'disponible' && isset($_SESSION['user'])): ?>
    <form method="POST" action="borrow.php">
        <input type="hidden" name="book_id" value="<?= $bookId ?>">
        <button type="submit">📚 Emprunter</button>
    </form>
<?php elseif (!isset($_SESSION['user'])): ?>
    <p><a href="login.php">Connectez-vous</a> pour emprunter ce livre.</p>
<?php else: ?>
    <p><em>Ce livre est déjà emprunté.</em></p>
<?php endif; ?>

</body>
</html>