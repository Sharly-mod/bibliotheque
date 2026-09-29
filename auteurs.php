<?php
session_start();
require 'config.php';
include 'navbar.php';

// Récupère les catégories de livre pour les filtres
$categories = $pdo->query("SELECT * FROM Book_Categories")->fetchAll();

// Récupère le filtre sélectionné
$selectedCategory = isset($_GET['category']) ? (int)$_GET['category'] : null;

// Récupère tous les auteurs
$authors = $pdo->query("SELECT * FROM Authors")->fetchAll();
?>

<h2>Liste des auteurs</h2>

<form method="GET">
    <label for="category">Filtrer par catégorie de livre :</label>
    <select name="category" id="category" onchange="this.form.submit()">
        <option value="">-- Toutes les catégories --</option>
        <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>" <?= $selectedCategory == $cat['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</form>

<?php foreach ($authors as $author): ?>
    <h3><?= htmlspecialchars($author['first_name'] . ' ' . $author['last_name']) ?></h3>
    <ul>
    <?php
        // Récupère les livres de l’auteur (et filtre par catégorie si sélectionnée)
        $sql = "
            SELECT B.title, B.id 
            FROM Books B
            JOIN Book_Author BA ON BA.book_id = B.id
            WHERE BA.author_id = ?
        ";

        if ($selectedCategory) {
            $sql .= " AND B.id IN (
                SELECT book_id FROM Book_Category_Link WHERE category_id = ?
            )";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$author['id'], $selectedCategory]);
        } else {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$author['id']]);
        }

        $books = $stmt->fetchAll();

        if ($books):
            foreach ($books as $book): ?>
                <li><a href="book_details.php?id=<?= $book['id'] ?>"><?= htmlspecialchars($book['title']) ?></a></li>
            <?php endforeach;
        else:
            echo "<li><em>Aucun livre dans cette catégorie.</em></li>";
        endif;
    ?>
    </ul>
<?php endforeach; ?>