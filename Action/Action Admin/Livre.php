<?php

function save_livre($pdo) {
    $isbn = $_POST['isbn'];
    $titre = $_POST['titre'];
    $auteur = $_POST['auteur'];
    $cat = $_POST['categorie'];

    // Gestion PDF
    $pdf = $_POST['current_pdf'] ?? null;
    if (!empty($_FILES['pdf_livre']['name'])) {
        $pdf = time() . "_" . basename($_FILES['pdf_livre']['name']);
        move_uploaded_file($_FILES['pdf_livre']['tmp_name'], "uploads/" . $pdf);
    }

    // Catégorie
    $stmt = $pdo->prepare("SELECT ID_Categorie FROM categorie_livre WHERE Libelle=?");
    $stmt->execute([$cat]);
    $cat_id = $stmt->fetchColumn();

    if (!$cat_id) {
        $pdo->prepare("INSERT INTO categorie_livre (Libelle) VALUES (?)")->execute([$cat]);
        $cat_id = $pdo->lastInsertId();
    }

    // Ajout ou modification
    if (isset($_POST['old_isbn'])) {
        $pdo->prepare("UPDATE livre SET ISBN=?, Titre=?, Auteur=?, id_categorie=?, Fichier_PDF=? WHERE ISBN=?")
            ->execute([$isbn, $titre, $auteur, $cat_id, $pdf, $_POST['old_isbn']]);
        return "Livre modifié.";
    } else {
        $pdo->prepare("INSERT INTO livre (ISBN, Titre, Auteur, id_categorie, Fichier_PDF) VALUES (?, ?, ?, ?, ?)")
            ->execute([$isbn, $titre, $auteur, $cat_id, $pdf]);

        $pdo->prepare("INSERT INTO exemplaire (ISBN, Etat) VALUES (?, 'Neuf')")->execute([$isbn]);
        return "Livre ajouté.";
    }
}

function delete_livre($pdo) {
    $pdo->prepare("DELETE FROM livre WHERE ISBN=?")->execute([$_POST['isbn']]);
    return "Livre supprimé.";
}
?>