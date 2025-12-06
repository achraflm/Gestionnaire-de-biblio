<?php
require_once 'header.php';

if (isset($_GET['action']) && isset($_GET['id'])) {
    $id_emprunt = (int)$_GET['id'];
    
    if ($_GET['action'] == 'retourner') {
        $date_retour = date('Y-m-d H:i:s');
        
        // 1. Recuperer ISBN pour verifier file d'attente
        $sql_isbn = "SELECT ex.ISBN 
                     FROM emprunt e 
                     JOIN exemplaire ex ON e.ID_Exemplaire = ex.ID_Exemplaire 
                     WHERE e.ID_Emprunt = $id_emprunt";
        $res_isbn = mysqli_query($connexion, $sql_isbn);
        $data_isbn = mysqli_fetch_assoc($res_isbn);
        $isbn = $data_isbn['ISBN'];

        // 2. Mise a jour Date_Retour (Marquer comme rendu)
        $sql = "UPDATE emprunt SET Date_Retour = '$date_retour', Etat_Retour = 'bon' WHERE ID_Emprunt = $id_emprunt";
        mysqli_query($connexion, $sql);
        
        // 3. Verifier reservations en attente
        $sql_wait = "SELECT COUNT(*) as nb, s.Nom, s.Prenom 
                     FROM reservation r 
                     JOIN student s ON r.ID_Etudiant = s.ID_Etudiant
                     WHERE r.ISBN = '$isbn' AND r.Statut = 'en attente'
                     ORDER BY r.Date_Reservation ASC LIMIT 1";
        $res_wait = mysqli_query($connexion, $sql_wait);
        $data_wait = mysqli_fetch_assoc($res_wait);

                if ($data_wait['nb'] > 0) {

                    // Recuperer ID_Etudiant et Titre du livre pour le message d'alerte

                    $sql_student_info = "SELECT r.ID_Etudiant, s.Nom, s.Prenom, l.Titre 

                                         FROM reservation r

                                         JOIN student s ON r.ID_Etudiant = s.ID_Etudiant

                                         JOIN livre l ON r.ISBN = l.ISBN

                                         WHERE r.ISBN = '$isbn' AND r.Statut = 'en attente'

                                         ORDER BY r.Date_Reservation ASC LIMIT 1";

                    $res_student_info = mysqli_query($connexion, $sql_student_info);

                    $data_student_info = mysqli_fetch_assoc($res_student_info);

        

                    $id_student_next_in_line = $data_student_info['ID_Etudiant'];

                    $student_name = $data_student_info['Nom'] . " " . $data_student_info['Prenom'];

                    $book_title = $data_student_info['Titre'];

        

                    $msg = "Livre retourne. ⚠ ATTENTION: Ce livre est reserve par " . $student_name . " (File d'attente). Ne pas remettre en rayon !";

                    $alert_type = "alert-warning";

        

                    // Envoi de l'alerte au student

                    $alert_message_content = "Le livre '" . $book_title . "' que vous avez reserve est maintenant disponible. Veuillez le recuperer a la bibliotheque.";

                    $sql_insert_alert = "INSERT INTO message (ID_Etudiant, Sens, Contenu, Lu) VALUES ($id_student_next_in_line, 'admin-vers-etudiant', '$alert_message_content', 0)";

                    mysqli_query($connexion, $sql_insert_alert);

        

                } else {

                    $msg = "Livre retourne avec succes.";

                    $alert_type = "alert-success";

                }

        

                // On redirige avec le message en parametre (encodé) pour l'afficher
        redirection("retours.php?msg=" . urlencode($msg) . "&type=" . $alert_type);
    } elseif ($_GET['action'] == 'penaliser' && isset($_GET['id_student'])) {
        $id_student = (int)$_GET['id_student'];
        // Reduire le score de l'etudiant (ex: -10 points)
        $sql_penalty = "UPDATE student SET Score = Score - 10 WHERE ID_Etudiant = $id_student";
        mysqli_query($connexion, $sql_penalty);
        $msg = "Penalite de 10 points appliquee a l'etudiant.";
        redirection("retours.php?msg=" . urlencode($msg) . "&type=alert-danger");
    }
}

// Liste des emprunts EN COURS (Date_Retour IS NULL)
$requete = "SELECT e.*, l.Titre, s.Nom, s.Prenom 
            FROM emprunt e 
            JOIN exemplaire ex ON e.ID_Exemplaire = ex.ID_Exemplaire
            JOIN livre l ON ex.ISBN = l.ISBN 
            JOIN student s ON e.ID_Etudiant = s.ID_Etudiant 
            WHERE e.Date_Retour IS NULL";
$resultat = mysqli_query($connexion, $requete);
?>

<h2>Gestion des Retours</h2>

<?php
if (isset($_GET['msg'])) {
    $type = isset($_GET['type']) ? $_GET['type'] : 'alert-success';
    // urldecode pour afficher les espaces et accents correctement
    echo '<div class="alert ' . $type . '" style="font-weight:bold;">' . urldecode($_GET['msg']) . '</div>';
}
?>

<table>
    <thead>
        <tr>
            <th>Etudiant</th>
            <th>Livre</th>
            <th>Date Emprunt</th>
            <th>Retour Prevu</th>
            <th>Statut</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($resultat && mysqli_num_rows($resultat) > 0) {
            while ($row = mysqli_fetch_assoc($resultat)) {
                $is_retard = (strtotime($row['Date_Retour_Prevu']) < time());
                $row_style = $is_retard ? 'background-color:#f8d7da; color:#721c24; font-weight:bold;' : '';
                $statut_text = $is_retard ? 'Retard' : 'En cours';
                
                echo '<tr style="' . $row_style . '">';
                echo '<td>' . $row['Nom'] . ' ' . $row['Prenom'] . '</td>';
                echo '<td>' . $row['Titre'] . '</td>';
                echo '<td>' . $row['Date_Emprunt'] . '</td>';
                echo '<td>' . $row['Date_Retour_Prevu'] . '</td>';
                echo '<td>' . $statut_text . '</td>';
                echo '<td>';
                echo '<a href="retours.php?action=retourner&id=' . $row['ID_Emprunt'] . '" class="btn-primary" onclick="return confirm(\'Confirmer le retour du livre ?\')">Marquer comme retourne</a> ';
                if ($is_retard) {
                    echo '<a href="retours.php?action=penaliser&id_student=' . $row['ID_Etudiant'] . '" class="btn-danger" onclick="return confirm(\'Penaliser cet etudiant (Retirer 10 points) ?\')">Pénaliser</a>';
                }
                echo '</td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="6">Aucun emprunt en cours.</td></tr>';
        }
        ?>
    </tbody>
</table>

</div>
</body>
</html>
