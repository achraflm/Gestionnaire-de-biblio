<?php
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

if (!est_etudiant()) {
    redirection('../index.php');
}

if (isset($_GET['id'])) {
    $id_reservation = (int)$_GET['id'];
    $id_etudiant = $_SESSION['user_id'];

    // Verifier que la reservation appartient bien a l'etudiant et qu'elle est "en attente"
    $check = "SELECT * FROM reservation WHERE ID_Reservation = $id_reservation AND ID_Etudiant = $id_etudiant AND Statut = 'en attente'";
    $res = mysqli_query($connexion, $check);

    if (mysqli_num_rows($res) == 1) {
        // Suppression de la demande (ou passage en 'annulee' si on veut garder l'historique)
        // Ici on supprime purement pour annuler
        $sql = "DELETE FROM reservation WHERE ID_Reservation = $id_reservation";
        if (mysqli_query($connexion, $sql)) {
            redirection("mes_emprunts.php?msg=annule");
        } else {
            echo "Erreur lors de l'annulation.";
        }
    } else {
        echo "Impossible d'annuler cette reservation (peut-etre deja traitee).";
    }
} else {
    redirection("mes_emprunts.php");
}
?>
