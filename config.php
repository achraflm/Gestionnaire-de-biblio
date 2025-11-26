<?php
// config.php
$host = 'localhost';
$dbname = 'gestionbibliotheque'; // Vérifiez que c'est le bon nom de votre base de données
$username = 'root';
$password = ''; // Laissez vide pour XAMPP par défaut

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>