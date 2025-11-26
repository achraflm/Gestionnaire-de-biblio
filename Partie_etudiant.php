<?php
session_start();
require_once 'config.php';

// Vérification de session
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'etudiant') {
    header('Location: se_connecter.php');
    exit;
}

$id_etudiant = $_SESSION['user_id'];
$message_action = "";

// --------------------------------------------------------
// 1. TRAITEMENT : UPLOAD IMAGE PROFIL ÉTUDIANT
// --------------------------------------------------------
if (isset($_FILES['student_avatar']) && $_FILES['student_avatar']['error'] == 0) {
    $target_dir = "uploads/etudiants/";
    if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
    
    $file_ext = strtolower(pathinfo($_FILES["student_avatar"]["name"], PATHINFO_EXTENSION));
    $new_name = "student_" . $id_etudiant . "." . $file_ext;
    $target_file = $target_dir . $new_name;

    if (move_uploaded_file($_FILES["student_avatar"]["tmp_name"], $target_file)) {
        $stmt = $pdo->prepare("UPDATE etudiant SET photo_profil = ? WHERE id_etudiant = ?");
        $stmt->execute([$target_file, $id_etudiant]);
        $_SESSION['photo'] = $target_file;
        $message_action = "Photo de profil mise à jour !";
    }
}

// --------------------------------------------------------
// 2. TRAITEMENT : MISE À JOUR PROFIL
// --------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $new_email = $_POST['email'];
    $new_pass = $_POST['password'];

    if (!empty($new_pass)) {
        $stmt = $pdo->prepare("UPDATE etudiant SET email = ?, mot_de_passe = ? WHERE id_etudiant = ?");
        $stmt->execute([$new_email, $new_pass, $id_etudiant]);
    } else {
        $stmt = $pdo->prepare("UPDATE etudiant SET email = ? WHERE id_etudiant = ?");
        $stmt->execute([$new_email, $id_etudiant]);
    }
    $message_action = "Profil mis à jour avec succès !";
}

// --------------------------------------------------------
// 3. TRAITEMENT : CONTACTER L'ADMIN (Même page)
// --------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_admin'])) {
    $sujet = htmlspecialchars($_POST['sujet']);
    $msg_content = htmlspecialchars($_POST['message']);
    $message_complet = "Sujet: $sujet \n\n $msg_content";
    
    // On insère dans la table notification (Type 'Contact Admin')
    // Note: id_etudiant est l'expéditeur. L'admin lira les notifs de ce type.
    $sql_msg = "INSERT INTO notification (id_etudiant, type, message, date_envoi, lu) VALUES (?, 'Contact Admin', ?, CURDATE(), 'Non')";
    $stmt = $pdo->prepare($sql_msg);
    if($stmt->execute([$id_etudiant, $message_complet])) {
        $message_action = "Votre message a été envoyé à l'administration.";
    }
}

// --------------------------------------------------------
// 4. RÉCUPÉRATION DES DONNÉES
// --------------------------------------------------------

// Infos Étudiant
$stmt = $pdo->prepare("SELECT * FROM etudiant WHERE id_etudiant = ?");
$stmt->execute([$id_etudiant]);
$user = $stmt->fetch();

// Messages Reçus (Venant de l'Admin)
$sql_msgs = "SELECT * FROM notification WHERE id_etudiant = ? AND type = 'Message Admin' AND lu = 'Non' ORDER BY date_envoi DESC";
$stmt_msgs = $pdo->prepare($sql_msgs);
$stmt_msgs->execute([$id_etudiant]);
$messages_recus = $stmt_msgs->fetchAll();

// Statistiques Dashboard
$stats = [];
$stats['emprunts'] = $pdo->query("SELECT COUNT(*) FROM emprunt WHERE id_etudiant = $id_etudiant AND statut = 'Emprunté'")->fetchColumn();
$stats['dispo'] = $pdo->query("SELECT SUM(exemplaires_disponibles) FROM livre")->fetchColumn();
$stats['retards'] = $pdo->query("SELECT COUNT(*) FROM emprunt WHERE id_etudiant = $id_etudiant AND (statut = 'En retard' OR (statut = 'Emprunté' AND date_retour_prevue < CURDATE()))")->fetchColumn();
$stats['total_lus'] = $pdo->query("SELECT COUNT(*) FROM emprunt WHERE id_etudiant = $id_etudiant AND statut = 'Retourné'")->fetchColumn();

