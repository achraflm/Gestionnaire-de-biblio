<?php
require_once 'config.php';


function getOrInsertCategorieID($libelle_categorie) {
    global $pdo;
    $libelle_categorie = trim($libelle_categorie);
    $stmt = $pdo->prepare("SELECT ID_Categorie FROM categorie_livre WHERE Libelle = :libelle");
    $stmt->execute([':libelle' => $libelle_categorie]);
    $result = $stmt->fetch();

    if ($result) {
        return $result['ID_Categorie']; 
    } else {
        $stmt_ins = $pdo->prepare("INSERT INTO categorie_livre (Libelle) VALUES (:libelle)");
        $stmt_ins->execute([':libelle' => $libelle_categorie]);
        return $pdo->lastInsertId(); 
    }
}

// --- CRUD LIVRES ---
function createLivre($isbn, $titre, $auteur, $cat_libelle) {
    global $pdo;
    $id_cat = getOrInsertCategorieID($cat_libelle);
    $sql = "INSERT INTO livre (ISBN, Titre, Auteur, id_categorie) VALUES (?, ?, ?, ?)";
    return $pdo->prepare($sql)->execute([$isbn, $titre, $auteur, $id_cat]);
}

function updateLivre($original_isbn, $isbn, $titre, $auteur, $cat_libelle) {
    global $pdo;
    $id_cat = getOrInsertCategorieID($cat_libelle);
    $sql = "UPDATE livre SET ISBN=?, Titre=?, Auteur=?, id_categorie=? WHERE ISBN=?";
    return $pdo->prepare($sql)->execute([$isbn, $titre, $auteur, $id_cat, $original_isbn]);
}

