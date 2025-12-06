<?php
require_once 'header.php';

// Action Bloquer/Debloquer
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $action = $_GET['action'];
    
    $new_status = ($action == 'bloquer') ? 'restrict' : 'actif';
    
    $sql = "UPDATE student SET Statut = '$new_status' WHERE ID_Etudiant = $id";
    mysqli_query($connexion, $sql);
    redirection("gestion_etudiants.php");
}

$requete = "SELECT * FROM student";
$resultat = mysqli_query($connexion, $requete);
?>

<h2>Gestion des Etudiants</h2>

<table>
    <thead>
        <tr>
            <th>Nom</th>
            <th>Prenom</th>
            <th>Email</th>
            <th>Statut</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        while ($row = mysqli_fetch_assoc($resultat)) {
            $est_bloque = ($row['Statut'] == 'restrict');
            echo '<tr>';
            echo '<td>' . $row['Nom'] . '</td>';
            echo '<td>' . $row['Prenom'] . '</td>';
            echo '<td>' . $row['Email'] . '</td>';
            echo '<td>' . ($est_bloque ? '<span style="color:red;">Restreint</span>' : '<span style="color:green;">Actif</span>') . '</td>';
            echo '<td>';
            echo '<a href="modifier_etudiant.php?id=' . $row['ID_Etudiant'] . '" class="btn-primary" style="padding:5px; margin-right:5px;">Modifier</a>';
            if ($est_bloque) {
                echo '<a href="gestion_etudiants.php?action=debloquer&id=' . $row['ID_Etudiant'] . '" class="btn-primary" style="padding:5px;">Reactiver</a>';
            } else {
                echo '<a href="gestion_etudiants.php?action=bloquer&id=' . $row['ID_Etudiant'] . '" class="btn-danger" style="padding:5px;">Pinaliser (Restreindre)</a>';
            }
            echo '</td>';
            echo '</tr>';
        }
        ?>
    </tbody>
</table>

</div>
</body>
</html>
