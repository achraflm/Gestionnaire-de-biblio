<?php
require_once 'header.php';

// Ajout
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nom_auteur'])) {
    $nom = nettoyer_donnees($_POST['nom_auteur']);
    if (!empty($nom)) {
        $sql = "INSERT INTO auteur (Nom_Auteur) VALUES ('$nom')";
        mysqli_query($connexion, $sql);
    }
}

// Suppression
if (isset($_GET['supprimer'])) {
    $id = (int)$_GET['supprimer'];
    // Verif si utilise par des livres
    $check = "SELECT COUNT(*) as cnt FROM livre WHERE ID_Auteur = $id";
    $res = mysqli_query($connexion, $check);
    $data = mysqli_fetch_assoc($res);
    
    if ($data['cnt'] == 0) {
        mysqli_query($connexion, "DELETE FROM auteur WHERE ID_Auteur = $id");
    } else {
        echo "<script>alert('Impossible : Cet auteur est lie a des livres.');</script>";
    }
    redirection("gestion_auteurs.php");
}

$resultat = mysqli_query($connexion, "SELECT * FROM auteur ORDER BY Nom_Auteur");
?>

<h2>Gestion des Auteurs</h2>

<div style="background: #f9f9f9; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
    <form method="POST" action="gestion_auteurs.php" style="display:flex; gap:10px; align-items:center;">
        <label style="margin:0;">Nouvel Auteur :</label>
        <input type="text" name="nom_auteur" required placeholder="Nom complet" style="flex:1;">
        <input type="submit" value="Ajouter">
    </form>
</div>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nom de l'Auteur</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        while ($row = mysqli_fetch_assoc($resultat)) {
            echo '<tr>';
            echo '<td>' . $row['ID_Auteur'] . '</td>';
            echo '<td>' . $row['Nom_Auteur'] . '</td>';
            echo '<td>';
            echo '<a href="modifier_auteur.php?id=' . $row['ID_Auteur'] . '" class="btn-primary" style="padding:2px 5px; font-size:12px; margin-right:5px;">Modifier</a>';
            echo '<a href="gestion_auteurs.php?supprimer=' . $row['ID_Auteur'] . '" class="btn-danger" style="padding:2px 5px; font-size:12px;" onclick="return confirm(\'Supprimer ?\')">X</a>';
            echo '</td>';
            echo '</tr>';
        }
        ?>
    </tbody>
</table>

</div>
</body>
</html>
