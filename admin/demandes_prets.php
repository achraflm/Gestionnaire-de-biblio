<?php
require_once 'header.php';

if (isset($_GET['action']) && isset($_GET['id'])) {
    $id_reservation = (int)$_GET['id'];
    $action = $_GET['action'];
    
    // Recuperer infos reservation
    $sql_res = "SELECT * FROM reservation WHERE ID_Reservation = $id_reservation";
    $res_res = mysqli_query($connexion, $sql_res);
    $reservation = mysqli_fetch_assoc($res_res);
    $isbn = $reservation['ISBN'];
    $id_etudiant = $reservation['ID_Etudiant'];
    
    if ($action == 'valider') {
        // 1. Trouver un exemplaire disponible
        $sql_exemplaire = "SELECT ID_Exemplaire FROM exemplaire 
                           WHERE ISBN = '$isbn' 
                           AND Etat = 'bon etat'
                           AND ID_Exemplaire NOT IN (SELECT ID_Exemplaire FROM emprunt WHERE Date_Retour IS NULL) 
                           LIMIT 1";
        $res_exemplaire = mysqli_query($connexion, $sql_exemplaire);
        
        if (mysqli_num_rows($res_exemplaire) > 0) {
            $exemplaire = mysqli_fetch_assoc($res_exemplaire);
            $id_exemplaire = $exemplaire['ID_Exemplaire'];
            
            $date_emprunt = date('Y-m-d H:i:s');
            $date_retour_prevu = date('Y-m-d H:i:s', strtotime('+15 days'));
            
            // 2. Creer l'emprunt
            $sql_insert = "INSERT INTO emprunt (ID_Etudiant, ID_Exemplaire, Date_Emprunt, Date_Retour_Prevu) 
                           VALUES ($id_etudiant, $id_exemplaire, '$date_emprunt', '$date_retour_prevu')";
            
            if (mysqli_query($connexion, $sql_insert)) {
                // 3. Mettre a jour la reservation
                $sql_update = "UPDATE reservation SET Statut = 'acceptee' WHERE ID_Reservation = $id_reservation";
                mysqli_query($connexion, $sql_update);
            }
        } else {
            redirection("demandes_prets.php?erreur=stock");
        }
        
    } elseif ($action == 'refuser') {
        $sql = "UPDATE reservation SET Statut = 'refusee' WHERE ID_Reservation = $id_reservation";
        mysqli_query($connexion, $sql);
    }
    redirection("demandes_prets.php");
}

// liste des demandes en attente (Table reservation)
$requete = "SELECT r.*, l.Titre, l.ISBN, s.Nom, s.Prenom,
            (SELECT COUNT(*) FROM exemplaire WHERE ISBN = l.ISBN) as total_copies,
            (SELECT COUNT(*) FROM emprunt e JOIN exemplaire ex ON e.ID_Exemplaire = ex.ID_Exemplaire WHERE ex.ISBN = l.ISBN AND e.Date_Retour IS NULL) as borrowed_copies
            FROM reservation r 
            JOIN livre l ON r.ISBN = l.ISBN 
            JOIN student s ON r.ID_Etudiant = s.ID_Etudiant 
            WHERE r.Statut = 'en attente'
            ORDER BY r.Date_Reservation ASC";
$resultat = mysqli_query($connexion, $requete);
?>

<h2>Demandes de Prets & File d'Attente</h2>

<?php
if (isset($_GET['erreur']) && $_GET['erreur'] == 'stock') {
    echo '<div class="alert alert-danger">Aucun exemplaire disponible pour ce livre.</div>';
}
?>

<table>
    <thead>
        <tr>
            <th>Etudiant</th>
            <th>Livre</th>
            <th>Dispo</th>
            <th>Date Demande</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($resultat && mysqli_num_rows($resultat) > 0) {
            while ($row = mysqli_fetch_assoc($resultat)) {
                $dispo = $row['total_copies'] - $row['borrowed_copies'];
                $is_available = ($dispo > 0);

                echo '<tr>';
                echo '<td>' . $row['Nom'] . ' ' . $row['Prenom'] . '</td>';
                echo '<td>' . $row['Titre'] . '</td>';
                
                if ($is_available) {
                    echo '<td style="color:green; font-weight:bold;">Oui (' . $dispo . ')</td>';
                } else {
                    echo '<td style="color:orange; font-weight:bold;">Non (File d\'attente)</td>';
                }

                echo '<td>' . $row['Date_Reservation'] . '</td>';
                echo '<td>';
                
                if ($is_available) {
                    echo '<a href="demandes_prets.php?action=valider&id=' . $row['ID_Reservation'] . '" class="btn-primary">Valider</a> ';
                } else {
                    echo '<button disabled style="background:gray; color:white; border:none; padding:5px;">En attente</button> ';
                }
                
                echo '<a href="demandes_prets.php?action=refuser&id=' . $row['ID_Reservation'] . '" class="btn-danger">Refuser</a>';
                echo '</td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="5">Aucune demande en attente.</td></tr>';
        }
        ?>
    </tbody>
</table>

</div>
</body>
</html>
