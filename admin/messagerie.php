<?php
require_once 'header.php';

// Marquer comme lu si demande
if (isset($_GET['read_id'])) {
    $id_msg = (int)$_GET['read_id'];
    mysqli_query($connexion, "UPDATE message SET Lu = 1 WHERE ID_Message = $id_msg");
    redirection("messagerie.php");
}

// Repondre
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_destinataire = (int)$_POST['id_etudiant'];
    $sujet = "Reponse Admin";
    $message = nettoyer_donnees($_POST['reponse']);
    
    $sql = "INSERT INTO message (ID_Etudiant, Sens, Contenu, Lu) VALUES ($id_destinataire, 'admin-vers-etudiant', '$message', 0)";
    
    if (mysqli_query($connexion, $sql)) {
        echo '<div class="alert alert-success">Reponse envoyee.</div>';
    } else {
        echo '<div class="alert alert-danger">Erreur envoi.</div>';
    }
}

// Liste des messages recus
$sql = "SELECT m.*, s.Nom, s.Prenom, s.ID_Etudiant as StudentID 
        FROM message m 
        JOIN student s ON m.ID_Etudiant = s.ID_Etudiant 
        WHERE m.Sens = 'etudiant-vers-admin' 
        ORDER BY m.Date_Envoi DESC";
$resultat = mysqli_query($connexion, $sql);
?>

<h2>Messagerie Etudiants</h2>

<div class="messages-list">
    <?php
    if (mysqli_num_rows($resultat) > 0) {
        while ($row = mysqli_fetch_assoc($resultat)) {
            $style = ($row['Lu'] == 0) ? 'background-color:#f0f8ff; border-left: 5px solid #007bff;' : 'background-color:#fff;';
            
            echo '<div class="message-card" style="border:1px solid #ddd; margin-bottom:15px; padding:15px; ' . $style . '">';
            echo '<div style="display:flex; justify-content:space-between;">';
            echo '<strong>De : ' . $row['Nom'] . ' ' . $row['Prenom'] . '</strong>';
            echo '<small>' . $row['Date_Envoi'] . '</small>';
            echo '</div>';
            
            echo '<p style="margin:10px 0;">' . nl2br($row['Contenu']) . '</p>';
            
            echo '<div style="display:flex; gap:10px; align-items:center;">';
            if ($row['Lu'] == 0) {
                echo '<a href="messagerie.php?read_id=' . $row['ID_Message'] . '" class="btn-primary" style="font-size:12px; padding:5px;">Marquer comme lu</a>';
            }
            
            // Formulaire de reponse rapide
            echo '<form method="POST" action="messagerie.php" style="flex:1; display:flex; gap:5px;">';
            echo '<input type="hidden" name="id_etudiant" value="' . $row['StudentID'] . '">';
            echo '<input type="text" name="reponse" placeholder="Repondre..." required style="flex:1;">';
            echo '<input type="submit" value="Envoyer" style="padding:5px;">';
            echo '</form>';
            
            echo '</div>';
            echo '</div>';
        }
    } else {
        echo '<p>Aucun message recu.</p>';
    }
    ?>
</div>

</div>
</body>
</html>
