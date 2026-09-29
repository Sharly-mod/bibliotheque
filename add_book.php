<?php 
session_start();
require 'config.php';
include 'navbar.php';
// Vérifier le rôle admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    die("Accès refusé.");
}


$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $year = $_POST['year'];
    $authorFullName = trim($_POST['author']);

    // Insertion du livre
    $stmt = $pdo->prepare("INSERT INTO Books (title, description, published_year, status) VALUES (?, ?, ?, 'disponible')");
    $stmt->execute([$title, $description, $year]);
    $bookId = $pdo->lastInsertId();

    // Gestion auteur (séparation prénom et nom)
    $authorParts = explode(' ', $authorFullName, 2);
    $firstName = $authorParts[0];
    $lastName = $authorParts[1] ?? '';

    // Chercher auteur existant
    $stmt = $pdo->prepare("SELECT id FROM Authors WHERE first_name = ? AND last_name = ?");
    $stmt->execute([$firstName, $lastName]);
    $author = $stmt->fetch();

    if (!$author) {
        // Auteur inexistant : on l'ajoute
        $stmt = $pdo->prepare("INSERT INTO Authors (first_name, last_name) VALUES (?, ?)");
        $stmt->execute([$firstName, $lastName]);
        $authorId = $pdo->lastInsertId();
    } else {
        $authorId = $author['id'];
    }

    // Lier livre et auteur
    $stmt = $pdo->prepare("INSERT INTO Book_Author (book_id, author_id) VALUES (?, ?)");
    $stmt->execute([$bookId, $authorId]);

    $message = "Livre et auteur ajoutés avec succès !";
}
?>

<div style="max-width:600px; margin: auto;">
    <?php if ($message): ?>
        <p style="background: #d4edda; color: #155724; padding: 10px; border-radius: 5px;"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <h2>Ajouter un livre</h2>
    <form method="POST">
        <input name="title" placeholder="Titre" required style="width: 100%; padding: 8px; margin-bottom: 10px;"><br>
        <textarea name="description" placeholder="Description" style="width: 100%; padding: 8px; margin-bottom: 10px;"></textarea><br>
        <input name="year" type="number" placeholder="Année de publication" style="width: 100%; padding: 8px; margin-bottom: 10px;"><br>
        <input name="author" placeholder="Auteur/Autrice (Prénom Nom)" required style="width: 100%; padding: 8px; margin-bottom: 10px;"><br>
        <button type="submit" style="width: 100%; padding: 10px;">Ajouter</button>
    </form>
</div>
