<?php
require_once 'header.php';

if (!isset($_GET['isbn'])) {
    redirection("livres.php");
}

$isbn = nettoyer_donnees($_GET['isbn']);
$id_etudiant = $_SESSION['user_id'];

// 1. Infos du Livre
$sql_livre = "SELECT l.*, a.Nom_Auteur, c.Libelle as Categorie, l.ID_Categorie,
              (SELECT COUNT(*) FROM favoris WHERE ISBN = l.ISBN) as nb_likes,
              (SELECT COUNT(*) FROM favoris WHERE ISBN = l.ISBN AND ID_Etudiant = $id_etudiant) as is_fav
              FROM livre l
              LEFT JOIN auteur a ON l.ID_Auteur = a.ID_Auteur
              LEFT JOIN categorie c ON l.ID_Categorie = c.ID_Categorie
              WHERE l.ISBN = '$isbn'";
$res_livre = mysqli_query($connexion, $sql_livre);

if (mysqli_num_rows($res_livre) == 0) {
    die("Livre introuvable.");
}
$livre = mysqli_fetch_assoc($res_livre);

// 2. Disponibilité
$sql_dispo = "SELECT 
              (SELECT COUNT(*) FROM exemplaire WHERE ISBN = '$isbn') as total,
              (SELECT COUNT(*) FROM emprunt e JOIN exemplaire ex ON e.ID_Exemplaire = ex.ID_Exemplaire WHERE ex.ISBN = '$isbn' AND e.Date_Retour IS NULL) as emprunts";
$res_dispo = mysqli_query($connexion, $sql_dispo);
$data_dispo = mysqli_fetch_assoc($res_dispo);
$dispo_count = $data_dispo['total'] - $data_dispo['emprunts'];

// 3. Note Moyenne & Commentaires
$sql_avis = "SELECT e.*, s.Nom, s.Prenom 
             FROM evaluation e 
             JOIN student s ON e.ID_Etudiant = s.ID_Etudiant 
             WHERE e.ISBN = '$isbn' 
             ORDER BY e.Date_Evaluation DESC";
$res_avis = mysqli_query($connexion, $sql_avis);

$total_notes = 0;
$count_avis = 0;
$avis_list = [];

while ($avis = mysqli_fetch_assoc($res_avis)) {
    $total_notes += $avis['Note'];
    $count_avis++;
    $avis_list[] = $avis;
}

$moyenne = ($count_avis > 0) ? round($total_notes / $count_avis, 1) : 0;

// --- Traitement Formulaire Avis ---
$msg_avis = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['note'])) {
    $note = (int)$_POST['note'];
    $commentaire = nettoyer_donnees($_POST['commentaire']);
    
    if ($note < 1 || $note > 5) {
        $msg_avis = '<div class="alert alert-danger">La note doit etre entre 1 et 5.</div>';
    } else {
        // Verifier si deja note
        $sql_check = "SELECT * FROM evaluation WHERE ID_Etudiant = $id_etudiant AND ISBN = '$isbn'";
        $res_check = mysqli_query($connexion, $sql_check);
        
        if (mysqli_num_rows($res_check) > 0) {
            // Update
            $existing_id = mysqli_fetch_assoc($res_check)['ID_Evaluation'];
            $sql_upd = "UPDATE evaluation SET Note = $note, Commentaire = '$commentaire', Date_Evaluation = CURRENT_TIMESTAMP WHERE ID_Evaluation = $existing_id";
            mysqli_query($connexion, $sql_upd);
            $msg_avis = '<div class="alert alert-success">Votre avis a été mis à jour.</div>';
        } else {
            // Insert
            $sql_ins = "INSERT INTO evaluation (ID_Etudiant, ISBN, Note, Commentaire) VALUES ($id_etudiant, '$isbn', $note, '$commentaire')";
            mysqli_query($connexion, $sql_ins);
            $msg_avis = '<div class="alert alert-success">Merci pour votre avis !</div>';
        }
        // Refresh pour voir l'avis
        redirection("detail_livre.php?isbn=$isbn");
    }
}

