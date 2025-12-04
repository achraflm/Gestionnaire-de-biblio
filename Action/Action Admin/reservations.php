<?php

function valider_resa($pdo) {

    $id_res = $_POST['id'];
    $info = $pdo->query("SELECT * FROM reservation WHERE ID_Reservation=$id_res")->fetch();
    $ex = $pdo->query("SELECT ID_Exemplaire FROM exemplaire WHERE ISBN='{$info['ISBN']}' LIMIT 1")->fetch();

    $pdo->prepare("INSERT INTO emprunter (ID_Etudiant, ID_Exemplaire, Date_Emprunt, Date_Retour_Prevue) 
                    VALUES (?, ?, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 15 DAY))")
        ->execute([$info['ID_Etudiant'], $ex['ID_Exemplaire']]);

    $pdo->prepare("DELETE FROM reservation WHERE ID_Reservation=?")->execute([$id_res]);

    return "Réservation validée.";
}
?>