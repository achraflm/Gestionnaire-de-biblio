<?php
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = nettoyer_donnees($_POST['nom']);
    $prenom = nettoyer_donnees($_POST['prenom']);
    // $cne = nettoyer_donnees($_POST['cne']); // Pas de CNE dans la table student
    $email = nettoyer_donnees($_POST['email']);
    $mot_de_passe = $_POST['mot_de_passe']; // Pas de MD5

    // verification email existant
    $check_query = "SELECT * FROM student WHERE Email = '$email'";
    $check_result = mysqli_query($connexion, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        header("Location: inscription.php?erreur=email_existe");
        exit();
    }

    // insertion
    $requete = "INSERT INTO student (Nom, Prenom, Email, Mot_de_passe, Statut) VALUES ('$nom', '$prenom', '$email', '$mot_de_passe', 'actif')";

    if (mysqli_query($connexion, $requete)) {
        header("Location: index.php?succes=1");
    } else {
        header("Location: inscription.php?erreur=sql");
    }
} else {
    header("Location: inscription.php");
}
?>
