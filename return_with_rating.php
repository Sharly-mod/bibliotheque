<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

if (empty($_POST['borrow_id']) || empty($_POST['rating'])) {
    die("Informations manquantes.");
}

$borrowId = (int)$_POST['borrow_id'];
$rating = (int)$_POST['rating'];
$userId = $_SESSION['user']['id'];

// Vérifier que l'emprunt appartient bien à l'utilisateur et est en cours
$stmt = $pdo->prepare("SELECT book_id FROM Borrows WHERE id = ? AND user_id = ? AND return_date IS NULL");
$stmt->execute([$borrowId, $userId]);
$borrow = $stmt->fetch();

if (!$borrow) {
    die("Emprunt introuvable ou déjà retourné.");
}

$bookId = $borrow['book_id'];

// Mettre à jour la date de retour
$pdo->prepare("UPDATE Borrows SET return_date = NOW(), rating = ? WHERE id = ?")->execute([$rating, $borrowId]);

// Mettre à jour le statut du livre
$pdo->prepare("UPDATE Books SET status = 'disponible' WHERE id = ?")->execute([$bookId]);

header("Location: dashboard.php");
exit;
