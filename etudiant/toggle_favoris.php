<?php
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

if (!est_etudiant()) {
    redirection('../index.php');
}

if (isset($_GET['isbn'])) {
    $isbn = nettoyer_donnees($_GET['isbn']);
    $id_etudiant = $_SESSION['user_id'];

    // Verifier si deja en favoris
    $check = "SELECT * FROM favoris WHERE ID_Etudiant = $id_etudiant AND ISBN = '$isbn'";
    $res = mysqli_query($connexion, $check);

    if (mysqli_num_rows($res) > 0) {
        // Si existe -> Supprimer
        $sql = "DELETE FROM favoris WHERE ID_Etudiant = $id_etudiant AND ISBN = '$isbn'";
        mysqli_query($connexion, $sql);
    } else {
        // Si n'existe pas -> Ajouter
        $sql = "INSERT INTO favoris (ID_Etudiant, ISBN) VALUES ($id_etudiant, '$isbn')";
        mysqli_query($connexion, $sql);
    }

    // Redirection vers la page precedente
    if (isset($_SERVER['HTTP_REFERER'])) {
        header("Location: " . $_SERVER['HTTP_REFERER']);
    } else {
        redirection("livres.php");
    }
} else {
    redirection("livres.php");
}
?>
