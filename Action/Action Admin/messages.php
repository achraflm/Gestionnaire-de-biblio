<?php

function send_message($pdo) {
    $pdo->prepare("INSERT INTO messages (ID_Etudiant, Sens, Contenu) VALUES (?, 'AdminVersEtudiant', ?)")
        ->execute([$_POST['id_etudiant'], $_POST['message']]);
    return "✉️ Message envoyé.";
}

function clear_chat($pdo) {
    $pdo->prepare("DELETE FROM messages WHERE ID_Etudiant=?")->execute([$_POST['id_etudiant']]);
    return "Conversation effacée.";
}
?>