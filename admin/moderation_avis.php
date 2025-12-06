<?php
require_once 'header.php';

// Suppression d'un avis
if (isset($_GET['supprimer'])) {
    $id_eval = (int)$_GET['supprimer'];
    mysqli_query($connexion, "DELETE FROM evaluation WHERE ID_Evaluation = $id_eval");
    echo '<div class="alert alert-success">Avis supprimé avec succès.</div>';
}

// Récupération de tous les avis
$sql = "SELECT e.*, s.Nom, s.Prenom, l.Titre, l.ISBN 
        FROM evaluation e 
        JOIN student s ON e.ID_Etudiant = s.ID_Etudiant 
        JOIN livre l ON e.ISBN = l.ISBN 
        ORDER BY e.Date_Evaluation DESC";
$resultat = mysqli_query($connexion, $sql);
?>

<h2>Modération des Avis</h2>

<table>
    <thead>
        <tr>
            <th>Date</th>
            <th>Livre</th>
            <th>Etudiant</th>
            <th>Note</th>
            <th>Commentaire</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if (mysqli_num_rows($resultat) > 0) {
            while ($row = mysqli_fetch_assoc($resultat)) {
                echo '<tr>';
                echo '<td>' . date('d/m/Y', strtotime($row['Date_Evaluation'])) . '</td>';
                echo '<td>' . $row['Titre'] . '</td>';
                echo '<td>' . $row['Nom'] . ' ' . $row['Prenom'] . '</td>';
                echo '<td>' . $row['Note'] . '/5</td>';
                echo '<td>' . nl2br($row['Commentaire']) . '</td>';
                echo '<td>';
                echo '<a href="moderation_avis.php?supprimer=' . $row['ID_Evaluation'] . '" class="btn-danger" style="font-size:12px; padding:5px;" onclick="return confirm(\'Supprimer cet avis définitivement ?\')">Supprimer</a>';
                echo '</td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="6">Aucun avis à modérer.</td></tr>';
        }
        ?>
    </tbody>
</table>

</div>
</body>
</html>
