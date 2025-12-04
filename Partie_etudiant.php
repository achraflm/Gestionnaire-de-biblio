<?php
session_start();
require_once 'config.php';
require_once 'actions/Action Etudiant/emprunt.php';    
require_once 'actions/Action Etudiant/recu.php';        
require_once 'actions/Action Etudiant/messages.php';      
require_once 'actions/Action Etudiant/reservation.php'; 


$id_etudiant = $_SESSION['id_etudiant'] ?? 1;
$section = $_GET['section'] ?? 'catalogue';
$msg = "";

// ======== GESTION DES POST ========
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $isbn   = $_POST['isbn'] ?? '';
    $message = $_POST['message'] ?? '';

    if ($action === 'emprunter' && $isbn) {
        $msg = emprunterLivre($pdo, $id_etudiant, $isbn);
    } elseif ($action === 'reserver' && $isbn) {
        $msg = reserverLivre($pdo, $id_etudiant, $isbn);
    } elseif ($action === 'send_message' && $message) {
        $msg = sendMessageToAdmin($pdo, $id_etudiant, $message);
    }
}

if (isset($_GET['mode']) && $_GET['mode'] === 'recu_pdf' && isset($_GET['id'])) {
    echo genererRecu($pdo, $_GET['id']);
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    
    <meta charset="UTF-8">
    <title>Espace Étudiant</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-Ss+8A4ckf5+E9gWZ8yecM+6x+PL2soMH6N9uZp9NvDRLRkL7v+5b6J/EEj3vYq0tT96+j1T3aQWw5+8RLs0nRg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="general.css">
</head>
<body>

    <header id="header">
        <h1 id="title">📚 Espace Étudiant</h1>
        <nav id="main-nav">
            <a href="?section=catalogue" class="nav-link">Catalogue Livres</a>
            <a href="?section=mes_emprunts" class="nav-link">Mes Emprunts</a>
            <a href="?section=messagerie" class="nav-link">Contact Admin</a>
        </nav>
        <hr>
    </header>

    <main id="main-content">

        <?php if($msg): ?>
            <div id="info-msg"><?= $msg ?></div>
            <hr>
        <?php endif; ?>

        <!-- ===== SECTION CATALOGUE ===== -->
        <?php if($section == 'catalogue'): 
            $livres = $pdo->query("SELECT l.*, c.Libelle FROM livre l LEFT JOIN categorie_livre c ON l.id_categorie=c.ID_Categorie")->fetchAll();
        ?>
            <section id="catalogue-section">
                <h2 class="section-title">📚 Catalogue</h2>
                <table id="catalogue-table">
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Auteur</th>
                            <th>Disponibilité</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($livres as $l): $dispo = getDispo($pdo, $l['ISBN']); ?>
                            <tr>
                                <td><?= htmlspecialchars($l['Titre']) ?></td>
                                <td><?= htmlspecialchars($l['Auteur']) ?></td>
                                <td>
                                    <span class="<?= $dispo>0?'dispo':'indispo' ?>"><?= $dispo>0?'Disponible':'Indisponible' ?></span>
                                </td>
                                <td>
                                    <form method="POST" class="action-form">
                                        <input type="hidden" name="isbn" value="<?= htmlspecialchars($l['ISBN']) ?>">
                                        <?php if($dispo > 0): ?>
                                            <button class="btn borrow-btn" type="submit" name="action" value="emprunter">Emprunter (15j)</button>
                                        <?php else: ?>
                                            <button class="btn reserve-btn" type="submit" name="action" value="reserver">Réserver</button>
                                        <?php endif; ?>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>

        <!-- ===== SECTION MES EMPRUNTS ===== -->
        <?php elseif($section == 'mes_emprunts'): 
            $stmt = $pdo->prepare("SELECT em.*, l.Titre 
                                   FROM emprunter em 
                                   JOIN exemplaire ex ON em.ID_Exemplaire=ex.ID_Exemplaire 
                                   JOIN livre l ON ex.ISBN=l.ISBN 
                                   WHERE em.ID_Etudiant=? AND em.Date_Retour IS NULL");
            $stmt->execute([$id_etudiant]);
            $mes_emprunts = $stmt->fetchAll();
        ?>
            <section id="emprunts-section">
                <h2 class="section-title">📂 Mes Livres en cours</h2>
                <table id="emprunts-table">
                    <thead>
                        <tr>
                            <th>Livre</th>
                            <th>Date fin</th>
                            <th>État</th>
                            <th>Preuve</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($mes_emprunts as $me): ?>
                            <tr>
                                <td><?= htmlspecialchars($me['Titre']) ?></td>
                                <td><?= $me['Date_Retour_Prevue'] ?></td>
                                <td><?= genererBadge($me['Date_Retour_Prevue']) ?></td>
                                <td><a class="download-link" href="?mode=recu_pdf&id=<?= $me['ID_Emprunt'] ?>" target="_blank">Télécharger Reçu</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>

        <!-- ===== SECTION MESSAGERIE ===== -->
        <?php elseif($section == 'messagerie'): 
            $stmt = $pdo->prepare("SELECT * FROM messages WHERE ID_Etudiant=? ORDER BY Date_Envoi ASC");
            $stmt->execute([$id_etudiant]);
            $msgs = $stmt->fetchAll();
        ?>
            <section id="messagerie-section">
                <h2 class="section-title">💬 Discussion avec l'Admin</h2>
                
                <div id="messages-box">
                    <?php foreach($msgs as $m): ?>
                        <div class="message <?= $m['Sens']=='EtudiantVersAdmin'?'mine':'admin' ?>">
                            <p><?= htmlspecialchars($m['Contenu']) ?></p>
                            <small><?= $m['Date_Envoi'] ?></small>
                        </div>
                    <?php endforeach; ?>
                </div>

                <form id="messagerie-form" method="POST">
                    <input type="hidden" name="action" value="send_message">
                    <input type="text" name="message" id="message-input" placeholder="Écrire un message..." required>
                    <button id="send-btn" type="submit">Envoyer</button>
                </form>
            </section>
        <?php endif; ?>

    </main>

</body>
</html>

