<?php
require_once 'helpers.php';

if (!function_exists('emprunterLivre')) {
    function emprunterLivre($pdo, $id_etudiant, $isbn) {
        $stmt = $pdo->prepare("SELECT ID_Exemplaire FROM exemplaire WHERE ISBN=? AND ID_Exemplaire NOT IN (SELECT ID_Exemplaire FROM emprunter WHERE Date_Retour IS NULL) LIMIT 1");
        $stmt->execute([$isbn]);
        $ex = $stmt->fetch();

        if ($ex) {
            $pdo->prepare("INSERT INTO emprunter (ID_Etudiant, ID_Exemplaire, Date_Emprunt, Date_Retour_Prevue) VALUES (?, ?, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 15 DAY))")
                ->execute([$id_etudiant, $ex['ID_Exemplaire']]);
            $id = $pdo->lastInsertId();

            $f = $pdo->prepare("SELECT Fichier_PDF FROM livre WHERE ISBN=?");
            $f->execute([$isbn]);
            $file = $f->fetch();

            $msg = "Emprunt réussi ! <br>";
            $msg .= "<a href='?mode=recu_pdf&id=$id' target='_blank'>[Télécharger Reçu]</a> ";
            if ($file && $file['Fichier_PDF']) $msg .= "<a href='uploads/{$file['Fichier_PDF']}' download>[Télécharger Livre]</a>";
            return $msg;
        } else {
            return "Plus de stock.";
        }
    }
}
?>
