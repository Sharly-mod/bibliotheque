<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Ma Bibliothèque</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>

<?php
// Copie ce fichier en config.php et renseigne tes identifiants locaux :
// config.php est ignoré par git.

$host = 'localhost';
$db   = 'bibliotheque';
$user = 'ton_utilisateur';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
