<?php
require_once 'header.php';

$id_etudiant = $_SESSION['user_id'];
$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sujet = nettoyer_donnees($_POST['sujet']);
    $message_body = nettoyer_donnees($_POST['message']);

    // Combine subject and message body into Contenu field
    $contenu = "Sujet: " . $sujet . "\n\n" . $message_body;

    $sql = "INSERT INTO message (ID_Etudiant, Sens, Contenu, Lu) VALUES ($id_etudiant, 'etudiant-vers-admin', '$contenu', 0)";
    
    if (mysqli_query($connexion, $sql)) {
        $msg = '<div class="alert alert-success">Votre message a été envoyé à l\'administrateur.</div>';
    } else {
        $msg = '<div class="alert alert-danger">Erreur lors de l\'envoi du message : ' . mysqli_error($connexion) . '</div>';
    }
}
?>

<h2>Contacter l\'Administrateur</h2>

<?php echo $msg; ?>

<form method="POST" action="contact.php">
    <label for="sujet">Sujet :</label>
    <input type="text" name="sujet" id="sujet" required>

    <label for="message">Message :</label>
    <textarea name="message" id="message" rows="8" required></textarea>

    <input type="submit" value="Envoyer le message">
</form>

</div>
</body>
</html>
