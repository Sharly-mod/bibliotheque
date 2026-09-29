<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Liste des utilisateurs - Ma Bibliothèque</title>
    <link rel="stylesheet" href="style.css" />
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 900px;
            margin: 20px auto;
            padding: 0 15px;
            background-color: #f9f9f9;
            color: #333;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #2c3e50;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 2px 5px rgb(0 0 0 / 0.1);
        }
        th, td {
            padding: 12px 15px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #3498db;
            color: white;
            font-weight: 600;
        }
        tr:nth-child(even) {
            background-color: #f4f6f8;
        }
        tr:hover {
            background-color: #d0e7ff;
        }
        a.return-link {
            display: inline-block;
            margin-top: 15px;
            padding: 8px 12px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }
        a.return-link:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>

<?php 
session_start();
include 'navbar.php';
require 'config.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    exit("Accès interdit");
}

$stmt = $pdo->query("SELECT id, first_name, last_name, email, role FROM Users");
$users = $stmt->fetchAll();
?>

<h2>Liste des utilisateurs</h2>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nom complet</th>
            <th>Email</th>
            <th>Rôle</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $u): ?>
        <tr>
            <td><?= htmlspecialchars($u['id']) ?></td>
            <td><?= htmlspecialchars($u['first_name'] . ' ' . $u['last_name']) ?></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td><?= htmlspecialchars($u['role']) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<a href="dashboard.php" class="return-link">← Retour</a>

</body>
</html>