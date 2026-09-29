<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_POST['book_id']) || empty($_POST['book_id'])) {
    die("ID de livre manquant.");
}

$bookId = (int)$_POST['book_id'];
$userId = $_SESSION['user']['id'];

$rating = isset($_POST['rating']) ? (int)$_POST['rating'] : null;
if ($rating !== null && ($rating < 1 || $rating > 5)) {
    die("Note invalide.");
}

// Mettre à jour la date de retour et la note dans la table Borrows
$stmt = $pdo->prepare("UPDATE Borrows SET return_date = NOW(), rating = :rating WHERE user_id = :user_id AND book_id = :book_id AND return_date IS NULL");
$stmt->execute([
    ':rating' => $rating,
    ':user_id' => $userId,
    ':book_id' => $bookId
]);

// Mise à jour du statut du livre à disponible
$pdo->prepare("UPDATE Books SET status = 'disponible' WHERE id = ?")->execute([$bookId]);

header("Location: mon_compte.php");
exit;