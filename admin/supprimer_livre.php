<?php
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

if (!est_admin()) {
    redirection('../index.php');
}

if (isset($_GET['isbn'])) {
    $isbn = nettoyer_donnees($_GET['isbn']);
    
    // Verification: Impossible de supprimer si un exemplaire est en cours d'emprunt (Date_Retour IS NULL)
    // On joint Emprunt -> Exemplaire -> Livre (ISBN)
    $check = "SELECT e.ID_Emprunt 
              FROM emprunt e 
              JOIN exemplaire ex ON e.ID_Exemplaire = ex.ID_Exemplaire 
              WHERE ex.ISBN = '$isbn' AND e.Date_Retour IS NULL";
              
    $res_check = mysqli_query($connexion, $check);
    
    if (mysqli_num_rows($res_check) > 0) {
        // On pourrait faire un joli message d'erreur, mais pour l'instant un die suffit
        die("<h1>Impossible de supprimer</h1><p>Ce livre a des exemplaires en cours d'emprunt. Veuillez attendre le retour de tous les exemplaires.</p><a href='gestion_livres.php'>Retour</a>");
    } else {
        // Suppression du livre. Les contraintes ON DELETE CASCADE de la BDD 
        // devraient supprimer automatiquement les exemplaires, les avis, etc.
        $requete = "DELETE FROM livre WHERE ISBN = '$isbn'";
        if (mysqli_query($connexion, $requete)) {
            redirection("gestion_livres.php");
        } else {
            die("Erreur SQL : " . mysqli_error($connexion));
        }
    }
} else {
    redirection("gestion_livres.php");
}
?>

