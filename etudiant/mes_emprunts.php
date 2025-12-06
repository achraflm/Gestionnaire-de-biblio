<?php
require_once 'header.php';

$id_etudiant = $_SESSION['user_id'];

// 1. Reservations (En attente)
$sql_res = "SELECT r.*, l.Titre, l.ISBN, 
            (SELECT COUNT(*) FROM exemplaire WHERE ISBN = l.ISBN) as total_copies,
            (SELECT COUNT(*) FROM emprunt e JOIN exemplaire ex ON e.ID_Exemplaire = ex.ID_Exemplaire WHERE ex.ISBN = l.ISBN AND e.Date_Retour IS NULL) as borrowed_copies
            FROM reservation r 
            JOIN livre l ON r.ISBN = l.ISBN 
            WHERE r.ID_Etudiant = $id_etudiant 
            ORDER BY r.Date_Reservation DESC";
$res_reservations = mysqli_query($connexion, $sql_res);

// 2. Emprunts (Historique)
$sql_emp = "SELECT e.*, l.Titre, ex.ISBN 
            FROM emprunt e 
            JOIN exemplaire ex ON e.ID_Exemplaire = ex.ID_Exemplaire 
            JOIN livre l ON ex.ISBN = l.ISBN 
            WHERE e.ID_Etudiant = $id_etudiant 
            ORDER BY e.Date_Emprunt DESC";
$res_emprunts = mysqli_query($connexion, $sql_emp);
?>

<h2>Mes Demandes (Reservations)</h2>
<table>
    <thead>
        <tr>
            <th>Livre</th>
            <th>Date Demande</th>
            <th>Statut Demande</th>
            <th>Disponibilite Actuelle</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($res_reservations && mysqli_num_rows($res_reservations) > 0) {
            while ($row = mysqli_fetch_assoc($res_reservations)) {
                $current_dispo = $row['total_copies'] - $row['borrowed_copies'];
                $dispo_status = ($current_dispo > 0) ? '<span style="color:green;font-weight:bold;">Disponible pour emprunt (' . $current_dispo . ' ex.)</span>' : '<span style="color:orange;font-weight:bold;">En file d\'attente</span>';
                
                echo '<tr>';
                echo '<td>' . $row['Titre'] . '</td>';
                echo '<td>' . $row['Date_Reservation'] . '</td>';
                $color = ($row['Statut'] == 'en attente') ? 'orange' : (($row['Statut'] == 'acceptee') ? 'green' : 'red');
                echo '<td style="color:' . $color . ';">' . ucfirst($row['Statut']) . '</td>';
                echo '<td>' . $dispo_status . '</td>';
                echo '<td>';
                if ($row['Statut'] == 'en attente') {
                    echo '<a href="annuler_demande.php?id=' . $row['ID_Reservation'] . '" class="btn-danger" style="text-decoration:none; font-size:12px; padding:5px;" onclick="return confirm(\'Annuler cette demande ?\')">Annuler</a>';
                }
                echo '</td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="5">Aucune demande.</td></tr>';
        }
        ?>
    </tbody>
</table>

<h2>Mes Emprunts</h2>
<table>
    <thead>
        <tr>
            <th>Livre</th>
            <th>Date Emprunt</th>
            <th>Retour Prevu</th>
            <th>Date Retour</th>
            <th>Statut</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($res_emprunts && mysqli_num_rows($res_emprunts) > 0) {
            while ($row = mysqli_fetch_assoc($res_emprunts)) {
                $date_retour = $row['Date_Retour'];
                $statut = 'En cours';
                $color = 'green';
                
                if ($date_retour) {
                    $statut = 'Retourne';
                    $color = 'gray';
                } elseif (strtotime($row['Date_Retour_Prevu']) < time()) {
                    $statut = 'Retard';
                    $color = 'red';
                }
                
                echo '<tr>';
                echo '<td>' . $row['Titre'] . '</td>';
                echo '<td>' . $row['Date_Emprunt'] . '</td>';
                echo '<td>' . $row['Date_Retour_Prevu'] . '</td>';
                echo '<td>' . ($date_retour ? $date_retour : '-') . '</td>';
                echo '<td style="color:' . $color . ';">' . $statut . '</td>';
                echo '<td>';
                if (!$date_retour) {
                     echo '<a href="generer_pdf.php?id=' . $row['ID_Emprunt'] . '" target="_blank" class="btn-primary" style="text-decoration:none; font-size:12px;">Recu PDF</a> ';
                     
                     if ($row['Prolongation_Demande'] == 0) {
                        echo '<a href="demander_prolongation.php?id=' . $row['ID_Emprunt'] . '" class="btn-warning" style="text-decoration:none; font-size:12px; background:orange; color:white;">Prolonger</a>';
                     } else {
                         echo '<span style="font-size:12px; color:orange;">Prolongation en attente</span>';
                     }
                } else {
                    // Livre retourne, permettre evaluation
                    // Verifier si l'evaluation existe deja
                    $isbn_livre_eval = $row['ISBN']; // Assurez-vous que l'ISBN est recupere dans la requete sql_emp
                    $sql_check_eval = "SELECT COUNT(*) as nb_eval FROM evaluation WHERE ID_Etudiant = $id_etudiant AND ISBN = '$isbn_livre_eval'";
                    $res_check_eval = mysqli_query($connexion, $sql_check_eval);
                    $data_check_eval = mysqli_fetch_assoc($res_check_eval);

                    if ($data_check_eval['nb_eval'] == 0) {
                        echo '<a href="evaluer_livre.php?isbn=' . $isbn_livre_eval . '" class="btn-primary" style="text-decoration:none; font-size:12px; background:#17a2b8; color:white;">Évaluer</a>';
                    } else {
                        echo '<span style="font-size:12px; color:gray;">Déjà évalué</span>';
                    }
                }
                echo '</td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="6">Aucun emprunt.</td></tr>';
        }
        ?>
    </tbody>
</table>

</div>
</body>
</html>
