<?php
if (!function_exists('genererRecu')) {
    function genererRecu($pdo, $id_emprunt) {
        $stmt = $pdo->prepare("SELECT e.Nom, l.Titre, em.Date_Emprunt, em.Date_Retour_Prevue 
                               FROM emprunter em 
                               JOIN etudiant e ON em.ID_Etudiant=e.ID_Etudiant 
                               JOIN exemplaire ex ON em.ID_Exemplaire=ex.ID_Exemplaire 
                               JOIN livre l ON ex.ISBN=l.ISBN 
                               WHERE em.ID_Emprunt=?");
        $stmt->execute([$id_emprunt]);
        $d = $stmt->fetch();
        if (!$d) return "Reçu introuvable.";

        return "<body onload='window.print()'>
                    <div style='border:1px solid black; padding:20px;'>
                        <h1>REÇU D'EMPRUNT</h1>
                        <p>Livre : ".htmlspecialchars($d['Titre'])."</p>
                        <p>Emprunteur : ".htmlspecialchars($d['Nom'])."</p>
                        <p>Date emprunt : ".$d['Date_Emprunt']."</p>
                        <p>Date retour : ".$d['Date_Retour_Prevue']."</p>
                    </div>
                </body>";
    }
}
?>
