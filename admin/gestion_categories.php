<?php
require_once 'header.php';

// Ajout
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['libelle'])) {
    $libelle = nettoyer_donnees($_POST['libelle']);
    if (!empty($libelle)) {
        $sql = "INSERT INTO categorie (Libelle) VALUES ('$libelle')";
        mysqli_query($connexion, $sql);
    }
}

// Suppression
if (isset($_GET['supprimer'])) {
    $id = (int)$_GET['supprimer'];
    // Verif si utilise
    $check = "SELECT COUNT(*) as cnt FROM livre WHERE ID_Categorie = $id";
    $res = mysqli_query($connexion, $check);
    $data = mysqli_fetch_assoc($res);
    
    if ($data['cnt'] == 0) {
        mysqli_query($connexion, "DELETE FROM categorie WHERE ID_Categorie = $id");
    } else {
        echo "<script>alert('Impossible : Cette categorie contient des livres.');</script>";
    }
    redirection("gestion_categories.php");
}

$resultat = mysqli_query($connexion, "SELECT * FROM categorie ORDER BY Libelle");
?>

<h2>Gestion des Categories</h2>

<div style="background: #f9f9f9; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
    <form method="POST" action="gestion_categories.php" style="display:flex; gap:10px; align-items:center;">
        <label style="margin:0;">Nouvelle Categorie :</label>
        <input type="text" name="libelle" required placeholder="Libelle" style="flex:1;">
        <input type="submit" value="Ajouter">
    </form>
</div>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Libelle</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        while ($row = mysqli_fetch_assoc($resultat)) {
            echo '<tr>';
            echo '<td>' . $row['ID_Categorie'] . '</td>';
            echo '<td>' . $row['Libelle'] . '</td>';
            echo '<td>';
            echo '<a href="modifier_categorie.php?id=' . $row['ID_Categorie'] . '" class="btn-primary" style="padding:2px 5px; font-size:12px; margin-right:5px;">Modifier</a>';
            echo '<a href="gestion_categories.php?supprimer=' . $row['ID_Categorie'] . '" class="btn-danger" style="padding:2px 5px; font-size:12px;" onclick="return confirm(\'Supprimer ?\')">X</a>';
            echo '</td>';
            echo '</tr>';
        }
        ?>
    </tbody>
</table>

</div>
</body>
</html>
