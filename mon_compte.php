<?php
session_start();
require 'config.php';
include 'navbar.php';
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user']['id'];

$emprunts = $pdo->prepare("
    SELECT B.id as book_id, B.title, BR.borrow_date 
    FROM Borrows BR 
    JOIN Books B ON BR.book_id = B.id 
    WHERE BR.user_id = ? AND BR.return_date IS NULL
");
$emprunts->execute([$userId]);
?>
<?php foreach ($emprunts as $e): ?>
    <li>
        <?= htmlspecialchars($e['title']) ?> (emprunté le <?= $e['borrow_date'] ?>)
        
        <form method="POST" action="return.php" style="display:inline;">
            <input type="hidden" name="book_id" value="<?= $e['book_id'] ?>">
            
            <!-- Choix de la note -->
            <label for="rating_<?= $e['book_id'] ?>">Note :</label>
            <select name="rating" id="rating_<?= $e['book_id'] ?>" required>
                <option value="">--</option>
                <?php for ($i=1; $i<=5; $i++): ?>
                    <option value="<?= $i ?>"><?= $i ?> ★</option>
                <?php endfor; ?>
            </select>
            
            <button type="submit">Retourner & Noter</button>
        </form>
    </li>
<?php endforeach; ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon compte - Ma Bibliothèque</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h2>👤 Mon compte : <?= htmlspecialchars($_SESSION['user']['first_name']) ?></h2>

    <h3>📘 Mes emprunts</h3>
    <ul class="book-list">
        <?php foreach ($emprunts as $e): ?>
            <li>
                <?= htmlspecialchars($e['title']) ?> (emprunté le <?= $e['borrow_date'] ?>)
                <form method="POST" action="return.php" style="display:inline;">
                    <input type="hidden" name="book_id" value="<?= $e['book_id'] ?? '' ?>">
                    <button type="submit">Retourner</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
</body>
</html>
