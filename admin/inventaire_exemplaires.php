<?php
require_once 'header.php';

if (!isset($_GET['isbn'])) {
    redirection("gestion_livres.php");
}

$isbn = nettoyer_donnees($_GET['isbn']);
$msg = "";

// Traitement ajout d'exemplaire
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'add_exemplaire') {
    $etat = nettoyer_donnees($_POST['etat']);
    $sql_add = "INSERT INTO exemplaire (ISBN, Etat) VALUES ('$isbn', '$etat')";
    if (mysqli_query($connexion, $sql_add)) {
        $msg = '<div class="alert alert-success">Nouvel exemplaire ajouté.</div>';
    } else {
        $msg = '<div class="alert alert-danger">Erreur ajout exemplaire : ' . mysqli_error($connexion) . '</div>';
    }
}

// Traitement suppression d'exemplaire
if (isset($_GET['action']) && $_GET['action'] == 'delete_exemplaire' && isset($_GET['id_exemplaire'])) {
    $id_exemplaire = (int)$_GET['id_exemplaire'];
    
    // Verifier si l'exemplaire est emprunte
    $check_emprunt = "SELECT COUNT(*) as nb_emprunt FROM emprunt WHERE ID_Exemplaire = $id_exemplaire AND Date_Retour IS NULL";
    $res_check = mysqli_query($connexion, $check_emprunt);
    $data_check = mysqli_fetch_assoc($res_check);

    if ($data_check['nb_emprunt'] > 0) {
        $msg = '<div class="alert alert-danger">Impossible de supprimer : Exemplaire actuellement emprunté.</div>';
    } else {
        $sql_delete = "DELETE FROM exemplaire WHERE ID_Exemplaire = $id_exemplaire";
        if (mysqli_query($connexion, $sql_delete)) {
            $msg = '<div class="alert alert-success">Exemplaire supprimé.</div>';
        } else {
            $msg = '<div class="alert alert-danger">Erreur suppression exemplaire : ' . mysqli_error($connexion) . '</div>';
        }
    }
}

// Recuperer les infos du livre
$sql_livre = "SELECT Titre, Book_Cover, a.Nom_Auteur FROM livre l JOIN auteur a ON l.ID_Auteur = a.ID_Auteur WHERE ISBN = '$isbn'";
$res_livre = mysqli_query($connexion, $sql_livre);
$livre = mysqli_fetch_assoc($res_livre);

// Recuperer les exemplaires du livre
$sql_exemplaires = "SELECT ex.*, e.ID_Emprunt, e.Date_Emprunt, e.Date_Retour_Prevu, s.Nom, s.Prenom 
                    FROM exemplaire ex 
                    LEFT JOIN emprunt e ON ex.ID_Exemplaire = e.ID_Exemplaire AND e.Date_Retour IS NULL
                    LEFT JOIN student s ON e.ID_Etudiant = s.ID_Etudiant
                    WHERE ex.ISBN = '$isbn'
                    ORDER BY ex.ID_Exemplaire ASC";
$res_exemplaires = mysqli_query($connexion, $sql_exemplaires);
?>

<h2>Inventaire Détaillé pour : <?php echo $livre['Titre']; ?> (<?php echo $isbn; ?>)</h2>
<p>Auteur : <?php echo $livre['Nom_Auteur']; ?></p>

<?php echo $msg; ?>

<div style="display:flex; margin-bottom:20px; align-items:center;">
    <img src="<?php echo $livre['Book_Cover'] ?: 'https://via.placeholder.com/100'; ?>" alt="<?php echo $livre['Titre']; ?>" style="width:80px; height:auto; margin-right:15px; border-radius:5px;">
    <h3><?php echo $livre['Titre']; ?></h3>
</div>

<h3>Ajouter un nouvel exemplaire</h3>
<form method="POST" action="inventaire_exemplaires.php?isbn=<?php echo $isbn; ?>" style="display:flex; gap:10px; margin-bottom:20px;">
    <input type="hidden" name="action" value="add_exemplaire">
    <label>État :</label>
    <select name="etat" required>
        <option value="bonetat">Bon état</option>
        <option value="mauvaisetat">Mauvais état</option>
    </select>
    <input type="submit" value="Ajouter Exemplaire">
</form>

<h3>Liste des Exemplaires</h3>
<table>
    <thead>
        <tr>
            <th>ID Exemplaire</th>
            <th>État</th>
            <th>Emprunté par</th>
            <th>Date Emprunt</th>
            <th>Retour Prévu</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($res_exemplaires && mysqli_num_rows($res_exemplaires) > 0) {
            while ($exemplaire = mysqli_fetch_assoc($res_exemplaires)) {
                echo '<tr>';
                echo '<td>' . $exemplaire['ID_Exemplaire'] . '</td>';
                echo '<td>' . $exemplaire['Etat'] . '</td>';
                if ($exemplaire['ID_Emprunt']) {
                    echo '<td>' . $exemplaire['Nom'] . ' ' . $exemplaire['Prenom'] . '</td>';
                    echo '<td>' . $exemplaire['Date_Emprunt'] . '</td>';
                    echo '<td>' . $exemplaire['Date_Retour_Prevu'] . '</td>';
                    echo '<td>(Emprunté)</td>'; // Pas d'action si emprunté
                } else {
                    echo '<td>-</td>';
                    echo '<td>-</td>';
                    echo '<td>-</td>';
                    echo '<td>';
                    echo '<a href="inventaire_exemplaires.php?action=delete_exemplaire&isbn=' . $isbn . '&id_exemplaire=' . $exemplaire['ID_Exemplaire'] . '" class="btn-danger" style="font-size:12px; padding:5px;" onclick="return confirm(\'Supprimer cet exemplaire ?\')">Supprimer</a>';
                    echo '</td>';
                }
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="6">Aucun exemplaire pour ce livre.</td></tr>';
        }
        ?>
    </tbody>
</table>

<br>
<a href="gestion_livres.php">Retour à la gestion des livres</a>

</div>
</body>
</html>
