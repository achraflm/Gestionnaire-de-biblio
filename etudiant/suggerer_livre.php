<?php
require_once 'header.php';

$id_etudiant = $_SESSION['user_id'];
$msg = "";

// Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titre = nettoyer_donnees($_POST['titre']);
    $auteur = nettoyer_donnees($_POST['auteur']);

    $sql = "INSERT INTO suggestion_livre (Titre, Auteur, ID_Etudiant) VALUES ('$titre', '$auteur', $id_etudiant)";
    
    if (mysqli_query($connexion, $sql)) {
        $msg = '<div class="alert alert-success">Suggestion envoyée avec succès !</div>';
    } else {
        $msg = '<div class="alert alert-danger">Erreur lors de l\'envoi.</div>';
    }
}

// Liste de mes suggestions
$sql_list = "SELECT * FROM suggestion_livre WHERE ID_Etudiant = $id_etudiant ORDER BY Date_Suggestion DESC";
$res_list = mysqli_query($connexion, $sql_list);
?>

<h2>Suggérer un Livre</h2>
<p>Le livre que vous cherchez n'est pas disponible ? Suggérez-le nous !</p>

<?php echo $msg; ?>

<div style="background:#f9f9f9; padding:20px; border-radius:8px; margin-bottom:30px;">
    <form method="POST" action="suggerer_livre.php">
        <label>Titre du Livre :</label>
        <input type="text" name="titre" required>

        <label>Auteur :</label>
        <input type="text" name="auteur" required>

        <input type="submit" value="Envoyer la suggestion" class="btn-primary">
    </form>
</div>

<h3>Mes Suggestions</h3>
<table>
    <thead>
        <tr>
            <th>Livre</th>
            <th>Auteur</th>
            <th>Date</th>
            <th>Statut</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if (mysqli_num_rows($res_list) > 0) {
            while ($row = mysqli_fetch_assoc($res_list)) {
                $status_color = 'orange';
                if ($row['Statut'] == 'acceptee') $status_color = 'green';
                if ($row['Statut'] == 'refusee') $status_color = 'red';

                echo '<tr>';
                echo '<td>' . $row['Titre'] . '</td>';
                echo '<td>' . $row['Auteur'] . '</td>';
                echo '<td>' . date('d/m/Y', strtotime($row['Date_Suggestion'])) . '</td>';
                echo '<td style="color:' . $status_color . '; font-weight:bold;">' . ucfirst($row['Statut']) . '</td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="4">Aucune suggestion envoyée.</td></tr>';
        }
        ?>
    </tbody>
</table>

</div>
</body>
</html>
