<?php
require_once 'header.php';

if (!isset($_GET['id'])) {
    redirection("gestion_etudiants.php");
}

$id = (int)$_GET['id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = nettoyer_donnees($_POST['nom']);
    $prenom = nettoyer_donnees($_POST['prenom']);
    $email = nettoyer_donnees($_POST['email']);
    $statut = nettoyer_donnees($_POST['statut']);
    $mdp = $_POST['mot_de_passe']; // Optionnel

    $sql = "UPDATE student SET Nom='$nom', Prenom='$prenom', Email='$email', Statut='$statut' WHERE ID_Etudiant=$id";
    
    if (!empty($mdp)) {
        // Mise a jour mot de passe si fourni
        $sql = "UPDATE student SET Nom='$nom', Prenom='$prenom', Email='$email', Statut='$statut', Mot_de_passe='$mdp' WHERE ID_Etudiant=$id";
    }

    if (mysqli_query($connexion, $sql)) {
        redirection("gestion_etudiants.php");
    } else {
        $erreur = "Erreur modification : " . mysqli_error($connexion);
    }
}

$resultat = mysqli_query($connexion, "SELECT * FROM student WHERE ID_Etudiant = $id");
$etudiant = mysqli_fetch_assoc($resultat);
?>

<h2>Modifier Etudiant</h2>

<?php if(isset($erreur)) echo "<div class='alert alert-danger'>$erreur</div>"; ?>

<form method="POST" action="modifier_etudiant.php?id=<?php echo $id; ?>">
    <label>Nom :</label>
    <input type="text" name="nom" value="<?php echo $etudiant['Nom']; ?>" required>

    <label>Prenom :</label>
    <input type="text" name="prenom" value="<?php echo $etudiant['Prenom']; ?>" required>

    <label>Email :</label>
    <input type="email" name="email" value="<?php echo $etudiant['Email']; ?>" required>

    <label>Statut :</label>
    <select name="statut">
        <option value="actif" <?php if($etudiant['Statut'] == 'actif') echo 'selected'; ?>>Actif</option>
        <option value="restrict" <?php if($etudiant['Statut'] == 'restrict') echo 'selected'; ?>>Restreint (Bloque)</option>
    </select>

    <label>Nouveau Mot de passe (Laisser vide pour ne pas changer) :</label>
    <input type="text" name="mot_de_passe" placeholder="Nouveau mot de passe">

    <input type="submit" value="Enregistrer">
</form>

</div>
</body>
</html>
