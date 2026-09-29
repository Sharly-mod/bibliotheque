<?php
session_start();
require 'config.php';

// Vérifier que l'utilisateur est connecté et admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('HTTP/1.1 403 Forbidden');
    exit('Accès refusé');
}

// Vérifier la présence de l'ID du message
if (!isset($_POST['id']) || empty($_POST['id'])) {
    die("ID de message manquant.");
}

$messageId = (int)$_POST['id'];

// Préparer et exécuter la suppression
$stmt = $pdo->prepare("DELETE FROM Messages WHERE id = ?");
$stmt->execute([$messageId]);

// Redirection après suppression
header('Location: dashboard.php');
exit;
