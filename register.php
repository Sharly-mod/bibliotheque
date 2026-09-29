<?php
require 'config.php';
include 'navbar.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['first_name']);
    $lastName = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Vérifier si email déjà utilisé
    $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM Users WHERE email = ?");
    $stmtCheck->execute([$email]);
    if ($stmtCheck->fetchColumn() > 0) {
        echo "<p style='color:red;'>❌ Cet email est déjà utilisé.</p>";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO Users (first_name, last_name, email, password_hash, role) VALUES (?, ?, ?, ?, 'user')");
        try {
            $stmt->execute([$firstName, $lastName, $email, $hashedPassword]);
            echo "<p style='color:green;'>✅ Inscription réussie. <a href='login.php'>Se connecter</a></p>";
        } catch (PDOException $e) {
            echo "<p style='color:red;'>❌ Erreur : " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }
}
?>

<h2>Inscription</h2>
<form method="POST" onsubmit="return validateForm()">
    <input name="first_name" required placeholder="Prénom" /><br>
    <input name="last_name" required placeholder="Nom" /><br>
    <input name="email" type="email" required placeholder="Email" /><br>
    <input name="password" type="password" required placeholder="Mot de passe" /><br>
    <button type="submit">S'inscrire</button>
</form>

<script>
function validateForm() {
    const pwd = document.querySelector('input[name="password"]').value;
    if (pwd.length < 6) {
        alert("Le mot de passe doit contenir au moins 6 caractères.");
        return false;
    }
    return true;
}
</script>