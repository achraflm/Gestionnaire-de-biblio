<?php
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // recuperation des donnees
    $email = nettoyer_donnees($_POST['email']);
    $mot_de_passe = $_POST['mot_de_passe']; // Mot de passe en clair selon la base ia_bib

    // 1. Verifier si c'est un admin
    $requete_admin = "SELECT * FROM admin WHERE Email = '$email' AND Mot_de_passe = '$mot_de_passe'";
    $resultat_admin = mysqli_query($connexion, $requete_admin);

    if (mysqli_num_rows($resultat_admin) == 1) {
        $admin = mysqli_fetch_assoc($resultat_admin);
        
        $_SESSION['user_id'] = $admin['ID_Admin'];
        $_SESSION['nom'] = $admin['Nom'];
        $_SESSION['prenom'] = $admin['Prenom'];
        $_SESSION['role'] = 'admin';

        header("Location: admin/index.php");
        exit();
    }

    // 2. Verifier si c'est un etudiant
    $requete_student = "SELECT * FROM student WHERE Email = '$email' AND Mot_de_passe = '$mot_de_passe'";
    $resultat_student = mysqli_query($connexion, $requete_student);

    if (mysqli_num_rows($resultat_student) == 1) {
        $student = mysqli_fetch_assoc($resultat_student);

        // verification statut (si 'restrict' = bloqué ?)
        if ($student['Statut'] == 'restrict') {
             header("Location: index.php?erreur=bloque");
             exit();
        }

        $_SESSION['user_id'] = $student['ID_Etudiant'];
        $_SESSION['nom'] = $student['Nom'];
        $_SESSION['prenom'] = $student['Prenom'];
        $_SESSION['role'] = 'etudiant';

        header("Location: etudiant/index.php");
        exit();
    }

    // echec
    header("Location: index.php?erreur=1");
    exit();

} else {
    header("Location: index.php");
    exit();
}
?>
