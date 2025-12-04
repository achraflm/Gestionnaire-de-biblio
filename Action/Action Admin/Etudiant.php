<?php

function save_etudiant($pdo) {
    $nom=$_POST['nom'];
    $prenom=$_POST['prenom'];
    $email=$_POST['email'];
    $mdp=$_POST['mdp'];

    if (!empty($_POST['id_etudiant'])) {
        $pdo->prepare("UPDATE etudiant SET Nom=?, Prenom=?, Email=?, Mot_de_passe=? WHERE ID_Etudiant=?")
            ->execute([$nom, $prenom, $email, $mdp, $_POST['id_etudiant']]);
        return "Étudiant modifié.";
    } else {
        $pdo->prepare("INSERT INTO etudiant (Nom, Prenom, Email, Mot_de_passe, Date_Inscription) VALUES (?, ?, ?, ?, CURDATE())")
            ->execute([$nom, $prenom, $email, $mdp]);
        return "Étudiant créé.";
    }
}

function toggle_ban($pdo) {
    $pdo->prepare("UPDATE etudiant SET Statut = IF(Statut='Actif', 'Bloqué', 'Actif') WHERE ID_Etudiant=?")
        ->execute([$_POST['id']]);
    return "Statut changé.";
}

function delete_etudiant($pdo) {
    $pdo->prepare("DELETE FROM etudiant WHERE ID_Etudiant=?")->execute([$_POST['id']]);
    return "Étudiant supprimé.";
}
?>