<?php
require_once 'config.php';
require_once 'actions/Action Admin/emprunts.php';    
require_once 'actions/Action Admin/Etudiant.php';     
require_once 'actions/Action Admin/Livre.php';        
require_once 'actions/Action Admin/messages.php';      
require_once 'actions/Action Admin/reservations.php'; 
$msg = "";
$section = $_GET['section'] ?? 'dashboard';
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin - Bibliothèque</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-Ss+8A4ckf5+E9gWZ8yecM+6x+PL2soMH6N9uZp9NvDRLRkL7v+5b6J/EEj3vYq0tT96+j1T3aQWw5+8RLs0nRg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="general.css">
</head>
<body>

    <h1>🛡️ Panneau Administrateur</h1>
    <nav>
        <a href="?section=dashboard">[ Dashboard ]</a> | 
        <a href="?section=livres">[ Livres ]</a> | 
        <a href="?section=etudiants">[ Étudiants ]</a> | 
        <a href="?section=messagerie">[ Messagerie ]</a>
    </nav>
    <hr>

    <?php if($msg): ?><p style="background:#eee; padding:5px; border:1px solid #ccc;"><strong>INFO :</strong> <?= $msg ?></p><hr><?php endif; ?>

    <?php if($section == 'dashboard'): 
        $emp = $pdo->query("SELECT em.*, e.Nom, l.Titre 
                            FROM emprunter em 
                            JOIN etudiant e ON em.ID_Etudiant = e.ID_Etudiant 
                            JOIN exemplaire ex ON em.ID_Exemplaire = ex.ID_Exemplaire 
                            JOIN livre l ON ex.ISBN = l.ISBN 
                            WHERE em.Date_Retour IS NULL 
                            ORDER BY em.Date_Retour_Prevue ASC")->fetchAll();
                            
        $res = $pdo->query("SELECT r.*, e.Nom, l.Titre FROM reservation r JOIN etudiant e ON r.ID_Etudiant=e.ID_Etudiant JOIN livre l ON r.ISBN=l.ISBN")->fetchAll();
    ?>
        <h2>Validations & Retours</h2>
        
        <h3>Emprunts en cours</h3>
        <table border="1" width="100%">
            <tr><th>Étudiant</th><th>Livre</th><th>Fin Prévue</th><th>État</th><th>Action</th></tr>
            <?php foreach($emp as $e): ?>
            <tr>
                <td><?= $e['Nom'] ?></td>
                <td><?= $e['Titre'] ?></td>
                <td><?= $e['Date_Retour_Prevue'] ?></td>
                <td><?= genererBadge($e['Date_Retour_Prevue']) ?></td>
                <td>
                    <form method="POST">
                        <input type="hidden" name="action" value="retour_livre">
                        <input type="hidden" name="id" value="<?= $e['ID_Emprunt'] ?>">
                        <button type="submit">Retourné</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>

        <h3>Réservations en attente</h3>
        <table border="1" width="100%">
            <tr><th>Étudiant</th><th>Livre</th><th>Action</th></tr>
            <?php foreach($res as $r): ?>
            <tr>
                <td><?= $r['Nom'] ?></td>
                <td><?= $r['Titre'] ?></td>
                <td>
                    <form method="POST">
                        <input type="hidden" name="action" value="valider_resa">
                        <input type="hidden" name="id" value="<?= $r['ID_Reservation'] ?>">
                        <button type="submit">Valider</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>

    <?php elseif($section == 'etudiants'): 
        $ed_etu = isset($_GET['edit']) ? $pdo->query("SELECT * FROM etudiant WHERE ID_Etudiant={$_GET['edit']}")->fetch() : null;
        $etudiants = $pdo->query("SELECT * FROM etudiant")->fetchAll();
    ?>
        <h2>Gestion des Étudiants</h2>
        
        <form method="POST" style="border:1px solid #ccc; padding:10px; margin-bottom:20px;">
            <input type="hidden" name="action" value="save_etudiant">
            <?php if($ed_etu): ?><input type="hidden" name="id_etudiant" value="<?= $ed_etu['ID_Etudiant'] ?>"><?php endif; ?>
            
            <input type="text" name="nom" placeholder="Nom" value="<?= $ed_etu['Nom']??'' ?>" required>
            <input type="text" name="prenom" placeholder="Prénom" value="<?= $ed_etu['Prenom']??'' ?>" required>
            <input type="email" name="email" placeholder="Email" value="<?= $ed_etu['Email']??'' ?>" required>
            <input type="text" name="mdp" placeholder="Mot de passe" value="<?= $ed_etu['Mot_de_passe']??'' ?>" required>
            <button type="submit">Enregistrer</button>
        </form>

        <table border="1" width="100%">
            <tr><th>Nom</th><th>Email / MDP</th><th>Statut</th><th>Message Rapide</th><th>Actions</th></tr>
            <?php foreach($etudiants as $e): ?>
            <tr>
                <td><?= $e['Nom']." ".$e['Prenom'] ?></td>
                <td><?= $e['Email'] ?> <br> (<?= $e['Mot_de_passe'] ?>)</td>
                <td><?= $e['Statut'] ?></td>
                <td>
                    <form method="POST" style="display:flex;">
                        <input type="hidden" name="action" value="send_message">
                        <input type="hidden" name="id_etudiant" value="<?= $e['ID_Etudiant'] ?>">
                        <input type="text" name="message" placeholder="Écrire..." required style="width:100px;">
                        <button type="submit">Envoyer</button>
                    </form>
                </td>
                <td>
                    <a href="?section=etudiants&edit=<?= $e['ID_Etudiant'] ?>">Modifier</a> | 
                    <form method="POST" style="display:inline;"><input type="hidden" name="action" value="toggle_ban"><input type="hidden" name="id" value="<?= $e['ID_Etudiant'] ?>"><button><?= $e['Statut']=='Actif'?'Bloquer':'Activer' ?></button></form> | 
                    <form method="POST" style="display:inline;" onsubmit="return confirm('Supprimer ?')"><input type="hidden" name="action" value="delete_etudiant"><input type="hidden" name="id" value="<?= $e['ID_Etudiant'] ?>"><button>X</button></form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>

    <?php elseif($section == 'livres'): 
        $ed = isset($_GET['edit']) ? $pdo->query("SELECT * FROM livre WHERE ISBN='{$_GET['edit']}'")->fetch() : null;
        $livres = $pdo->query("SELECT l.*, c.Libelle FROM livre l LEFT JOIN categorie_livre c ON l.id_categorie=c.ID_Categorie")->fetchAll();
    ?>
        <h2>Gestion Livres</h2>
        <form method="POST" enctype="multipart/form-data" style="border:1px solid #ccc; padding:10px;">
            <input type="hidden" name="action" value="save_livre">
            <?php if($ed): ?><input type="hidden" name="old_isbn" value="<?= $ed['ISBN'] ?>"><?php endif; ?>
            <input type="text" name="isbn" placeholder="ISBN" value="<?= $ed['ISBN']??'' ?>" required>
            <input type="text" name="titre" placeholder="Titre" value="<?= $ed['Titre']??'' ?>" required>
            <input type="text" name="auteur" placeholder="Auteur" value="<?= $ed['Auteur']??'' ?>" required>
            <input type="text" name="categorie" placeholder="Catégorie" value="<?= $ed['id_categorie']??'' ?>" required>
            <input type="hidden" name="current_pdf" value="<?= $ed['Fichier_PDF']??'' ?>">
            <input type="file" name="pdf_livre" accept=".pdf">
            <button type="submit">Enregistrer</button>
        </form>
        <br>
        <table border="1" width="100%">
            <tr><th>Titre</th><th>Auteur</th><th>PDF</th><th>Actions</th></tr>
            <?php foreach($livres as $l): ?>
            <tr>
                <td><?= $l['Titre'] ?></td><td><?= $l['Auteur'] ?></td><td><?= $l['Fichier_PDF']?'OUI':'NON' ?></td>
                <td>
                    <a href="?section=livres&edit=<?= $l['ISBN'] ?>">Modif</a> | 
                    <form method="POST" style="display:inline" onsubmit="return confirm('Suppr?')"><input type="hidden" name="action" value="delete_livre"><input type="hidden" name="isbn" value="<?= $l['ISBN'] ?>"><button>X</button></form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>

    <?php elseif($section == 'messagerie'): 
        $users = $pdo->query("SELECT DISTINCT m.ID_Etudiant, e.Nom, e.Prenom FROM messages m JOIN etudiant e ON m.ID_Etudiant=e.ID_Etudiant")->fetchAll();
    ?>
        <h2>Messagerie</h2>
        <?php foreach($users as $u): 
            $msgs = $pdo->query("SELECT * FROM messages WHERE ID_Etudiant={$u['ID_Etudiant']} ORDER BY Date_Envoi ASC")->fetchAll();
        ?>
        <div style="border:1px solid #000; padding:10px; margin-bottom:10px;">
            <strong>Conversation avec <?= $u['Nom'] ?> :</strong>
            <form method="POST" style="float:right;" onsubmit="return confirm('Vider ?')">
                <input type="hidden" name="action" value="clear_chat"><input type="hidden" name="id_etudiant" value="<?= $u['ID_Etudiant'] ?>">
                <button>Vider</button>
            </form>
            <div style="height:150px; overflow-y:scroll; background:#f9f9f9; border:1px solid #ccc; margin:5px 0; padding:5px;">
                <?php foreach($msgs as $m): ?>
                    <div style="text-align:<?= $m['Sens']=='AdminVersEtudiant'?'right':'left' ?>">
                        <span style="background:<?= $m['Sens']=='AdminVersEtudiant'?'#ddd':'#cfc' ?>; padding:3px;">
                            <?= $m['Contenu'] ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="send_message">
                <input type="hidden" name="id_etudiant" value="<?= $u['ID_Etudiant'] ?>">
                <input type="text" name="message" placeholder="Répondre..." required style="width:80%">
                <button type="submit">Envoyer</button>
            </form>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>

</body>
</html>
