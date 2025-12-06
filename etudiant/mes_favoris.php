<?php
require_once 'header.php';

$id_etudiant = $_SESSION['user_id'];

// Requete pour recuperer les livres favoris de l'etudiant
$sql = "SELECT l.*, a.Nom_Auteur, c.Libelle,
        (SELECT COUNT(*) FROM exemplaire WHERE ISBN = l.ISBN) as total_copies,
        (SELECT COUNT(*) FROM emprunt e JOIN exemplaire ex ON e.ID_Exemplaire = ex.ID_Exemplaire WHERE ex.ISBN = l.ISBN AND e.Date_Retour IS NULL) as borrowed_copies
        FROM favoris f
        JOIN livre l ON f.ISBN = l.ISBN
        LEFT JOIN auteur a ON l.ID_Auteur = a.ID_Auteur
        LEFT JOIN categorie c ON l.ID_Categorie = c.ID_Categorie
        WHERE f.ID_Etudiant = $id_etudiant
        ORDER BY f.Date_Ajout DESC";

$resultat = mysqli_query($connexion, $sql);
?>

<h2>Mes Favoris ❤️</h2>

<div class="livres-grid">
    <?php
    if ($resultat && mysqli_num_rows($resultat) > 0) {
        while ($livre = mysqli_fetch_assoc($resultat)) {
            $dispo = $livre['total_copies'] - $livre['borrowed_copies'];
            
            echo '<div class="livre-card" style="position:relative;">';
            
            // Bouton Supprimer Favoris (Croix rouge ou Cœur brisé)
            echo '<a href="toggle_favoris.php?isbn=' . $livre['ISBN'] . '" title="Retirer des favoris" style="position:absolute; top:10px; right:10px; text-decoration:none; font-size:20px; z-index:10;">❌</a>';

            $img = !empty($livre['Book_Cover']) ? $livre['Book_Cover'] : 'https://via.placeholder.com/150';
            
            // Lien vers detail
            echo '<a href="detail_livre.php?isbn=' . $livre['ISBN'] . '" style="text-decoration:none; color:inherit;">';
            echo '<img src="' . $img . '" alt="' . $livre['Titre'] . '">';
            echo '<h3>' . $livre['Titre'] . '</h3>';
            echo '</a>';
            
            echo '<p>Auteur : ' . $livre['Nom_Auteur'] . '</p>';
            echo '<p>Categorie : ' . $livre['Libelle'] . '</p>';
            echo '<p>Dispo : ' . $dispo . '</p>';
            
            if ($dispo > 0) {
                echo '<a href="emprunter.php?isbn=' . $livre['ISBN'] . '" class="btn-primary" style="text-decoration:none; display:block; margin-top:10px;">Emprunter</a>';
            } else {
                echo '<a href="emprunter.php?isbn=' . $livre['ISBN'] . '" class="btn-warning" style="text-decoration:none; display:block; margin-top:10px; background-color:orange; color:white;">Rejoindre la file d\'attente</a>';
            }
            echo '</div>';
        }
    } else {
        echo '<p>Vous n\'avez aucun livre en favoris. <a href="livres.php">Parcourir le catalogue</a></p>';
    }
    ?>
</div>

</div>
</body>
</html>
