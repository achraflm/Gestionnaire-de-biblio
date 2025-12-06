<?php
require_once 'header.php';

$id_etudiant = $_SESSION['user_id'];

// verifier les retards
$date_actuelle = date('Y-m-d H:i:s');
$requete_retard = "SELECT COUNT(*) as total FROM emprunt WHERE ID_Etudiant = $id_etudiant AND Date_Retour_Prevu < '$date_actuelle' AND Date_Retour IS NULL";
$resultat_retard = mysqli_query($connexion, $requete_retard);
$row_retard = mysqli_fetch_assoc($resultat_retard);
$nb_retards = $row_retard['total'];

// Recuperer les alertes "Pret a Emprunter"
$sql_alerts = "SELECT * FROM message WHERE ID_Etudiant = $id_etudiant AND Sens = 'admin-vers-etudiant' AND Lu = 0 ORDER BY Date_Envoi DESC";
$res_alerts = mysqli_query($connexion, $sql_alerts);

// --- STATISTIQUES POUR CHART.JS ---

// 1. Categories preferees (Basé sur les emprunts de l'etudiant)
$sql_stat_cat = "SELECT c.Libelle, COUNT(*) as count 
                 FROM emprunt e
                 JOIN exemplaire ex ON e.ID_Exemplaire = ex.ID_Exemplaire
                 JOIN livre l ON ex.ISBN = l.ISBN
                 JOIN categorie c ON l.ID_Categorie = c.ID_Categorie
                 WHERE e.ID_Etudiant = $id_etudiant
                 GROUP BY c.Libelle";
$res_stat_cat = mysqli_query($connexion, $sql_stat_cat);
$cat_labels = [];
$cat_data = [];
while ($row = mysqli_fetch_assoc($res_stat_cat)) {
    $cat_labels[] = $row['Libelle'];
    $cat_data[] = $row['count'];
}

// 2. Emprunts par mois (Historique)
$sql_stat_mois = "SELECT DATE_FORMAT(Date_Emprunt, '%Y-%m') as mois, COUNT(*) as count
                  FROM emprunt
                  WHERE ID_Etudiant = $id_etudiant
                  GROUP BY mois
                  ORDER BY mois ASC
                  LIMIT 6";
$res_stat_mois = mysqli_query($connexion, $sql_stat_mois);
$mois_labels = [];
$mois_data = [];
while ($row = mysqli_fetch_assoc($res_stat_mois)) {
    $mois_labels[] = $row['mois'];
    $mois_data[] = $row['count'];
}
?>

<h2>Bienvenue, <?php echo $_SESSION['prenom'] . ' ' . $_SESSION['nom']; ?></h2>

<div class="stats">
    <p>Vous etes connecte a votre espace etudiant.</p>
    
    <?php if ($nb_retards > 0): ?>
        <div class="alert alert-danger">
            Attention ! Vous avez <strong><?php echo $nb_retards; ?></strong> livre(s) en retard. Merci de les rendre au plus vite.
        </div>
    <?php else: ?>
        <div class="alert alert-success">
            Aucun retard a signaler. Bonne lecture !
        </div>
    <?php endif; ?>

    <?php if ($res_alerts && mysqli_num_rows($res_alerts) > 0): ?>
        <div class="alert alert-info" style="font-weight:bold;">
            <h3>Nouvelles Alertes :</h3>
            <ul>
                <?php while ($alert = mysqli_fetch_assoc($res_alerts)): ?>
                    <li>
                        <?php echo $alert['Contenu']; ?> 
                        <a href="mark_alert_read.php?id_message=<?php echo $alert['ID_Message']; ?>" style="margin-left:10px; font-size:12px;">Marquer comme lu</a>
                    </li>
                <?php endwhile; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <!-- Section Graphiques -->
    <div style="display:flex; gap:20px; margin-top:30px; flex-wrap:wrap;">
        <div style="flex:1; min-width:300px; background:#fff; padding:20px; border-radius:8px; box-shadow:0 2px 4px rgba(0,0,0,0.1);">
            <h3>Mes Categories Preferees</h3>
            <canvas id="chartCategories"></canvas>
        </div>
        <div style="flex:1; min-width:300px; background:#fff; padding:20px; border-radius:8px; box-shadow:0 2px 4px rgba(0,0,0,0.1);">
            <h3>Historique Emprunts (6 derniers mois)</h3>
            <canvas id="chartMois"></canvas>
        </div>
    </div>

    <script>
        // Chart Categories
        new Chart(document.getElementById('chartCategories'), {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($cat_labels); ?>,
                datasets: [{
                    data: <?php echo json_encode($cat_data); ?>,
                    backgroundColor: ['#ff6384', '#36a2eb', '#cc65fe', '#ffce56', '#4bc0c0']
                }]
            }
        });

        // Chart Mois
        new Chart(document.getElementById('chartMois'), {
            type: 'line',
            data: {
                labels: <?php echo json_encode($mois_labels); ?>,
                datasets: [{
                    label: 'Livres empruntes',
                    data: <?php echo json_encode($mois_data); ?>,
                    borderColor: '#36a2eb',
                    tension: 0.1
                }]
            },
            options: {
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });
    </script>
    
    <p style="margin-top:20px;">Utilisez le menu ci-dessus pour naviguer.</p>
</div>

</div> <!-- fin container -->
</body>
</html>
