<?php
session_start();
require 'config.php';
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

if (!empty($_POST['message'])) {
    $stmt = $pdo->prepare("INSERT INTO Messages (sender_id, content, sent_at) VALUES (?, ?, NOW())");
    $stmt->execute([$_SESSION['user']['id'], $_POST['message']]);
}

header("Location: dashboard.php");
exit;