<?php
session_start();
require 'config.php';
include 'navbar.php';

// Vérifier admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    die("Accès refusé.");
}

// Récupérer l’ID du livre
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID de livre invalide.");
}

$bookId = (int)$_GET['id'];
$message = '';

// Traitement ajout d'auteur
if (isset($_POST['add_author'])) {
    $firstName = trim($_POST['new_author_first_name']);
    $lastName = trim($_POST['new_author_last_name']);

    if ($firstName && $lastName) {
        $stmt = $pdo->prepare("SELECT id FROM Authors WHERE first_name = ? AND last_name = ?");
        $stmt->execute([$firstName, $lastName]);
        $existing = $stmt->fetch();

        if (!$existing) {
            $stmt = $pdo->prepare("INSERT INTO Authors (first_name, last_name) VALUES (?, ?)");
            $stmt->execute([$firstName, $lastName]);
        }
        header("Location: edit_book.php?id=" . $bookId);
        exit;
    }
}

// Récupérer données du livre
$stmt = $pdo->prepare("SELECT * FROM Books WHERE id = ?");
$stmt->execute([$bookId]);
$book = $stmt->fetch();

if (!$book) {
    die("Livre non trouvé.");
}

// Récupérer tous les auteurs
$allAuthors = $pdo->query("SELECT * FROM Authors ORDER BY last_name")->fetchAll();

// Auteurs associés au livre
$stmt = $pdo->prepare("SELECT author_id FROM Book_Author WHERE book_id = ?");
$stmt->execute([$bookId]);
$bookAuthorIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Catégories
$allCategories = $pdo->query("SELECT * FROM Book_Categories")->fetchAll();
$stmtCat = $pdo->prepare("SELECT category_id FROM Book_Category_Link WHERE book_id = ?");
$stmtCat->execute([$bookId]);
$bookCategoryIds = $stmtCat->fetchAll(PDO::FETCH_COLUMN);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['add_author'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $year = $_POST['year'];

    $stmt = $pdo->prepare("UPDATE Books SET title = ?, description = ?, published_year = ? WHERE id = ?");
    $stmt->execute([$title, $description, $year, $bookId]);

    // Mettre à jour les auteurs
    $pdo->prepare("DELETE FROM Book_Author WHERE book_id = ?")->execute([$bookId]);
    if (!empty($_POST['authors'])) {
        $stmtAuthor = $pdo->prepare("INSERT INTO Book_Author (book_id, author_id) VALUES (?, ?)");
        foreach ($_POST['authors'] as $authorId) {
            $stmtAuthor->execute([$bookId, $authorId]);
        }
    }

    // Mettre à jour les catégories
    $pdo->prepare("DELETE FROM Book_Category_Link WHERE book_id = ?")->execute([$bookId]);
    if (!empty($_POST['categories'])) {
        $stmtCatInsert = $pdo->prepare("INSERT INTO Book_Category_Link (book_id, category_id) VALUES (?, ?)");
        foreach ($_POST['categories'] as $catId) {
            $stmtCatInsert->execute([$bookId, $catId]);
        }
    }

    // Gérer image
    if (isset($_FILES['image_path']) && $_FILES['image_path']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $tmpName = $_FILES['image_path']['tmp_name'];
        $filename = basename($_FILES['image_path']['name']);
        $targetFile = $uploadDir . uniqid() . '_' . $filename;
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $mimeType = mime_content_type($tmpName);

        if (in_array($mimeType, $allowedMimeTypes)) {
            if (move_uploaded_file($tmpName, $targetFile)) {
                $stmt = $pdo->prepare("UPDATE Books SET image_path = ? WHERE id = ?");
                $stmt->execute([$targetFile, $bookId]);
            }
        }
    }

    $message = "Livre modifié avec succès !";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un livre</title>
    <link rel="stylesheet" href="style.css">
    <style>
        #authorModal input {
            padding: 10px;
            margin: 8px 0;
            width: 100%;
            box-sizing: border-box;
        }
        #authorModal button {
            padding: 10px;
            margin-top: 10px;
            margin-right: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }
        #authorModal button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<div style="max-width:600px; margin:auto;">
    <?php if ($message): ?>
        <p style="background:#d4edda; color:#155724; padding:10px; border-radius:5px;">
            <?= htmlspecialchars($message) ?>
        </p>
    <?php endif; ?>

    <h2>Modifier le livre</h2>
    <form action="edit_book.php?id=<?= $bookId ?>" method="POST" enctype="multipart/form-data">
        <input name="title" value="<?= htmlspecialchars($book['title']) ?>" placeholder="Titre" required><br>
        <textarea name="description" placeholder="Description"><?= htmlspecialchars($book['description']) ?></textarea><br>
        <input name="year" type="number" value="<?= htmlspecialchars($book['published_year']) ?>" placeholder="Année"><br>

        <label>Auteurs :</label>
        <select name="authors[]" multiple style="height:100px;">
            <?php foreach ($allAuthors as $author): ?>
                <option value="<?= $author['id'] ?>" <?= in_array($author['id'], $bookAuthorIds) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($author['first_name'] . ' ' . $author['last_name']) ?>
                </option>
            <?php endforeach; ?>
        </select><br>

        <button type="button" onclick="document.getElementById('authorModal').style.display='block'">+ Ajouter un auteur</button><br><br>

        <fieldset>
            <legend>Catégories</legend>
            <?php foreach ($allCategories as $cat): ?>
                <label><input type="checkbox" name="categories[]" value="<?= $cat['id'] ?>"
                    <?= in_array($cat['id'], $bookCategoryIds) ? 'checked' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </label><br>
            <?php endforeach; ?>
        </fieldset>

        <label>Image du livre :</label>
        <input type="file" name="image_path" accept="image/*">
        <?php if (!empty($book['image_path'])): ?>
            <img src="<?= htmlspecialchars($book['image_path']) ?>" alt="Image" style="height:80px;">
        <?php endif; ?>

        <button type="submit">Enregistrer</button>
    </form>

    <!-- MODAL pour ajout auteur -->
    <div id="authorModal" style="display:none; position:fixed; top:20%; left:50%; transform:translateX(-50%); background:#fff; padding:20px; border:1px solid #ccc; box-shadow:0 0 10px #aaa; z-index:1000;">
        <h3>Ajouter un auteur</h3>
        <form method="POST">
            <input type="text" name="new_author_first_name" placeholder="Prénom" required><br>
            <input type="text" name="new_author_last_name" placeholder="Nom" required><br>
            <button type="submit" name="add_author">Ajouter</button>
            <button type="button" onclick="document.getElementById('authorModal').style.display='none'">Annuler</button>
        </form>
    </div>

    <p><a href="books.php">Retour aux livres</a></p>
</div>
</body>
</html>