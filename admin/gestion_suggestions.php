<?php
require_once 'header.php';

// Actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $action = $_GET['action'];
    
    if ($action == 'accepter') {
        mysqli_query($connexion, "UPDATE suggestion_livre SET Statut = 'acceptee' WHERE ID_Suggestion = $id");
        
        // Notification optionnelle a l'etudiant
        $sql_info = "SELECT ID_Etudiant, Titre FROM suggestion_livre WHERE ID_Suggestion = $id";
        $res_info = mysqli_query($connexion, $sql_info);
        $info = mysqli_fetch_assoc($res_info);
        $msg_notif = "Bonne nouvelle ! Votre suggestion pour le livre '" . $info['Titre'] . "' a été acceptée. Nous allons le commander.";
        mysqli_query($connexion, "INSERT INTO message (ID_Etudiant, Sens, Contenu) VALUES (" . $info['ID_Etudiant'] . ", 'admin-vers-etudiant', '" . addslashes($msg_notif) . "')");

    } elseif ($action == 'refuser') {
        mysqli_query($connexion, "UPDATE suggestion_livre SET Statut = 'refusee' WHERE ID_Suggestion = $id");
    }
    redirection("gestion_suggestions.php");
}

// Liste des suggestions en attente
$sql = "SELECT s.*, st.Nom, st.Prenom 
        FROM suggestion_livre s 
        JOIN student st ON s.ID_Etudiant = st.ID_Etudiant 
        WHERE s.Statut = 'en_attente' 
        ORDER BY s.Date_Suggestion ASC";
$resultat = mysqli_query($connexion, $sql);
?>

<h2>Gestion des Suggestions</h2>

<table>
    <thead>
        <tr>
            <th>Date</th>
            <th>Etudiant</th>
            <th>Livre Suggéré</th>
            <th>Auteur</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if (mysqli_num_rows($resultat) > 0) {
            while ($row = mysqli_fetch_assoc($resultat)) {
                echo '<tr>';
                echo '<td>' . date('d/m/Y', strtotime($row['Date_Suggestion'])) . '</td>';
                echo '<td>' . $row['Nom'] . ' ' . $row['Prenom'] . '</td>';
                echo '<td>' . $row['Titre'] . '</td>';
                echo '<td>' . $row['Auteur'] . '</td>';
                echo '<td>';
                echo '<a href="gestion_suggestions.php?action=accepter&id=' . $row['ID_Suggestion'] . '" class="btn-primary" style="padding:5px; margin-right:5px;">Accepter</a>';
                echo '<a href="gestion_suggestions.php?action=refuser&id=' . $row['ID_Suggestion'] . '" class="btn-danger" style="padding:5px;">Refuser</a>';
                echo '</td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="5">Aucune suggestion en attente.</td></tr>';
        }
        ?>
    </tbody>
</table>

<h3 style="margin-top:40px;">Historique (Acceptées / Refusées)</h3>
<?php
$sql_hist = "SELECT s.*, st.Nom, st.Prenom 
             FROM suggestion_livre s 
             JOIN student st ON s.ID_Etudiant = st.ID_Etudiant 
             WHERE s.Statut != 'en_attente' 
             ORDER BY s.Date_Suggestion DESC LIMIT 10";
$res_hist = mysqli_query($connexion, $sql_hist);
?>
<table>
    <thead>
        <tr>
            <th>Date</th>
            <th>Etudiant</th>
            <th>Livre</th>
            <th>Statut</th>
        </tr>
    </thead>
    <tbody>
        <?php
        while ($row = mysqli_fetch_assoc($res_hist)) {
            $color = ($row['Statut'] == 'acceptee') ? 'green' : 'red';
            echo '<tr>';
            echo '<td>' . date('d/m/Y', strtotime($row['Date_Suggestion'])) . '</td>';
            echo '<td>' . $row['Nom'] . ' ' . $row['Prenom'] . '</td>';
            echo '<td>' . $row['Titre'] . '</td>';
            echo '<td style="color:' . $color . '; font-weight:bold;">' . ucfirst($row['Statut']) . '</td>';
            echo '</tr>';
        }
        ?>
    </tbody>
</table>

</div>
</body>
</html>