// Recuperer mon avis actuel si existe
$sql_mon_avis = "SELECT * FROM evaluation WHERE ID_Etudiant = $id_etudiant AND ISBN = '$isbn'";
$res_mon_avis = mysqli_query($connexion, $sql_mon_avis);
$mon_avis = mysqli_fetch_assoc($res_mon_avis);


// 4. Livres Similaires (Même catégorie)
$cat_id = $livre['ID_Categorie'];
$sql_sim = "SELECT l.ISBN, l.Titre, l.Book_Cover 
            FROM livre l 
            WHERE l.ID_Categorie = $cat_id AND l.ISBN != '$isbn' 
            ORDER BY RAND() LIMIT 3";
$res_sim = mysqli_query($connexion, $sql_sim);

// Helper pour les étoiles
function afficher_etoiles($note) {
    $stars = "";
    for ($i = 1; $i <= 5; $i++) {
        $stars .= ($i <= $note) ? "★" : "☆";
    }
    return '<span style="color:#f4c150; font-size:1.2em;">' . $stars . '</span>';
}
?>

<div class="container" style="padding-top:20px;">
    <a href="livres.php" style="text-decoration:none; color:#666;">&larr; Retour à la liste</a>

    <!-- Section Détails -->
    <div style="display:flex; gap:30px; margin-top:20px; background:white; padding:30px; border-radius:10px; box-shadow:0 2px 10px rgba(0,0,0,0.05);">
        
        <!-- Image -->
        <div style="flex:0 0 200px;">
            <img src="<?php echo $livre['Book_Cover'] ?: 'https://via.placeholder.com/200x300'; ?>" alt="Couverture" style="width:100%; border-radius:5px; box-shadow:0 4px 8px rgba(0,0,0,0.1);">
        </div>

        <!-- Infos -->
        <div style="flex:1;">
            <div style="display:flex; justify-content:space-between; align-items:start;">
                <h1 style="margin-top:0;"><?php echo $livre['Titre']; ?></h1>
                
                <!-- Bouton Like -->
                <div style="text-align:center;">
                    <a href="toggle_favoris.php?isbn=<?php echo $livre['ISBN']; ?>" style="text-decoration:none; font-size:2rem;">
                        <?php echo ($livre['is_fav'] > 0) ? '❤️' : '🤍'; ?>
                    </a>
                    <div style="font-size:0.9em; color:#666;"><?php echo $livre['nb_likes']; ?> J'aime</div>
                </div>
            </div>

            <p style="font-size:1.2em; color:#555;">Par <strong><?php echo $livre['Nom_Auteur']; ?></strong></p>
            <p><span style="background:#eee; padding:5px 10px; border-radius:15px; font-size:0.9em;"><?php echo $livre['Categorie']; ?></span></p>
            
            <div style="margin:20px 0;">
                <?php echo afficher_etoiles($moyenne); ?> 
                <span style="font-weight:bold; font-size:1.2em; margin-left:5px;"><?php echo $moyenne; ?>/5</span>
                <span style="color:#888;">(<?php echo $count_avis; ?> avis)</span>
            </div>

            <p style="line-height:1.6; color:#444;"><?php echo nl2br($livre['Description']); ?></p>

            <div style="margin-top:30px; padding-top:20px; border-top:1px solid #eee;">
                <?php if ($dispo_count > 0): ?>
                    <span style="color:green; font-weight:bold;">✅ Disponible (<?php echo $dispo_count; ?> en stock)</span>
                    <a href="emprunter.php?isbn=<?php echo $livre['ISBN']; ?>" class="btn-primary" style="display:inline-block; margin-left:20px; padding:10px 20px;">Emprunter</a>
                <?php else: ?>
                    <span style="color:orange; font-weight:bold;">⏳ Indisponible</span>
                    <a href="emprunter.php?isbn=<?php echo $livre['ISBN']; ?>" class="btn-warning" style="display:inline-block; margin-left:20px; padding:10px 20px; background:orange; color:white; text-decoration:none;">Rejoindre la file d'attente</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Section Similaires -->
    <?php if (mysqli_num_rows($res_sim) > 0): ?>
    <h3 style="margin-top:40px; border-bottom:2px solid #eee; padding-bottom:10px;">Livres Similaires</h3>
    <div style="display:flex; gap:20px; overflow-x:auto; padding-bottom:10px;">
        <?php while ($sim = mysqli_fetch_assoc($res_sim)): ?>
            <div style="flex:0 0 150px; text-align:center;">
                <a href="detail_livre.php?isbn=<?php echo $sim['ISBN']; ?>">
                    <img src="<?php echo $sim['Book_Cover'] ?: 'https://via.placeholder.com/150'; ?>" style="width:100%; height:200px; object-fit:cover; border-radius:5px; border:1px solid #ddd;">
                    <p style="font-size:0.9em; font-weight:bold; color:#333; margin-top:5px;"><?php echo $sim['Titre']; ?></p>
                </a>
            </div>
        <?php endwhile; ?>
    </div>
    <?php endif; ?>

    <!-- Formulaire d'avis -->
    <h3 style="margin-top:40px; border-bottom:2px solid #eee; padding-bottom:10px;">Donner votre avis</h3>
    <?php echo $msg_avis; ?>
    <div style="background:#f0f8ff; padding:20px; border-radius:8px;">
        <form method="POST" action="detail_livre.php?isbn=<?php echo $isbn; ?>">
            <div style="margin-bottom:15px;">
                <label>Votre Note :</label>
                <select name="note" required style="padding:5px;">
                    <option value="5" <?php if($mon_avis && $mon_avis['Note']==5) echo 'selected'; ?>>5 - Excellent</option>
                    <option value="4" <?php if($mon_avis && $mon_avis['Note']==4) echo 'selected'; ?>>4 - Très bien</option>
                    <option value="3" <?php if($mon_avis && $mon_avis['Note']==3) echo 'selected'; ?>>3 - Bien</option>
                    <option value="2" <?php if($mon_avis && $mon_avis['Note']==2) echo 'selected'; ?>>2 - Moyen</option>
                    <option value="1" <?php if($mon_avis && $mon_avis['Note']==1) echo 'selected'; ?>>1 - Mauvais</option>
                </select>
            </div>
            <div style="margin-bottom:15px;">
                <label>Votre Commentaire :</label>
                <textarea name="commentaire" rows="3" style="width:100%; margin-top:5px;" placeholder="Ce livre est..."><?php echo $mon_avis ? $mon_avis['Commentaire'] : ''; ?></textarea>
            </div>
            <input type="submit" value="<?php echo $mon_avis ? 'Modifier mon avis' : 'Publier mon avis'; ?>" class="btn-primary">
        </form>
    </div>

    <!-- Section Commentaires -->
    <h3 style="margin-top:40px; border-bottom:2px solid #eee; padding-bottom:10px;">Avis des Étudiants (<?php echo $count_avis; ?>)</h3>
    
    <div class="avis-list">
        <?php if (count($avis_list) > 0): ?>
            <?php foreach ($avis_list as $avis): ?>
                <div style="background:#f9f9f9; padding:15px; border-radius:8px; margin-bottom:15px;">
                    <div style="display:flex; justify-content:space-between;">
                        <strong><?php echo $avis['Prenom'] . ' ' . substr($avis['Nom'], 0, 1) . '.'; ?></strong>
                        <small style="color:#888;"><?php echo date('d/m/Y', strtotime($avis['Date_Evaluation'])); ?></small>
                    </div>
                    <div style="margin:5px 0;">
                        <?php echo afficher_etoiles($avis['Note']); ?>
                    </div>
                    <p style="margin:0; color:#555;"><?php echo nl2br($avis['Commentaire']); ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="color:#777; font-style:italic;">Aucun avis pour le moment.</p>
        <?php endif; ?>
    </div>

</div>

</body>
</html>
