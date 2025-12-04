<?php
if (!function_exists('reserverLivre')) {
    function reserverLivre($pdo, $id_etudiant, $isbn) {
        $stmt = $pdo->prepare("INSERT INTO reservation (ID_Etudiant, ISBN, Date_Reservation) VALUES (?, ?, NOW())");
        $stmt->execute([$id_etudiant, $isbn]);
        return "Réservation confirmée.";
    }
}
?>
