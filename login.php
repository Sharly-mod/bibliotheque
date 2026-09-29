<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Ma Bibliothèque - Connexion</title>
    <link rel="stylesheet" href="style.css" />
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 400px;
            margin: 60px auto;
            padding: 20px;
            background-color: #f7f9fc;
            color: #2c3e50;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border-radius: 12px;
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #34495e;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        input[type="email"],
        input[type="password"] {
            padding: 12px 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 1em;
            transition: border-color 0.3s ease;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: #2980b9;
            outline: none;
        }

        button {
            background-color: #2980b9;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #1c5980;
        }

        .error-message {
            color: #c0392b;
            font-weight: 600;
            text-align: center;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'config.php';
include 'navbar.php';

$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("SELECT * FROM Users WHERE email = ?");
    $stmt->execute([$_POST['email']]);
    $user = $stmt->fetch();

    if ($user && password_verify($_POST['password'], $user['password_hash'])) {
        $_SESSION['user'] = $user;
        header('Location: dashboard.php');
        exit;
    } else {
        $errorMessage = "❌ Email ou mot de passe incorrect.";
    }
}
?>

<h2>Connexion</h2>

<?php if ($errorMessage): ?>
    <p class="error-message"><?= htmlspecialchars($errorMessage) ?></p>
<?php endif; ?>

<form method="POST">
    <input type="email" name="email" required placeholder="Email" autofocus><br>
    <input type="password" name="password" required placeholder="Mot de passe"><br>
    <button type="submit">Connexion</button>
</form>

</body>
</html>