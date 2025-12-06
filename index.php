<?php
require_once 'includes/functions.php';

if (est_connecte()) {
    if (est_admin()) {
        redirection('admin/index.php');
    } else {
        redirection('etudiant/index.php');
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - Bibliotheque</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1>Gestion de Bibliotheque</h1>
    </header>
    <div class="container">
        <h2>Connexion</h2>
        <?php
        if (isset($_GET['erreur'])) {
            echo '<div class="alert alert-danger">Email ou mot de passe incorrect, ou compte bloque.</div>';
        }
        if (isset($_GET['succes'])) {
            echo '<div class="alert alert-success">Inscription reussie. Connectez-vous.</div>';
        }
        ?>
        <form action="auth_login.php" method="POST">
            <label for="email">Email :</label>
            <input type="email" id="email" name="email" required>

            <label for="mot_de_passe">Mot de passe :</label>
            <input type="password" id="mot_de_passe" name="mot_de_passe" required>

            <input type="submit" value="Se connecter">
        </form>
        <p>Pas encore inscrit ? <a href="inscription.php">Creer un compte etudiant</a></p>
    </div>
</body>
</html>
