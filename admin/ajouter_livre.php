<?php
require_once 'header.php';

// Recuperation des auteurs et categories pour les listes deroulantes
$sql_auteurs = "SELECT * FROM auteur ORDER BY Nom_Auteur";
$res_auteurs = mysqli_query($connexion, $sql_auteurs);

$sql_cats = "SELECT * FROM categorie ORDER BY Libelle";
$res_cats = mysqli_query($connexion, $sql_cats);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titre = nettoyer_donnees($_POST['titre']);
    $id_auteur = (int)$_POST['auteur'];
    $id_categorie = (int)$_POST['categorie'];
    $isbn = nettoyer_donnees($_POST['isbn']);
    $quantite = (int)$_POST['quantite'];
    $description = nettoyer_donnees($_POST['description']);
    
    // gestion image
    $image = "https://via.placeholder.com/150";
    if (!empty($_POST['image_url'])) {
        $image = nettoyer_donnees($_POST['image_url']);
    }

    // 1. Insertion du Livre
    $requete_livre = "INSERT INTO livre (ISBN, Titre, ID_Categorie, Book_Cover, Description, ID_Auteur) 
                      VALUES ('$isbn', '$titre', $id_categorie, '$image', '$description', $id_auteur)";

    if (mysqli_query($connexion, $requete_livre)) {
        // 2. Creation des exemplaires
        if ($quantite > 0) {
            for ($i = 0; $i < $quantite; $i++) {
                $sql_exemplaire = "INSERT INTO exemplaire (ISBN, Etat) VALUES ('$isbn', 'bon etat')";
                mysqli_query($connexion, $sql_exemplaire);
            }
        }
        redirection("gestion_livres.php");
    } else {
        $erreur = "Erreur ajout livre : " . mysqli_error($connexion);
    }
}
?>

<h2>Ajouter un Livre</h2>

<?php if(isset($erreur)) echo "<div class='alert alert-danger'>$erreur</div>"; ?>

<form method="POST" action="ajouter_livre.php">
    <label>Titre :</label>
    <input type="text" name="titre" required>

    <label>Auteur :</label>
    <select name="auteur" required>
        <option value="">Selectionner un auteur</option>
        <?php
        while ($a = mysqli_fetch_assoc($res_auteurs)) {
            echo "<option value='" . $a['ID_Auteur'] . "'>" . $a['Nom_Auteur'] . "</option>";
        }
        ?>
    </select>
    <small><a href="#">+ Ajouter un auteur (Non implemente)</a></small>
    <br><br>

    <label>Categorie :</label>
    <select name="categorie" required>
        <option value="">Selectionner une categorie</option>
        <?php
        while ($c = mysqli_fetch_assoc($res_cats)) {
            echo "<option value='" . $c['ID_Categorie'] . "'>" . $c['Libelle'] . "</option>";
        }
        ?>
    </select>
    <br><br>

    <label>ISBN :</label>
    <input type="text" name="isbn" required placeholder="Ex: ISBN0099">

    <label>Quantite (Nombre d'exemplaires) :</label>
    <input type="number" name="quantite" required min="0" value="1">

    <label>URL Image (Optionnel) :</label>
    <input type="text" name="image_url">

    <label>Description :</label>
    <textarea name="description"></textarea>

    <input type="submit" value="Ajouter">
</form>

</div>
</body>
</html>
