<?php
// --- CONNEXION A LA BASE DE DONNEES ---
$host = 'localhost';
$dbname = 'bib';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur connexion : " . $e->getMessage());
}

// --- FONCTION : CALCUL DU TEMPS RESTANT (Badge texte sans style) ---
// Cette fonction compare la date de fin prévue avec aujourd'hui
function genererBadge($date_fin) {
    if (!$date_fin) return "-";
    $diff = (new DateTime())->diff(new DateTime($date_fin));
    $jours = $diff->days;
    
    if ($diff->invert) return "[RETARD de $jours jours]";
    if ($jours <= 3) return "[URGENT : Reste $jours jours]";
    return "[OK : Reste $jours jours]";
}
?>
