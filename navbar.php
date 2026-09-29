<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<nav>
    <a href="dashboard.php">Accueil</a>
    <a href="books.php">Livres</a>
      <a href="auteurs.php">Auteurs</a>

    <?php if (isset($_SESSION['user'])): ?>
        <a href="mon_compte.php">Mon compte</a>

        <?php if ($_SESSION['user']['role'] === 'admin'): ?>
            <a href="add_book.php">Ajouter un livre</a>
            <a href="admin_users.php">Utilisateurs</a>
            <a href="stats.php">Statistiques</a>
        <?php endif; ?>

        <a href="logout.php">Déconnexion</a>
    <?php else: ?>
        <a href="login.php">Connexion</a>
        <a href="register.php">Inscription</a>
    <?php endif; ?>
</nav>