function readLivres() {
    global $pdo;
    return $pdo->query("SELECT livre.*, categorie_livre.Libelle as NomCategorie 
                        FROM livre 
                        LEFT JOIN categorie_livre ON livre.id_categorie = categorie_livre.ID_Categorie 
                        ORDER BY Titre ASC")->fetchAll();
}

function deleteLivre($isbn) {
    global $pdo;
    return $pdo->prepare("DELETE FROM livre WHERE ISBN=?")->execute([$isbn]);
}

// --- CRUD ÉTUDIANTS  ---

function createEtudiant($nom, $prenom, $email, $mdp) {
    global $pdo;
    $sql = "INSERT INTO etudiant (Nom, Prenom, Email, Mot_de_passe, Date_Inscription, Statut) VALUES (?, ?, ?, ?, CURDATE(), 'Actif')";
    return $pdo->prepare($sql)->execute([$nom, $prenom, $email, $mdp]);
}

function updateEtudiant($id, $nom, $prenom, $email, $mdp, $statut) {
    global $pdo;
    $sql = "UPDATE etudiant SET Nom=?, Prenom=?, Email=?, Mot_de_passe=?, Statut=? WHERE ID_Etudiant=?";
    return $pdo->prepare($sql)->execute([$nom, $prenom, $email, $mdp, $statut, $id]);
}

function deleteEtudiant($id) {
    global $pdo;
    return $pdo->prepare("DELETE FROM etudiant WHERE ID_Etudiant=?")->execute([$id]);
}

function readEtudiants() {
    global $pdo;
    return $pdo->query("SELECT * FROM etudiant ORDER BY ID_Etudiant DESC LIMIT 15")->fetchAll();
}

// ==================================================================

$page = $_GET['page'] ?? 'livres'; 
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $section = $_POST['section'];

    try {
        // --- SECTION LIVRE ---
        if ($section == 'livre') {
            if ($action == 'create') {
                createLivre($_POST['isbn'], $_POST['titre'], $_POST['auteur'], $_POST['categorie']);
                $message = "✅ Livre ajouté !";
            }
            if ($action == 'update') {
                updateLivre($_POST['original_isbn'], $_POST['isbn'], $_POST['titre'], $_POST['auteur'], $_POST['categorie']);
                $message = "✅ Livre mis à jour !";
            }
            if ($action == 'delete') {
                deleteLivre($_POST['isbn']);
                $message = "🗑️ Livre supprimé !";
            }
            $page = 'livres';
        }
        // --- SECTION ÉTUDIANT ---
        elseif ($section == 'etudiant') {
            if ($action == 'create') {
                createEtudiant($_POST['nom'], $_POST['prenom'], $_POST['email'], $_POST['mdp']);
                $message = "✅ Étudiant ajouté !";
            }
            if ($action == 'update') {
                // On récupère tous les champs du formulaire intégré au tableau
                updateEtudiant($_POST['id'], $_POST['nom'], $_POST['prenom'], $_POST['email'], $_POST['mdp'], $_POST['statut']);
                $message = "✅ Étudiant modifié !";
            }
            if ($action == 'delete') {
                deleteEtudiant($_POST['id']);
                $message = "🗑️ Étudiant supprimé !";
            }
            $page = 'etudiants';
        }
    } catch (Exception $e) {
        $message = "❌Erreur : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion Complète</title>
</head>
<body style="font-family: sans-serif; padding: 20px;">

    <h1>📚 Système de Gestion Bibliothèque</h1>
    
    <div style="background: #eee; padding: 10px; border: 1px solid #ccc;">
        <strong>Aller vers : </strong>
        <a href="?page=etudiants"><button>👥 Gérer les Étudiants</button></a>
        &nbsp;|&nbsp; 
        <a href="?page=livres"><button>📚 Gérer les Livres</button></a>
    </div>

    <?php if($message): ?>
        <p style="background: lightyellow; border: 1px solid gold; padding: 10px;">
            📢 <strong>Info :</strong> <?= $message ?>
        </p>
    <?php endif; ?>

    <?php if ($page == 'livres'): ?>
        <h2>Gestion des Livres (Catégories Auto)</h2>
        
        <?php 
        $cats = $pdo->query("SELECT Libelle FROM categorie_livre")->fetchAll(PDO::FETCH_COLUMN); 
        ?>
        <datalist id="liste_categories">
            <?php foreach($cats as $c) echo "<option value=\"$c\">"; ?>
        </datalist>

        <fieldset>
            <legend>Nouveau Livre</legend>
            <form method="POST">
                <input type="hidden" name="section" value="livre">
                <input type="hidden" name="action" value="create">
                
                <input type="text" name="isbn" placeholder="ISBN" required>
                <input type="text" name="titre" placeholder="Titre" required>
                <input type="text" name="auteur" placeholder="Auteur" required>
                <input type="text" name="categorie" list="liste_categories" placeholder="Catégorie (Ex: Manga)" required>
                <small>(Tapez une nouvelle catégorie pour la créer automatiquement)</small>

                <button type="submit">Ajouter</button>
            </form>
        </fieldset>

        <br>

        <table border="1" cellpadding="5" cellspacing="0" width="100%">
            <tr bgcolor="#ddd">
                <th>ISBN</th>
                <th>Titre</th>
                <th>Auteur</th>
                <th>Catégorie</th>
                <th>Action</th>
            </tr>
            <?php foreach(readLivres() as $l): ?>
            <tr>
                <td><?= $l['ISBN'] ?></td>
                <td><?= $l['Titre'] ?></td>
                <td><?= $l['Auteur'] ?></td>
                <td><b><?= $l['NomCategorie'] ?></b></td>
                <td>
                    <form method="POST" style="display:inline">
                        <input type="hidden" name="section" value="livre">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="original_isbn" value="<?= $l['ISBN'] ?>">
                        
                        <input type="text" name="isbn" value="<?= $l['ISBN'] ?>" size="8">
                        <input type="text" name="titre" value="<?= $l['Titre'] ?>" size="10">
                        <input type="text" name="categorie" list="liste_categories" value="<?= $l['NomCategorie'] ?>" size="10">
                        <button type="submit">💾</button>
                    </form>

                    <form method="POST" style="display:inline" onsubmit="return confirm('Supprimer ?');">
                        <input type="hidden" name="section" value="livre">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="isbn" value="<?= $l['ISBN'] ?>">
                        <button type="submit">❌</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>

    <?php elseif ($page == 'etudiants'): ?>
        <h2>Gestion des Étudiants</h2>
        
        <fieldset>
            <legend>Nouvel Étudiant</legend>
            <form method="POST">
                <input type="hidden" name="section" value="etudiant">
                <input type="hidden" name="action" value="create">
                
                <input type="text" name="nom" placeholder="Nom" required>
                <input type="text" name="prenom" placeholder="Prénom" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="text" name="mdp" placeholder="Mot de passe" required>
                <button type="submit">Ajouter</button>
            </form>
        </fieldset>

        <br>

        <table border="1" cellpadding="5" cellspacing="0" width="100%">
            <tr bgcolor="#ddd">
                <th>ID</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Email</th>
                <th>Mot de Passe (Clair)</th>
                <th>Statut</th>
                <th>Action</th>
            </tr>
            <?php foreach(readEtudiants() as $e): ?>
            <tr>
                <form method="POST">
                    <input type="hidden" name="section" value="etudiant">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?= $e['ID_Etudiant'] ?>">

                    <td><?= $e['ID_Etudiant'] ?></td>
                    <td><input type="text" name="nom" value="<?= $e['Nom'] ?>" size="8"></td>
                    <td><input type="text" name="prenom" value="<?= $e['Prenom'] ?>" size="8"></td>
                    <td><input type="text" name="email" value="<?= $e['Email'] ?>" size="15"></td>
                    <td><input type="text" name="mdp" value="<?= $e['Mot_de_passe'] ?>" size="10" style="background:#fffeb3"></td>
                    <td>
                        <select name="statut">
                            <option value="Actif" <?= $e['Statut']=='Actif'?'selected':'' ?>>Actif</option>
                            <option value="Bloqué" <?= $e['Statut']=='Bloqué'?'selected':'' ?>>Bloqué</option>
                        </select>
                    </td>
                    <td style="white-space:nowrap;">
                        <button type="submit" title="Enregistrer">💾</button>
                </form>

                <form method="POST" style="display:inline" onsubmit="return confirm('Supprimer ?');">
                        <input type="hidden" name="section" value="etudiant">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= $e['ID_Etudiant'] ?>">
                        <button type="submit" title="Supprimer">❌</button>
                </form>
                    </td>
            </tr>
            <?php endforeach; ?>
        </table>

    <?php endif; ?>

</body>
</html>
