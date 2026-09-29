<?php
session_start();
require 'config.php';
include 'navbar.php';
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Récupérer les messages
$messages = $pdo->query("
    SELECT M.id, M.content, U.first_name 
    FROM Messages M 
    JOIN Users U ON M.sender_id = U.id 
    ORDER BY M.sent_at DESC
")->fetchAll();

// Récupérer les livres empruntés par tous les utilisateurs
$livres = $pdo->query("
    SELECT B.title, U.first_name, BR.borrow_date 
    FROM Borrows BR 
    JOIN Books B ON BR.book_id = B.id 
    JOIN Users U ON BR.user_id = U.id 
    WHERE BR.return_date IS NULL
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Accueil - Ma Bibliothèque</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h2>📚 Livres actuellement empruntés</h2>
    <ul class="book-list">
        <?php foreach ($livres as $livre): ?>
            <li>
                <strong><?= htmlspecialchars($livre['title']) ?></strong> emprunté par <?= htmlspecialchars($livre['first_name']) ?> le <?= $livre['borrow_date'] ?>
            </li>
        <?php endforeach; ?>
    </ul>

    <h2>💬 Messagerie</h2>
    <form method="POST" action="send_message.php">
        <textarea name="message" required placeholder="Écris un message..."></textarea><br>
        <button type="submit">Envoyer</button>
    </form>

    <ul class="message-list">
        <?php foreach ($messages as $msg): ?>
            <li>
                <strong><?= htmlspecialchars($msg['first_name']) ?>:</strong> <?= htmlspecialchars($msg['content']) ?>
                <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                    <form method="POST" action="delete_message.php" style="display:inline;">
                        <input type="hidden" name="id" value="<?= $msg['id'] ?>">
                        <button type="submit">🗑</button>
                    </form>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
</body>
</html>