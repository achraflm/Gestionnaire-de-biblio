<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription - Bibliotheque</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1>Gestion de Bibliotheque</h1>
    </header>
    <div class="container">
        <h2>Inscription Etudiant</h2>
        <?php
        if (isset($_GET['erreur'])) {
            echo '<div class="alert alert-danger">Une erreur est survenue (Email deja utilise ?).</div>';
        }
        ?>
        <form action="auth_register.php" method="POST">
            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom" required>

            <label for="prenom">Prenom :</label>
            <input type="text" id="prenom" name="prenom" required>

            <!-- CNE supprimé car absent de la nouvelle base -->

            <label for="email">Email :</label>
            <input type="email" id="email" name="email" required>

            <label for="mot_de_passe">Mot de passe :</label>
            <input type="password" id="mot_de_passe" name="mot_de_passe" required>

            <input type="submit" value="S'inscrire">
        </form>
        <p>Deja un compte ? <a href="index.php">Se connecter</a></p>
    </div>
</body>
</html>
