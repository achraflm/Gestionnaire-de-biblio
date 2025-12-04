<?php
if (!function_exists('sendMessageToAdmin')) {
    function sendMessageToAdmin($pdo, $id_etudiant, $message) {
        $stmt = $pdo->prepare("INSERT INTO messages (ID_Etudiant, Sens, Contenu) VALUES (?, 'EtudiantVersAdmin', ?)");
        $stmt->execute([$id_etudiant, $message]);
        return "Message envoyé à l'admin.";
    }
}
?>
