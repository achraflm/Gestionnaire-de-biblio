<?php
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

if (!est_etudiant()) {
    redirection('../index.php');
}

if (isset($_GET['id_message'])) {
    $id_message = (int)$_GET['id_message'];
    $id_etudiant = $_SESSION['user_id'];

    // Marquer le message comme lu si il appartient a l'etudiant
    $sql = "UPDATE message SET Lu = 1 WHERE ID_Message = $id_message AND ID_Etudiant = $id_etudiant";
    mysqli_query($connexion, $sql);
}

redirection("index.php"); // Rediriger toujours vers le tableau de bord
?>
