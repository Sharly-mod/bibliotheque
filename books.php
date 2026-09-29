<?php 
session_start();
require 'config.php';
include 'navbar.php';

// Récupération des catégories
$allCategories = $pdo->query("SELECT * FROM Book_Categories")->fetchAll();

// Filtrage
$filterCategory = isset($_GET['category']) ? (int)$_GET['category'] : 0;

$sql = "
    SELECT B.id, B.title, B.status, B.image_path,
           ROUND(AVG(BR.rating), 1) AS avg_rating,
           COUNT(BR.rating) AS nb_ratings
    FROM Books B
    LEFT JOIN Borrows BR ON BR.book_id = B.id AND BR.rating IS NOT NULL
";

$params = [];
if ($filterCategory > 0) {
    $sql .= " INNER JOIN Book_Category_Link BCL ON B.id = BCL.book_id AND BCL.category_id = ? ";
    $params[] = $filterCategory;
}

$sql .= " GROUP BY B.id";

if ($filterCategory > 0) {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $books = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}
?>

<h2>Liste des livres</h2>
<form method="GET" style="margin-bottom: 20px;">
    <label for="category">Filtrer par catégorie :</label>
    <select name="category" id="category" onchange="this.form.submit()">
        <option value="0">-- Toutes --</option>
        <?php foreach ($allCategories as $cat): ?>
            <option value="<?= $cat['id'] ?>" <?= ($filterCategory === (int)$cat['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</form>

<ul class="book-list" style="list-style:none; padding:0;">
<?php foreach ($books as $book): ?>
    <li class="book-item" style="display:flex; align-items:center; margin-bottom:15px; border-bottom:1px solid #ccc; padding-bottom:10px;">
        
        <!-- Texte à gauche -->
        <div style="flex-grow:1;">
            <a href="book_details.php?id=<?= $book['id'] ?>" class="book-link" style="font-weight:bold; font-size:1.1em;">
                <?= htmlspecialchars($book['title']) ?> 
                <span class="status <?= htmlspecialchars($book['status']) ?>" style="margin-left:8px;">
                    <?= htmlspecialchars($book['status']) ?>
                </span>
            </a>

            <div>
                Note moyenne : 
                <?php if ($book['nb_ratings'] > 0): ?>
                    <span class="stars">
                        <?php
                        $stars = round($book['avg_rating']);
                        for ($i = 1; $i <= 5; $i++) {
                            echo $i <= $stars ? '★' : '☆';
                        }
                        ?>
                    </span>
                    (<?= $book['avg_rating'] ?>/5 - <?= $book['nb_ratings'] ?> avis)
                <?php else: ?>
                    Pas encore noté
                <?php endif; ?>
            </div>

            <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
                <div style="margin-top:5px;">
                    <a href="edit_book.php?id=<?= $book['id'] ?>">Modifier</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Image à droite -->
        <?php if (!empty($book['image_path']) && file_exists($book['image_path'])): ?>
            <img src="<?= htmlspecialchars($book['image_path']) ?>" alt="Image du livre" style="height:80px; margin-left:15px; object-fit: contain; border: 1px solid #ddd; padding: 3px; background:#fff;">
        <?php else: ?>
            <div style="width:80px; height:80px; margin-left:15px; background:#eee; display:flex; align-items:center; justify-content:center; color:#aaa; font-size:0.8em;">
                Pas d’image
            </div>
        <?php endif; ?>
    </li>
<?php endforeach; ?>
</ul>
