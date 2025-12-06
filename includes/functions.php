<?php

// fonction pour verifier si lutilisateur est connecte
function est_connecte() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['user_id']);
}

// fonction pour verifier si lutilisateur est admin
function est_admin() {
    if (est_connecte()) {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }
    return false;
}

// fonction pour verifier si lutilisateur est etudiant
function est_etudiant() {
    if (est_connecte()) {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'etudiant';
    }
    return false;
}

// fonction de redirection
function redirection($url) {
    header("Location: " . $url);
    exit();
}

// nettoyage des donnees
function nettoyer_donnees($donnee) {
    global $connexion;
    $donnee = trim($donnee);
    $donnee = stripslashes($donnee);
    $donnee = htmlspecialchars($donnee);
    return mysqli_real_escape_string($connexion, $donnee);
}

?>
