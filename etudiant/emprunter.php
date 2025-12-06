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
    
    // 1. Verifier la disponibilite reelle (Stock - Emprunts Actifs)
    // Pour savoir si c'est une demande standard ou une file d'attente
    $sql_stock = "SELECT 
                  (SELECT COUNT(*) FROM exemplaire WHERE ISBN = '$isbn') as total,
                  (SELECT COUNT(*) FROM emprunt e JOIN exemplaire ex ON e.ID_Exemplaire = ex.ID_Exemplaire WHERE ex.ISBN = '$isbn' AND e.Date_Retour IS NULL) as emprunts
                  ";
    $res_stock = mysqli_query($connexion, $sql_stock);
    $data_stock = mysqli_fetch_assoc($res_stock);
    $dispo = $data_stock['total'] - $data_stock['emprunts'];
    
    // 2. Si le livre est DISPONIBLE, on applique la limite de 2 reservations actives
    if ($dispo > 0) {
        // Compter les reservations "en attente" pour des livres QUI SONT DISPONIBLES (Demandes standard)
        // Note: C'est une approximation. Pour etre parfait, il faudrait verifier la dispo de chaque livre reserve par l'etudiant.
        // Simplification demandee: "2 active reservations". On compte simplement toutes les reservations 'en attente' de l'etudiant.
        
        $sql_count = "SELECT COUNT(*) as nb FROM reservation WHERE ID_Etudiant = $id_etudiant AND Statut = 'en attente'";
        $res_count = mysqli_query($connexion, $sql_count);
        $data_count = mysqli_fetch_assoc($res_count);
        
        if ($data_count['nb'] >= 2) {
            redirection("livres.php?erreur=limite");
        }
    }
    
    // 3. Verification doublon reservation (Toujours active)
    $check = "SELECT * FROM reservation WHERE ID_Etudiant = $id_etudiant AND ISBN = '$isbn' AND Statut = 'en attente'";
    $res_check = mysqli_query($connexion, $check);
    
    if (mysqli_num_rows($res_check) == 0) {
        // insertion demande (reservation)
        $requete = "INSERT INTO reservation (ID_Etudiant, ISBN, Statut) VALUES ($id_etudiant, '$isbn', 'en attente')";
        
        if (mysqli_query($connexion, $requete)) {
            redirection("livres.php?succes=1");
        } else {
            redirection("livres.php?erreur=sql");
        }
    } else {
        redirection("livres.php?erreur=doublon");
    }

} else {
    redirection("livres.php");
}
?>
