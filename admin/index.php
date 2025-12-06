<?php
require_once 'header.php';

// Statistiques
$sql_livres = "SELECT COUNT(*) as total FROM livre";
$res_livres = mysqli_query($connexion, $sql_livres);
$total_livres = mysqli_fetch_assoc($res_livres)['total'];

$sql_etudiants = "SELECT COUNT(*) as total FROM student";
$res_etudiants = mysqli_query($connexion, $sql_etudiants);
$total_etudiants = mysqli_fetch_assoc($res_etudiants)['total'];

$sql_emprunts = "SELECT COUNT(*) as total FROM emprunt WHERE Date_Retour IS NULL";
$res_emprunts = mysqli_query($connexion, $sql_emprunts);
$total_emprunts_actifs = mysqli_fetch_assoc($res_emprunts)['total'];

// Livres en retard
$date_actuelle = date('Y-m-d H:i:s');
$sql_retards = "SELECT COUNT(*) as total FROM emprunt WHERE Date_Retour IS NULL AND Date_Retour_Prevu < '$date_actuelle'";
$res_retards = mysqli_query($connexion, $sql_retards);
$total_retards = mysqli_fetch_assoc($res_retards)['total'];


// Donnees pour le graphique (Livres par categorie)
$sql_graph = "SELECT c.Libelle as categorie, COUNT(l.ISBN) as nombre FROM livre l JOIN categorie c ON l.ID_Categorie = c.ID_Categorie GROUP BY c.Libelle";
$res_graph = mysqli_query($connexion, $sql_graph);

$categories = [];
$nombres = [];

while($row = mysqli_fetch_assoc($res_graph)) {
    $categories[] = $row['categorie'];
    $nombres[] = $row['nombre'];
}
?>

<h2>Tableau de Bord Admin</h2>

<div style="display: flex; gap: 20px; margin-bottom: 30px;">
    <div class="alert alert-success" style="flex: 1;">
        <h3>Total Livres</h3>
        <p style="font-size: 24px;"><?php echo $total_livres; ?></p>
    </div>
    <div class="alert alert-success" style="flex: 1;">
        <h3>Etudiants Inscrits</h3>
        <p style="font-size: 24px;"><?php echo $total_etudiants; ?></p>
    </div>
    <div class="alert alert-danger" style="flex: 1;">
        <h3>Emprunts Actifs</h3>
        <p style="font-size: 24px;"><?php echo $total_emprunts_actifs; ?></p>
    </div>
    <div class="alert alert-warning" style="flex: 1; background-color:#f8d7da; border-color:#f5c6cb; color:#721c24;">
        <h3>Livres en Retard</h3>
        <p style="font-size: 24px; font-weight:bold;"><?php echo $total_retards; ?></p>
    </div>
</div>

<div style="width: 60%; margin: auto;">
    <canvas id="monGraphique"></canvas>
</div>

<script>
    var ctx = document.getElementById('monGraphique').getContext('2d');
    var monGraphique = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($categories); ?>,
            datasets: [{
                label: 'Nombre de livres par categorie',
                data: <?php echo json_encode($nombres); ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

</div>
</body>
</html>
