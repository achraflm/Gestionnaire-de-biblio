<?php

function retour_livre($pdo) {
    $pdo->prepare("UPDATE emprunter SET Date_Retour = CURDATE() WHERE ID_Emprunt=?")
        ->execute([$_POST['id']]);
    return "Retour enregistré.";
}
?>