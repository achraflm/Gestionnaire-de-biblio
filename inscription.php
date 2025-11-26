<?php
session_start();
require_once 'config.php';

$error_message = "";

// Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['username']);
    $password = trim($_POST['password']);
    $role = $_POST['role']; // 'admin' ou 'etudiant'

    if ($role === 'admin') {
        // --- Vérification ADMIN ---
        $stmt = $pdo->prepare("SELECT * FROM admin WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Vérification du mot de passe (en clair pour ce prototype)
        if ($user && $password === $user['mot_de_passe']) {
            $_SESSION['user_id'] = $user['id_admin'];
            $_SESSION['nom'] = $user['nom'];
            $_SESSION['role'] = 'admin';
            
            header("Location: Partie_admin.php");
            exit();
        } else {
            $error_message = "Identifiants administrateur incorrects.";
        }

    } elseif ($role === 'etudiant') {
        // --- Vérification ÉTUDIANT ---
        $stmt = $pdo->prepare("SELECT * FROM etudiant WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            if ($user['statut'] === 'Bloqué') {
                $error_message = "Votre compte est bloqué. Contactez la bibliothèque.";
            } elseif ($password === $user['mot_de_passe']) {
                $_SESSION['user_id'] = $user['id_etudiant'];
                $_SESSION['nom'] = $user['prenom'] . " " . $user['nom'];
                $_SESSION['photo'] = $user['photo_profil'];
                $_SESSION['role'] = 'etudiant';

                header("Location: Partie_etudiant.php");
                exit();
            } else {
                $error_message = "Mot de passe incorrect.";
            }
        } else {
            $error_message = "Aucun compte étudiant trouvé avec cet email.";
        }
    }
}
?>

<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Connexion - Bibliothèque</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="login.css" />
    <link rel="stylesheet" href="general.css" />
</head>
<body>

<div class="login-container">
    <div style="font-size: 3rem; color: #ff9d00; margin-bottom: 10px;">
        <i class="fas fa-book-reader"></i>
    </div>

    <h2>Se connecter</h2>
    <span class="subtitle">Accédez à votre espace bibliothèque</span>

    <?php if (!empty($error_message)): ?>
        <div class="error-msg" style="color: red; margin-bottom: 15px; font-weight: bold;">
            <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error_message) ?>
        </div>
    <?php endif; ?>

    <form action="login.php" method="post" class="login-form">
        
        <label for="username">Email ou ID d'utilisateur</label>
        <input type="text" id="username" name="username" placeholder="Ex: sophie.martin@ecole.com" required value="<?= isset($email) ? htmlspecialchars($email) : '' ?>" />

        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password" placeholder="Votre mot de passe" required />

        <div class="role-selector">
            <label class="role-option">
                <input type="radio" name="role" value="etudiant" checked>
                <span><i class="fas fa-user-graduate"></i> Étudiant</span>
            </label>
            <label class="role-option">
                <input type="radio" name="role" value="admin">
                <span><i class="fas fa-user-shield"></i> Admin</span>
            </label>
        </div>

        <button type="submit" class="btn-login">
            Se connecter <i class="fas fa-arrow-right"></i>
        </button>
    </form>

    <div class="footer-link">
        <a href="#">Mot de passe oublié ?</a>
    </div>

    <div class="register-section" style="margin-top: 20px; padding-top: 15px; border-top: 1px solid #eee;">
        Pas encore de compte ? 
        <a href="inscription.php" style="color: #ff9d00; font-weight: bold; text-decoration: none;">S'inscrire ici</a>
    </div>

</div>

</body>
</html>