// Emprunts Actifs (Calcul Jours Restants)
$sql_mes_emprunts = "
    SELECT e.*, l.titre, l.auteur,
    DATEDIFF(e.date_retour_prevue, CURDATE()) as jours_restants
    FROM emprunt e 
    JOIN livre l ON e.id_livre = l.id_livre 
    WHERE e.id_etudiant = ? AND e.statut IN ('Emprunté', 'En retard')
    ORDER BY jours_restants ASC";
$stmt_emprunts = $pdo->prepare($sql_mes_emprunts);
$stmt_emprunts->execute([$id_etudiant]);
$mes_emprunts = $stmt_emprunts->fetchAll();

// Catalogue (Recherche)
$search_term = isset($_GET['q']) ? $_GET['q'] : '';
$sql_catalogue = "SELECT l.*, c.nom as cat_nom FROM livre l JOIN categorie c ON l.categorie_id = c.id_categorie WHERE l.titre LIKE ? OR l.auteur LIKE ? LIMIT 10";
$stmt_cat = $pdo->prepare($sql_catalogue);
$stmt_cat->execute(["%$search_term%", "%$search_term%"]);
$livres_catalogue = $stmt_cat->fetchAll();

?>

<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Espace Étudiant - Bibliothèque</title>
    
    <link rel="stylesheet" href="sidebar.css" />
    <link rel="stylesheet" href="general.css" />
    <link rel="stylesheet" href="dashboard.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        /* Header avec Messages */
        .student-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .msg-icon-container { position: relative; cursor: pointer; margin-right: 20px; }
        .msg-icon { font-size: 1.5rem; color: #465985; }
        .msg-badge { position: absolute; top: -5px; right: -8px; background: red; color: white; border-radius: 50%; padding: 2px 6px; font-size: 0.7rem; font-weight: bold; }
        .msg-dropdown { display: none; position: absolute; right: 0; top: 40px; width: 300px; background: white; border: 1px solid #ddd; box-shadow: 0 5px 15px rgba(0,0,0,0.1); border-radius: 8px; z-index: 100; }
        .msg-dropdown.active { display: block; }
        .msg-item { padding: 10px; border-bottom: 1px solid #eee; font-size: 0.9rem; color: #555; }
        .msg-item:hover { background: #f9f9f9; }

        /* Style Dashboard "Glass" (Comme l'image) */
        .dashboard-glass {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.8), rgba(255, 255, 255, 0.3));
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.18);
            margin-bottom: 40px;
        }
        .glass-header { font-size: 2rem; color: #333; font-weight: bold; margin-bottom: 30px; }
        .glass-stats-row { display: flex; justify-content: space-between; flex-wrap: wrap; gap: 20px; }
        .glass-stat { flex: 1; min-width: 150px; text-align: center; }
        .glass-stat h4 { font-size: 0.9rem; color: #666; margin-bottom: 5px; font-weight: normal; }
        .glass-stat .number { font-size: 2.5rem; font-weight: bold; color: #333; display: block; }
        .glass-stat i { font-size: 1.2rem; margin-left: 5px; }
        
        /* Couleurs spécifiques stats */
        .stat-blue { color: #465985 !important; }
        .stat-green { color: #27ae60 !important; }
        .stat-red { color: #e74c3c !important; }

        /* Upload Profil */
        .profile-upload-label { cursor: pointer; position: relative; display: inline-block; }
        .profile-upload-label:hover .avatar { opacity: 0.7; }
        .upload-icon { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: white; font-size: 1.2rem; display: none; }
        .profile-upload-label:hover .upload-icon { display: block; }
        
        /* Tableaux & Boutons */
        .time-safe { color: #27ae60; font-weight: bold; }
        .time-warning { color: #e67e22; font-weight: bold; }
        .time-late { color: #c0392b; font-weight: bold; }
        .btn-action { border: none; padding: 5px 10px; border-radius: 5px; color: white; margin-right: 5px; cursor: pointer; }
        .btn-extend { background-color: #27ae60; }
        .btn-pdf { background-color: #e74c3c; }
    </style>
</head>

<body>

  <aside id="sidebar">
    <div class="header">
      <h1 class="logo">BiblioÉtudiant</h1> 
    </div>

    <nav class="menu">
      <a href="#dashboard" class="menu-item active"><i class="fas fa-th-large"></i> Tableau de bord</a>
      <a href="#catalogue" class="menu-item"><i class="fas fa-search"></i> Chercher un livre</a>
      <a href="#mes-emprunts" class="menu-item"><i class="fas fa-book-reader"></i> Mes Emprunts</a>
      <a href="#profil" class="menu-item"><i class="fas fa-user-edit"></i> Mon Profil</a>
      <a href="#contact" class="menu-item"><i class="fas fa-envelope"></i> Contact Admin</a>
    </nav>

    <div class="user-info">
        <form action="" method="POST" enctype="multipart/form-data" id="studentProfileForm">
            <label class="profile-upload-label">
                <div class="avatar">
                    <?php if(!empty($user['photo_profil'])): ?>
                        <img src="<?= htmlspecialchars($user['photo_profil']) ?>" style="width:100%; height:100%; border-radius:50%; object-fit:cover;">
                    <?php else: ?>
                        <?= strtoupper(substr($user['prenom'], 0, 1)) ?>
                    <?php endif; ?>
                </div>
                <i class="fas fa-camera upload-icon"></i>
                <input type="file" name="student_avatar" style="display:none;" onchange="document.getElementById('studentProfileForm').submit()">
            </label>
        </form>
      <div class="details">
        <span class="name"><?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></span>
        <a href="se_connecter.php" class="logout">Déconnexion</a>
      </div>
    </div>
  </aside>

  <main>
    <div class="main-box">

        <div class="student-header">
            <h1>Espace Étudiant</h1>
            
            <div class="msg-icon-container" onclick="toggleMessages()">
                <i class="fas fa-bell msg-icon"></i>
                <?php if(count($messages_recus) > 0): ?>
                    <span class="msg-badge"><?= count($messages_recus) ?></span>
                <?php endif; ?>
                
                <div class="msg-dropdown" id="msgDropdown">
                    <div style="padding:10px; border-bottom:1px solid #eee; font-weight:bold;">Messages de l'Admin</div>
                    <?php if(count($messages_recus) > 0): ?>
                        <?php foreach($messages_recus as $msg): ?>
                            <div class="msg-item">
                                <strong>Admin :</strong><br>
                                <?= htmlspecialchars($msg['message']) ?><br>
                                <small style="color:#aaa;"><?= date('d/m/Y', strtotime($msg['date_envoi'])) ?></small>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="msg-item">Aucun nouveau message.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if($message_action): ?>
            <div style="padding:15px; background:#d4edda; color:#155724; border-radius:5px; margin-bottom:20px; border-left: 5px solid #28a745;">
                <i class="fas fa-check-circle"></i> <?= $message_action ?>
            </div>
        <?php endif; ?>

        <section id="dashboard" class="dashboard-glass">
            <div class="glass-header">Tableau de bord</div>
            
            <div class="glass-stats-row">
                <div class="glass-stat">
                    <h4>Emprunts en cours <i class="fas fa-book-reader stat-blue"></i></h4>
                    <span class="number"><?= $stats['emprunts'] ?></span>
                </div>
                <div class="glass-stat">
                    <h4>Livres Disponibles <i class="fas fa-book-open stat-green"></i></h4>
                    <span class="number"><?= $stats['dispo'] ?: 0 ?></span>
                </div>
                <div class="glass-stat">
                    <h4>Retards <i class="fas fa-exclamation-triangle stat-red"></i></h4>
                    <span class="number stat-red"><?= $stats['retards'] ?></span>
                </div>
                <div class="glass-stat">
                    <h4>Livres Lus <i class="fas fa-check-circle stat-blue"></i></h4>
                    <span class="number"><?= $stats['total_lus'] ?></span>
                </div>
            </div>
        </section>

        <section id="mes-emprunts">
            <h2><i class="fas fa-clock"></i> Mes Emprunts Actuels</h2>
            <table>
                <thead>
                    <tr>
                        <th>Livre</th>
                        <th>Date Prévue</th>
                        <th>Jours Restants</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($mes_emprunts) > 0): ?>
                        <?php foreach ($mes_emprunts as $emp): 
                            $jours = $emp['jours_restants'];
                            $class = 'time-safe';
                            $text = $jours . " jours";
                            if($jours < 0) { $class = 'time-late'; $text = "Retard (" . abs($jours) . "j)"; }
                            elseif($jours <= 3) { $class = 'time-warning'; }
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($emp['titre']) ?></td>
                            <td><?= date('d/m/Y', strtotime($emp['date_retour_prevue'])) ?></td>
                            <td class="<?= $class ?>"><?= $text ?></td>
                            <td>
                                <?php if($jours >= 0): ?>
                                    <button class="btn-action btn-extend" title="Demander Prolongation"><i class="fas fa-clock"></i> +</button>
                                <?php endif; ?>
                                <button class="btn-action btn-pdf" title="Reçu"><i class="fas fa-file-pdf"></i> PDF</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4">Aucun emprunt en cours.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>

        <section id="catalogue">
            <h2><i class="fas fa-search"></i> Catalogue des livres</h2>
            <form method="GET" action="" class="search-bar" style="display:flex; gap:10px; margin-bottom:20px;">
                <input type="text" name="q" placeholder="Titre, auteur..." value="<?= htmlspecialchars($search_term) ?>" style="flex:1; padding:10px; border:1px solid #ccc; border-radius:5px;">
                <button type="submit" style="background:#465985; color:white; padding:10px 20px; border:none; border-radius:5px;">Rechercher</button>
            </form>
            <table>
                <thead><tr><th>Titre</th><th>Auteur</th><th>Catégorie</th><th>État</th></tr></thead>
                <tbody>
                    <?php foreach($livres_catalogue as $livre): ?>
                    <tr>
                        <td><?= htmlspecialchars($livre['titre']) ?></td>
                        <td><?= htmlspecialchars($livre['auteur']) ?></td>
                        <td><?= htmlspecialchars($livre['cat_nom']) ?></td>
                        <td>
                            <?= ($livre['exemplaires_disponibles'] > 0) ? '<span class="time-safe">Dispo</span>' : '<span class="time-late">Indisponible</span>' ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
        
        <section id="profil">
            <h2><i class="fas fa-user-cog"></i> Modifier Profil</h2>
            <form method="POST" action="">
                <input type="hidden" name="update_profile" value="1">
                <div style="margin-bottom:10px;">
                    <label>Email :</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required style="width:100%; padding:10px;">
                </div>
                <div style="margin-bottom:10px;">
                    <label>Nouveau Mot de passe :</label>
                    <input type="password" name="password" style="width:100%; padding:10px;">
                </div>
                <button type="submit" style="background:#465985; color:white; padding:10px 20px; border:none; border-radius:5px;">Sauvegarder</button>
            </form>
        </section>

        <section id="contact">
            <h2><i class="fas fa-envelope"></i> Contacter l'Admin</h2>
            <form action="" method="POST">
                <input type="hidden" name="contact_admin" value="1">
                <div style="margin-bottom:10px;">
                    <label>Sujet :</label>
                    <input type="text" name="sujet" placeholder="Ex: Problème de retour..." required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:5px;">
                </div>
                <div style="margin-bottom:10px;">
                    <label>Message :</label>
                    <textarea name="message" rows="4" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:5px;"></textarea>
                </div>
                <button type="submit" style="background:#465985; color:white; padding:10px 20px; border:none; border-radius:5px;">Envoyer</button>
            </form>
        </section>

        <footer>
            &copy; Achraf Lemrani @2025 — Gestion de Bibliothèque Scolaire
        </footer>

    </div>
  </main>

  <script>
      function toggleMessages() {
          document.getElementById("msgDropdown").classList.toggle("active");
      }
      window.onclick = function(event) {
        if (!event.target.matches('.msg-icon') && !event.target.closest('.msg-icon-container')) {
            var dropdowns = document.getElementsByClassName("msg-dropdown");
            for (var i = 0; i < dropdowns.length; i++) {
                var openDropdown = dropdowns[i];
                if (openDropdown.classList.contains('active')) {
                    openDropdown.classList.remove('active');
                }
            }
        }
      }
  </script>

</body>
</html>