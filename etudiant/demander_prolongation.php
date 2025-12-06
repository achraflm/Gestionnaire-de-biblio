<?php
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

if (!est_etudiant()) {
    redirection('../index.php');
}

if (isset($_GET['id'])) {
    $id_emprunt = (int)$_GET['id'];
    $id_etudiant = $_SESSION['user_id'];

    // Verifier que l'emprunt appartient a l'etudiant
    $check = "SELECT * FROM emprunt WHERE ID_Emprunt = $id_emprunt AND ID_Etudiant = $id_etudiant AND Date_Retour IS NULL";
    $res = mysqli_query($connexion, $check);

    if (mysqli_num_rows($res) == 1) {
        // Marquer la demande de prolongation
        $sql = "UPDATE emprunt SET Prolongation_Demande = 1 WHERE ID_Emprunt = $id_emprunt";
        if (mysqli_query($connexion, $sql)) {
            redirection("mes_emprunts.php?msg=prolongation_ok");
        } else {
            echo "Erreur SQL.";
        }
    } else {
        echo "Emprunt non valide ou deja retourne.";
    }
} else {
    redirection("mes_emprunts.php");
}
?>
