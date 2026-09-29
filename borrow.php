<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Emprunter un livre - Ma Bibliothèque</title>
    <link rel="stylesheet" href="style.css" />
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #f7f9fc;
            color: #2c3e50;
            text-align: center;
        }
        a {
            color: #2980b9;
            text-decoration: none;
            font-weight: 600;
        }
        .success {
            color: #27ae60;
            font-size: 1.2em;
            margin-bottom: 20px;
        }
        .error {
            color: #c0392b;
            font-size: 1.2em;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<?php 
session_start();
require 'config.php';
include 'navbar.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_POST['book_id']) || empty($_POST['book_id'])) {
    echo "<p class='error'>ID de livre manquant.</p>";
    exit;
}

$bookId = (int)$_POST['book_id'];
$userId = $_SESSION['user']['id'];

// Vérifier disponibilité
$stmt = $pdo->prepare("SELECT * FROM Books WHERE id = ?");
$stmt->execute([$bookId]);
$book = $stmt->fetch();

if (!$book) {
    echo "<p class='error'>Livre introuvable.</p>";
    exit;
}

if ($book['status'] !== 'disponible') {
    echo "<p class='error'>❌ Livre non disponible.</p>";
    exit;
}

// Emprunt
$pdo->prepare("INSERT INTO Borrows (user_id, book_id) VALUES (?, ?)")->execute([$userId, $bookId]);
$pdo->prepare("UPDATE Books SET status = 'emprunté' WHERE id = ?")->execute([$bookId]);

echo "<p class='success'>✅ Livre emprunté avec succès.</p>";
echo "<a href='dashboard.php'>Retour au tableau de bord</a>";
?>

</body>
</html>