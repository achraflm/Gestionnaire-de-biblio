<?php
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

if (!est_etudiant()) {
    redirection('../index.php');
}

if (!isset($_GET['isbn'])) {
    redirection("mes_emprunts.php");
}

$isbn = nettoyer_donnees($_GET['isbn']);
$id_etudiant = $_SESSION['user_id'];
$msg = "";

// Verifier si une evaluation existe deja pour ce livre par cet etudiant
$sql_current_eval = "SELECT * FROM evaluation WHERE ID_Etudiant = $id_etudiant AND ISBN = '$isbn'";
$res_current_eval = mysqli_query($connexion, $sql_current_eval);
$existing_eval = mysqli_fetch_assoc($res_current_eval);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $note = (int)$_POST['note'];
    $commentaire = nettoyer_donnees($_POST['commentaire']);

    // Validation
    if ($note < 1 || $note > 5) {
        $msg = '<div class="alert alert-danger">La note doit etre entre 1 et 5.</div>';
    } else {
        if ($existing_eval) {
            // Mise a jour de l'evaluation existante
            $sql = "UPDATE evaluation SET Note = $note, Commentaire = '$commentaire', Date_Evaluation = CURRENT_TIMESTAMP WHERE ID_Evaluation = " . $existing_eval['ID_Evaluation'];
        } else {
            // Nouvelle evaluation
            $sql = "INSERT INTO evaluation (ID_Etudiant, ISBN, Note, Commentaire) VALUES ($id_etudiant, '$isbn', $note, '$commentaire')";
        }

        if (mysqli_query($connexion, $sql)) {
            redirection("mes_emprunts.php?msg=evaluation_succes");
        } else {
            $msg = '<div class="alert alert-danger">Erreur SQL: ' . mysqli_error($connexion) . '</div>';
        }
    }
}

// Recuperer les infos du livre
$sql_livre = "SELECT Titre, Book_Cover FROM livre WHERE ISBN = '$isbn'";
$res_livre = mysqli_query($connexion, $sql_livre);
if (mysqli_num_rows($res_livre) == 0) {
    die("Livre introuvable.");
}
$livre = mysqli_fetch_assoc($res_livre);

?>

<h2>Évaluer le livre : <?php echo $livre['Titre']; ?></h2>

<?php echo $msg; ?>

<div style="display:flex; align-items:center; margin-bottom:20px;">
    <img src="<?php echo $livre['Book_Cover'] ?: 'https://via.placeholder.com/100'; ?>" alt="<?php echo $livre['Titre']; ?>" style="width:80px; height:auto; margin-right:15px; border-radius:5px;">
    <h3><?php echo $livre['Titre']; ?></h3>
</div>

<form method="POST" action="evaluer_livre.php?isbn=<?php echo $isbn; ?>">
    <label for="note">Note (1-5) :</label>
    <select name="note" id="note" required>
        <?php for ($i = 1; $i <= 5; $i++): ?>
            <option value="<?php echo $i; ?>" <?php if ($existing_eval && $existing_eval['Note'] == $i) echo 'selected'; ?>><?php echo $i; ?></option>
        <?php endfor; ?>
    </select>
    <br><br>

    <label for="commentaire">Commentaire (facultatif) :</label>
    <textarea name="commentaire" id="commentaire" rows="5"><?php echo $existing_eval['Commentaire'] ?? ''; ?></textarea>
    <br><br>

    <input type="submit" value="<?php echo $existing_eval ? 'Modifier mon évaluation' : 'Soumettre mon évaluation'; ?>">
</form>

</div>
</body>
</html>
