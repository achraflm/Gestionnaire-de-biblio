<?php
require_once 'header.php';

// List only logic here. Add/Edit in separate files or modals. keeping it simple.
// Jointure pour recuperer les noms d'auteur et categorie + Compte des exemplaires
$requete = "SELECT l.ISBN, l.Titre, a.Nom_Auteur, c.Libelle, 
            (SELECT COUNT(*) FROM exemplaire e WHERE e.ISBN = l.ISBN) as Total_Exemplaires,
            (SELECT COUNT(*) FROM exemplaire e WHERE e.ISBN = l.ISBN AND e.Etat = 'bon etat' AND e.ID_Exemplaire NOT IN (SELECT ID_Exemplaire FROM emprunt WHERE Date_Retour IS NULL)) as Disponibles,
            (SELECT COUNT(*) FROM emprunt e JOIN exemplaire ex ON e.ID_Exemplaire = ex.ID_Exemplaire WHERE ex.ISBN = l.ISBN AND e.Date_Retour IS NULL) as Empruntes
            FROM livre l
            LEFT JOIN auteur a ON l.ID_Auteur = a.ID_Auteur
            LEFT JOIN categorie c ON l.ID_Categorie = c.ID_Categorie";

// Ajouter le filtre de recherche
$recherche = "";
if (isset($_GET['recherche'])) {
    $recherche = nettoyer_donnees($_GET['recherche']);
    $requete .= " WHERE l.Titre LIKE '%$recherche%' OR a.Nom_Auteur LIKE '%$recherche%' OR c.Libelle LIKE '%$recherche%'";
}

$resultat = mysqli_query($connexion, $requete);
?>

<h2>Gestion des Livres (Inventaire)</h2>

<a href="ajouter_livre.php" class="btn-primary" style="text-decoration:none;">+ Ajouter un Livre</a>

<form method="GET" action="gestion_livres.php" style="margin-top:20px; margin-bottom:20px; display:flex; gap:10px;">
    <input type="text" name="recherche" placeholder="Rechercher par titre, auteur, categorie..." value="<?php echo $recherche; ?>" style="flex:1; padding:8px;">
    <input type="submit" value="Rechercher" class="btn-primary" style="padding:8px 15px;">
</form>

<table>
    <thead>
        <tr>
            <th>ISBN</th>
            <th>Titre</th>
            <th>Auteur</th>
            <th>Categorie</th>
            <th>Total Exemplaires</th>
            <th>Disponibles</th>
            <th>Empruntés</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($resultat) {
            while ($row = mysqli_fetch_assoc($resultat)) {
                echo '<tr>';
                echo '<td>' . $row['ISBN'] . '</td>';
                echo '<td>' . $row['Titre'] . '</td>';
                echo '<td>' . $row['Nom_Auteur'] . '</td>';
                echo '<td>' . $row['Libelle'] . '</td>';
                echo '<td>' . $row['Total_Exemplaires'] . '</td>';
                echo '<td>' . $row['Disponibles'] . '</td>';
                echo '<td>' . $row['Empruntes'] . '</td>';
                echo '<td>';
                echo '<a href="inventaire_exemplaires.php?isbn=' . $row['ISBN'] . '" class="btn-info" style="text-decoration:none; padding:5px; font-size:12px; margin-right:5px;">Détails Exemplaires</a>';
                echo '<a href="modifier_livre.php?isbn=' . $row['ISBN'] . '" class="btn-primary" style="text-decoration:none; padding:5px; font-size:12px; margin-right:5px;">Modifier</a>';
                echo '<a href="supprimer_livre.php?isbn=' . $row['ISBN'] . '" class="btn-danger" style="text-decoration:none; padding:5px; font-size:12px;" onclick="return confirm(\'Supprimer ce livre et TOUS ses exemplaires ?\')">Supprimer</a>';
                echo '</td>';
                echo '</tr>';
            }
        }
        ?>
    </tbody>
</table>

</div>
</body>
</html>
