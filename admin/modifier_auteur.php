<?php
require_once 'header.php';

if (!isset($_GET['id'])) {
    redirection("gestion_auteurs.php");
}

$id = (int)$_GET['id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = nettoyer_donnees($_POST['nom']);
    if (!empty($nom)) {
        $sql = "UPDATE auteur SET Nom_Auteur = '$nom' WHERE ID_Auteur = $id";
        if (mysqli_query($connexion, $sql)) {
            redirection("gestion_auteurs.php");
        } else {
            $erreur = "Erreur modification : " . mysqli_error($connexion);
        }
    }
}

$resultat = mysqli_query($connexion, "SELECT * FROM auteur WHERE ID_Auteur = $id");
$auteur = mysqli_fetch_assoc($resultat);
?>

<h2>Modifier Auteur</h2>

<?php if(isset($erreur)) echo "<div class='alert alert-danger'>$erreur</div>"; ?>

<form method="POST" action="modifier_auteur.php?id=<?php echo $id; ?>">
    <label>Nom de l'Auteur :</label>
    <input type="text" name="nom" value="<?php echo $auteur['Nom_Auteur']; ?>" required>
    <input type="submit" value="Modifier">
</form>

</div>
</body>
</html>
