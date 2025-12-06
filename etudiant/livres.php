<?php
require_once 'header.php';

$recherche = "";
if (isset($_GET['recherche'])) {
    $recherche = nettoyer_donnees($_GET['recherche']);
}

$tri = "pertinence";
if (isset($_GET['tri'])) {
    $tri = nettoyer_donnees($_GET['tri']);
}

$disponibles_seulement = false;
if (isset($_GET['disponibles_seulement'])) {
    $disponibles_seulement = true;
}

$id_etudiant = $_SESSION['user_id'];

// Ordre de tri
$order_by = "l.Titre ASC"; // Defaut
switch ($tri) {
    case 'note':
        $order_by = "avg_note DESC";
        break;
    case 'avis':
        $order_by = "nb_avis DESC";
        break;
    case 'populaire':
        $order_by = "nb_emprunts DESC";
        break;
    default:
        $order_by = "l.Titre ASC";
}

// requete de recherche avec jointures, calcul de dispo, favoris et STATS
$requete = "SELECT l.ISBN, l.Titre, l.Book_Cover, a.Nom_Auteur, c.Libelle,
            (SELECT COUNT(*) FROM exemplaire WHERE ISBN = l.ISBN) as total_copies,
            (SELECT COUNT(*) FROM emprunt e JOIN exemplaire ex ON e.ID_Exemplaire = ex.ID_Exemplaire WHERE ex.ISBN = l.ISBN AND e.Date_Retour IS NULL) as borrowed_copies,
            (SELECT COUNT(*) FROM favoris f WHERE f.ISBN = l.ISBN AND f.ID_Etudiant = $id_etudiant) as is_favorite,
            (SELECT COALESCE(AVG(Note), 0) FROM evaluation ev WHERE ev.ISBN = l.ISBN) as avg_note,
            (SELECT COUNT(*) FROM evaluation ev WHERE ev.ISBN = l.ISBN) as nb_avis,
            (SELECT COUNT(*) FROM emprunt e JOIN exemplaire ex ON e.ID_Exemplaire = ex.ID_Exemplaire WHERE ex.ISBN = l.ISBN) as nb_emprunts
            FROM livre l 
            LEFT JOIN auteur a ON l.ID_Auteur = a.ID_Auteur 
            LEFT JOIN categorie c ON l.ID_Categorie = c.ID_Categorie
            WHERE l.Titre LIKE '%$recherche%' OR a.Nom_Auteur LIKE '%$recherche%' OR c.Libelle LIKE '%$recherche%'";

// Ajouter le filtre de disponibilite
if ($disponibles_seulement) {
    $requete .= " HAVING (total_copies - borrowed_copies) > 0";
}

$requete .= " ORDER BY $order_by"; // Ajout de l'ORDER BY ici, après le HAVING si present

$resultat = mysqli_query($connexion, $requete);

// Helper étoiles
function render_stars($note) {
    $stars = "";
    for ($i = 1; $i <= 5; $i++) {
        $stars .= ($i <= round($note)) ? "★" : "☆";
    }
    return '<span style="color:#f4c150;">' . $stars . '</span>';
}
?>

<h2>Liste des Livres</h2>

<form method="GET" action="livres.php" style="background:#f8f9fa; padding:20px; border-radius:8px; box-shadow:0 2px 5px rgba(0,0,0,0.05); margin-bottom:30px;">
    <div style="display:flex; flex-wrap:wrap; gap:15px; align-items:center;">
        
        <!-- Barre de recherche -->
        <div style="flex: 3; min-width: 250px;">
            <input type="text" name="recherche" placeholder="Rechercher un livre, auteur, categorie..." value="<?php echo $recherche; ?>" 
                   style="width:100%; padding:12px; border:1px solid #ddd; border-radius:5px; font-size:16px;">
        </div>
        
        <!-- Tri -->
        <div style="flex: 1; min-width: 200px;">
            <select name="tri" onchange="this.form.submit()" 
                    style="width:100%; padding:12px; border:1px solid #ddd; border-radius:5px; background:white; cursor:pointer; font-size:14px;">
                <option value="pertinence" <?php if($tri == 'pertinence') echo 'selected'; ?>>Trier par : Pertinence</option>
                <option value="note" <?php if($tri == 'note') echo 'selected'; ?>>⭐ Les mieux notés</option>
                <option value="populaire" <?php if($tri == 'populaire') echo 'selected'; ?>>📈 Les plus populaires</option>
                <option value="avis" <?php if($tri == 'avis') echo 'selected'; ?>>💬 Les plus commentés</option>
            </select>
        </div>

        <!-- Bouton Submit -->
        <div>
            <input type="submit" value="Rechercher" class="btn-primary" style="padding:12px 25px; font-size:16px; cursor:pointer;">
        </div>
    </div>

    <!-- Filtres supplémentaires (Checkbox) -->
    <div style="margin-top:15px; display:flex; align-items:center;">
        <input type="checkbox" id="disponibles_seulement" name="disponibles_seulement" value="1" <?php if($disponibles_seulement) echo 'checked'; ?> onchange="this.form.submit()" style="width:18px; height:18px; margin-right:8px; cursor:pointer;">
        <label for="disponibles_seulement" style="margin:0; font-size:14px; color:#555; cursor:pointer;">Masquer les livres indisponibles</label>
    </div>
