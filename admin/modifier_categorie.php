<?php
require_once 'header.php';

if (!isset($_GET['id'])) {
    redirection("gestion_categories.php");
}

$id = (int)$_GET['id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $libelle = nettoyer_donnees($_POST['libelle']);
    if (!empty($libelle)) {
        $sql = "UPDATE categorie SET Libelle = '$libelle' WHERE ID_Categorie = $id";
        if (mysqli_query($connexion, $sql)) {
            redirection("gestion_categories.php");
        } else {
            $erreur = "Erreur modification : " . mysqli_error($connexion);
        }
    }
}

$resultat = mysqli_query($connexion, "SELECT * FROM categorie WHERE ID_Categorie = $id");
$categorie = mysqli_fetch_assoc($resultat);
?>

<h2>Modifier Categorie</h2>

<?php if(isset($erreur)) echo "<div class='alert alert-danger'>$erreur</div>"; ?>

<form method="POST" action="modifier_categorie.php?id=<?php echo $id; ?>">
    <label>Libelle :</label>
    <input type="text" name="libelle" value="<?php echo $categorie['Libelle']; ?>" required>
    <input type="submit" value="Modifier">
</form>

</div>
</body>
</html>
