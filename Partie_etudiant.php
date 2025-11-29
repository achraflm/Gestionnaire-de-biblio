<?php
require_once 'config.php';
session_start();
if (!isset($_SESSION['id_etudiant'])) $_SESSION['id_etudiant'] = 1; 
$id_etudiant = $_SESSION['id_etudiant'];
$section = $_GET['section'] ?? 'catalogue';
$msg = "";

// =========================================================
// TRAITEMENT PDF (Pour l'emprunt)
// =========================================================
if (isset($_GET['mode']) && $_GET['mode'] === 'recu_pdf') {
    $sql="SELECT e.Nom, l.Titre, em.Date_Emprunt, em.Date_Retour_Prevue FROM emprunter em JOIN etudiant e ON em.ID_Etudiant=e.ID_Etudiant JOIN exemplaire ex ON em.ID_Exemplaire=ex.ID_Exemplaire JOIN livre l ON ex.ISBN=l.ISBN WHERE em.ID_Emprunt=?";
    $stmt=$pdo->prepare($sql); $stmt->execute([$_GET['id']]); $d=$stmt->fetch();
    echo "<body onload='window.print()'>
            <div style='border:1px solid black; padding:20px;'>
                <h1>REÇU D'EMPRUNT</h1>
                <p>Livre : {$d['Titre']}</p>
                <p>Emprunteur : {$d['Nom']}</p>
                <p>Date retour : {$d['Date_Retour_Prevue']}</p>
            </div>
          </body>"; 
    exit;
}

// =========================================================
// TRAITEMENT DES ACTIONS (POST)
// =========================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    // --- FONCTIONNALITE : EMPRUNTER UN LIVRE ---
    if ($action === 'emprunter') {
        $isbn = $_POST['isbn'];
        // Vérifier stock
        $sql = "SELECT ID_Exemplaire FROM exemplaire WHERE ISBN=? AND ID_Exemplaire NOT IN (SELECT ID_Exemplaire FROM emprunter WHERE Date_Retour IS NULL) LIMIT 1";
        $stmt = $pdo->prepare($sql); $stmt->execute([$isbn]); $ex = $stmt->fetch();

        if ($ex) {
            $pdo->prepare("INSERT INTO emprunter (ID_Etudiant, ID_Exemplaire, Date_Emprunt, Date_Retour_Prevue) VALUES (?, ?, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 15 DAY))")->execute([$id_etudiant, $ex['ID_Exemplaire']]);
            $id = $pdo->lastInsertId();
            
            // Info téléchargement
            $f = $pdo->query("SELECT Fichier_PDF FROM livre WHERE ISBN='$isbn'")->fetch();
            $msg = "Emprunt réussi ! <br>";
            $msg .= "<a href='?mode=recu_pdf&id=$id' target='_blank'>[Télécharger Reçu]</a> ";
            if($f['Fichier_PDF']) $msg .= "<a href='uploads/{$f['Fichier_PDF']}' download>[Télécharger Livre]</a>";
        } else { $msg = "Plus de stock."; }
    }

    // --- FONCTIONNALITE : RESERVER UN LIVRE ---
    elseif ($action === 'reserver') {
        $pdo->prepare("INSERT INTO reservation (ID_Etudiant, ISBN, Date_Reservation) VALUES (?, ?, NOW())")->execute([$id_etudiant, $_POST['isbn']]);
        $msg = "Réservation confirmée.";
    }

    // --- FONCTIONNALITE : ENVOYER MESSAGE A L'ADMIN ---
    elseif ($action === 'send_message') {
        $pdo->prepare("INSERT INTO messages (ID_Etudiant, Sens, Contenu) VALUES (?, 'EtudiantVersAdmin', ?)")->execute([$id_etudiant, $_POST['message']]);
        $msg = "Message envoyé à l'admin.";
    }
}