</form>

<?php
if (isset($_GET['succes'])) {
    echo '<div class="alert alert-success">Demande d\'emprunt enregistree avec succes.</div>';
}
if (isset($_GET['erreur'])) {
    if ($_GET['erreur'] == 'limite') {
        echo '<div class="alert alert-warning" style="background-color:#fff3cd; color:#856404; border-color:#ffeeba;">
                <strong>Limite atteinte !</strong> Vous avez déjà 2 demandes de prêt actives. 
                Veuillez retourner un livre ou annuler une demande avant d\'en emprunter un nouveau.
              </div>';
    } elseif ($_GET['erreur'] == 'doublon') {
        echo '<div class="alert alert-warning">Vous avez déjà demandé ce livre.</div>';
    } else {
        echo '<div class="alert alert-danger">Erreur lors de la demande.</div>';
    }
}
?>

<div class="livres-grid">
    <?php
    if ($resultat && mysqli_num_rows($resultat) > 0) {
        while ($livre = mysqli_fetch_assoc($resultat)) {
            $dispo = $livre['total_copies'] - $livre['borrowed_copies'];
            $fav_icon = ($livre['is_favorite'] > 0) ? '❤️' : '🤍';
            $fav_title = ($livre['is_favorite'] > 0) ? 'Retirer des favoris' : 'Ajouter aux favoris';
            
            echo '<div class="livre-card" style="position:relative;">';
            
            // Bouton Favoris
            echo '<a href="toggle_favoris.php?isbn=' . $livre['ISBN'] . '" title="' . $fav_title . '" style="position:absolute; top:10px; right:10px; text-decoration:none; font-size:24px; z-index:10;">' . $fav_icon . '</a>';

            // image placeholder si vide
            $img = !empty($livre['Book_Cover']) ? $livre['Book_Cover'] : 'https://via.placeholder.com/150';
            
            // Lien vers detail
            echo '<a href="detail_livre.php?isbn=' . $livre['ISBN'] . '" style="text-decoration:none; color:inherit;">';
            echo '<img src="' . $img . '" alt="' . $livre['Titre'] . '">';
            echo '<h3>' . $livre['Titre'] . '</h3>';
            echo '</a>';
            
            // Stats
            echo '<div style="font-size:0.9em; margin-bottom:5px;">';
            echo render_stars($livre['avg_note']) . ' <small>(' . $livre['nb_avis'] . ' avis)</small>';
            echo '</div>';
            
            echo '<p>Auteur : ' . $livre['Nom_Auteur'] . '</p>';
            echo '<p>Categorie : ' . $livre['Libelle'] . '</p>';
            echo '<p>Dispo : ' . $dispo . '</p>';
            
            // Indicateur Popularité
            if ($livre['nb_emprunts'] > 0) {
                 echo '<p style="font-size:0.8em; color:gray;">📚 Emprunté ' . $livre['nb_emprunts'] . ' fois</p>';
            }
            
            if ($dispo > 0) {
                echo '<a href="emprunter.php?isbn=' . $livre['ISBN'] . '" class="btn-primary" style="text-decoration:none; display:block; margin-top:10px;">Emprunter</a>';
            } else {
                // Bouton File d'attente
                echo '<a href="emprunter.php?isbn=' . $livre['ISBN'] . '" class="btn-warning" style="text-decoration:none; display:block; margin-top:10px; background-color:orange; color:white;">Rejoindre la file d\'attente</a>';
            }
            echo '</div>';
        }
    } else {
        echo '<p>Aucun livre trouve.</p>';
    }
    ?>
</div>

</div> <!-- fin container -->
</body>
</html>
