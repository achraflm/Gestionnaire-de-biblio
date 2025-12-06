<?php
require_once 'header.php';

if (!isset($_GET['isbn'])) {
    redirection("gestion_livres.php");
}

$isbn = nettoyer_donnees($_GET['isbn']);

// Traitement du formulaire de modification
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titre = nettoyer_donnees($_POST['titre']);
    $id_auteur = (int)$_POST['auteur'];
    $id_categorie = (int)$_POST['categorie'];
    $description = nettoyer_donnees($_POST['description']);
    $image = nettoyer_donnees($_POST['image_url']);
    
    // On ne modifie pas l'ISBN et la quantite ici (quantite geree par exemplaires)
    $requete_update = "UPDATE livre SET Titre = '$titre', ID_Auteur = $id_auteur, ID_Categorie = $id_categorie, Description = '$description', Book_Cover = '$image' WHERE ISBN = '$isbn'";
    
    if (mysqli_query($connexion, $requete_update)) {
        echo '<div class="alert alert-success">Livre modifie avec succes. <a href="gestion_livres.php">Retour</a></div>';
    } else {
        echo '<div class="alert alert-danger">Erreur modification : ' . mysqli_error($connexion) . '</div>';
    }
}

// Recuperation des infos du livre
$sql_livre = "SELECT * FROM livre WHERE ISBN = '$isbn'";
$res_livre = mysqli_query($connexion, $sql_livre);
if (mysqli_num_rows($res_livre) == 0) {
    die("Livre introuvable.");
}
$livre = mysqli_fetch_assoc($res_livre);

// Listes pour dropdowns
$sql_auteurs = "SELECT * FROM auteur ORDER BY Nom_Auteur";
$res_auteurs = mysqli_query($connexion, $sql_auteurs);

$sql_cats = "SELECT * FROM categorie ORDER BY Libelle";
$res_cats = mysqli_query($connexion, $sql_cats);
?>

<h2>Modifier le Livre : <?php echo $livre['Titre']; ?></h2>

<form method="POST" action="modifier_livre.php?isbn=<?php echo $isbn; ?>">
    <label>ISBN (Non modifiable) :</label>
    <input type="text" value="<?php echo $livre['ISBN']; ?>" disabled style="background-color:#eee;">

    <label>Titre :</label>
    <input type="text" name="titre" value="<?php echo $livre['Titre']; ?>" required>

    <label>Auteur :</label>
    <select name="auteur" required>
        <?php
        while ($a = mysqli_fetch_assoc($res_auteurs)) {
            $selected = ($a['ID_Auteur'] == $livre['ID_Auteur']) ? 'selected' : '';
            echo "<option value='" . $a['ID_Auteur'] . "' $selected>" . $a['Nom_Auteur'] . "</option>";
        }
        ?>
    </select>
    <br><br>

    <label>Categorie :</label>
    <select name="categorie" required>
        <?php
        while ($c = mysqli_fetch_assoc($res_cats)) {
            $selected = ($c['ID_Categorie'] == $livre['ID_Categorie']) ? 'selected' : '';
            echo "<option value='" . $c['ID_Categorie'] . "' $selected>" . $c['Libelle'] . "</option>";
        }
        ?>
    </select>
    <br><br>

    <label>URL Image :</label>
    <input type="text" name="image_url" value="<?php echo $livre['Book_Cover']; ?>">
    <?php if(!empty($livre['Book_Cover'])) echo "<img src='".$livre['Book_Cover']."' style='max-height:100px; display:block; margin-top:5px;'>"; ?>

    <label>Description :</label>
    <textarea name="description" rows="5"><?php echo $livre['Description']; ?></textarea>

    <input type="submit" value="Enregistrer les modifications">
</form>

</div>
</body>
</html>