// Fonction helper dispo
function getDispo($pdo, $isbn) {
    $t=$pdo->query("SELECT COUNT(*) FROM exemplaire WHERE ISBN='$isbn'")->fetchColumn(); 
    $o=$pdo->query("SELECT COUNT(*) FROM emprunter em JOIN exemplaire ex ON em.ID_Exemplaire=ex.ID_Exemplaire WHERE ex.ISBN='$isbn' AND em.Date_Retour IS NULL")->fetchColumn();
    return $t - $o;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"><title>Espace Étudiant</title>
</head>
<body>

    <h1>Espace Étudiant</h1>
    <nav>
        <a href="?section=catalogue">[ Catalogue Livres ]</a> | 
        <a href="?section=mes_emprunts">[ Mes Emprunts ]</a> | 
        <a href="?section=messagerie">[ Contact Admin ]</a>
    </nav>
    <hr>

    <?php if($msg): ?><p><strong>INFO :</strong> <?= $msg ?></p><hr><?php endif; ?>

    <?php if($section == 'catalogue'): 
        $livres = $pdo->query("SELECT l.*, c.Libelle FROM livre l LEFT JOIN categorie_livre c ON l.id_categorie=c.ID_Categorie")->fetchAll();
    ?>
        <h2>📚 Catalogue</h2>
        <table border="1" width="100%">
            <tr><th>Titre</th><th>Auteur</th><th>Disponibilité</th><th>Action</th></tr>
            <?php foreach($livres as $l): $dispo = getDispo($pdo, $l['ISBN']); ?>
            <tr>
                <td><?= $l['Titre'] ?></td>
                <td><?= $l['Auteur'] ?></td>
                <td><?= $dispo > 0 ? "Disponible" : "Indisponible" ?></td>
                <td>
                    <form method="POST">
                        <input type="hidden" name="isbn" value="<?= $l['ISBN'] ?>">
                        <?php if($dispo > 0): ?>
                            <button type="submit" name="action" value="emprunter">Emprunter (15j)</button>
                        <?php else: ?>
                            <button type="submit" name="action" value="reserver">Réserver</button>
                        <?php endif; ?>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>

    <?php elseif($section == 'mes_emprunts'): 
        $mes_emprunts = $pdo->query("SELECT em.*, l.Titre FROM emprunter em JOIN exemplaire ex ON em.ID_Exemplaire=ex.ID_Exemplaire JOIN livre l ON ex.ISBN=l.ISBN WHERE em.ID_Etudiant=$id_etudiant AND em.Date_Retour IS NULL")->fetchAll();
    ?>
        <h2>📂 Mes Livres en cours</h2>
        <table border="1" width="100%">
            <tr><th>Livre</th><th>Date fin</th><th>État (Compteur)</th><th>Preuve</th></tr>
            <?php foreach($mes_emprunts as $me): ?>
            <tr>
                <td><?= $me['Titre'] ?></td>
                <td><?= $me['Date_Retour_Prevue'] ?></td>
                <td><?= genererBadge($me['Date_Retour_Prevue']) ?></td>
                <td><a href="?mode=recu_pdf&id=<?= $me['ID_Emprunt'] ?>" target="_blank">Télécharger Reçu</a></td>
            </tr>
            <?php endforeach; ?>
        </table>

    <?php elseif($section == 'messagerie'): 
        $msgs = $pdo->query("SELECT * FROM messages WHERE ID_Etudiant=$id_etudiant ORDER BY Date_Envoi ASC")->fetchAll();
    ?>
        <h2>💬 Discussion avec l'Admin</h2>
        
        <div style="border:1px solid black; padding:10px; height:300px; overflow-y:scroll; margin-bottom:10px;">
            <?php foreach($msgs as $m): ?>
                <p>
                    <strong><?= $m['Sens']=='EtudiantVersAdmin' ? 'MOI' : 'ADMIN' ?> :</strong><br>
                    <?= $m['Contenu'] ?>
                    <br><small><i><?= $m['Date_Envoi'] ?></i></small>
                </p>
                <hr>
            <?php endforeach; ?>
        </div>

        <form method="POST">
            <input type="hidden" name="action" value="send_message">
            <input type="text" name="message" placeholder="Écrire un message..." required style="width:80%;">
            <button type="submit">Envoyer</button>
        </form>

    <?php endif; ?>

</body>
</html>
