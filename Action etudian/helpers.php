<?php
if (!function_exists('genererBadge')) {
    function genererBadge($date_retour) {
        $today = date('Y-m-d');
        if ($today > $date_retour) return "<span style='color:red'>En retard</span>";
        return "<span style='color:green'>OK</span>";
    }
}

if (!function_exists('getDispo')) {
    function getDispo($pdo, $isbn) {
        $t = $pdo->query("SELECT COUNT(*) FROM exemplaire WHERE ISBN='$isbn'")->fetchColumn(); 
        $o = $pdo->query("SELECT COUNT(*) FROM emprunter em 
                          JOIN exemplaire ex ON em.ID_Exemplaire=ex.ID_Exemplaire 
                          WHERE ex.ISBN='$isbn' AND em.Date_Retour IS NULL")->fetchColumn();
        return $t - $o;
    }
}
?>
