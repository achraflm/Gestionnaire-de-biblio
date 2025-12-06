<?php
require_once 'header.php';

$id_etudiant = $_SESSION['user_id'];
$msg = "";

// mise a jour profil
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = nettoyer_donnees($_POST['nom']);
    $prenom = nettoyer_donnees($_POST['prenom']);
    $email = nettoyer_donnees($_POST['email']);
    
    $requete_update = "UPDATE student SET Nom = '$nom', Prenom = '$prenom', Email = '$email' WHERE ID_Etudiant = $id_etudiant";
    
    if (mysqli_query($connexion, $requete_update)) {
        $msg = '<div class="alert alert-success">Profil mis a jour.</div>';
        // mise a jour session
        $_SESSION['nom'] = $nom;
        $_SESSION['prenom'] = $prenom;
    } else {
        $msg = '<div class="alert alert-danger">Erreur mise a jour.</div>';
    }
}

// recuperation infos actuelles
$requete = "SELECT * FROM student WHERE ID_Etudiant = $id_etudiant";
$resultat = mysqli_query($connexion, $requete);
$user = mysqli_fetch_assoc($resultat);
?>

<h2>Mon Profil</h2>

<?php echo $msg; ?>

<form method="POST" action="profil.php">
    <label for="nom">Nom :</label>
    <input type="text" name="nom" value="<?php echo $user['Nom']; ?>" required>

    <label for="prenom">Prenom :</label>
    <input type="text" name="prenom" value="<?php echo $user['Prenom']; ?>" required>

    <label for="email">Email :</label>
    <input type="email" name="email" value="<?php echo $user['Email']; ?>" required>

    <!-- CNE supprime -->

    <input type="submit" value="Enregistrer les modifications">
</form>

</div>
</body>
</html>
