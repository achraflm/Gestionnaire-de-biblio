<?php
require_once 'header.php';

// Traitement Validation
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id_emprunt = (int)$_GET['id'];
    
    if ($_GET['action'] == 'valider') {
        // Ajouter 15 jours a la date de retour prevue et reset le flag
        $sql = "UPDATE emprunt SET Date_Retour_Prevu = DATE_ADD(Date_Retour_Prevu, INTERVAL 15 DAY), Prolongation_Demande = 0 WHERE ID_Emprunt = $id_emprunt";
        mysqli_query($connexion, $sql);
    } elseif ($_GET['action'] == 'refuser') {
        // Juste reset le flag
        $sql = "UPDATE emprunt SET Prolongation_Demande = 0 WHERE ID_Emprunt = $id_emprunt";
        mysqli_query($connexion, $sql);
    }
    redirection("prolongations.php");
}

// Liste des demandes
$sql = "SELECT e.*, l.Titre, s.Nom, s.Prenom 
        FROM emprunt e 
        JOIN exemplaire ex ON e.ID_Exemplaire = ex.ID_Exemplaire
        JOIN livre l ON ex.ISBN = l.ISBN 
        JOIN student s ON e.ID_Etudiant = s.ID_Etudiant 
        WHERE e.Prolongation_Demande = 1";
$resultat = mysqli_query($connexion, $sql);
?>

<h2>Demandes de Prolongation</h2>

<table>
    <thead>
        <tr>
            <th>Etudiant</th>
            <th>Livre</th>
            <th>Date Emprunt</th>
            <th>Retour Prevu Actuel</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($resultat && mysqli_num_rows($resultat) > 0) {
            while ($row = mysqli_fetch_assoc($resultat)) {
                echo '<tr>';
                echo '<td>' . $row['Nom'] . ' ' . $row['Prenom'] . '</td>';
                echo '<td>' . $row['Titre'] . '</td>';
                echo '<td>' . $row['Date_Emprunt'] . '</td>';
                echo '<td>' . $row['Date_Retour_Prevu'] . '</td>';
                echo '<td>';
                echo '<a href="prolongations.php?action=valider&id=' . $row['ID_Emprunt'] . '" class="btn-primary">Accepter (+15j)</a> ';
                echo '<a href="prolongations.php?action=refuser&id=' . $row['ID_Emprunt'] . '" class="btn-danger">Refuser</a>';
                echo '</td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="5">Aucune demande.</td></tr>';
        }
        ?>
    </tbody>
</table>

</div>
</body>
</html>
