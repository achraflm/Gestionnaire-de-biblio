<?php
// verification de la session et du role
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

if (!est_etudiant()) {
    redirection('../index.php');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Espace Etudiant</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <header>
        <h1>Bibliotheque - Espace Etudiant</h1>
        <nav>
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <li><a href="livres.php">Liste des Livres</a></li>
                <li><a href="mes_favoris.php">Mes Favoris</a></li>
                <li><a href="mes_emprunts.php">Mes Emprunts</a></li>
                <li><a href="suggerer_livre.php">Suggérer un Livre</a></li>
                <li><a href="profil.php">Mon Profil</a></li>
                <li><a href="contact.php">Contact Admin</a></li>
                <li><button id="darkModeToggle" style="background:none; border:none; cursor:pointer; font-size:20px;">🌙</button></li>
                <li><a href="../logout.php" class="btn-danger" style="padding: 5px 10px; color: white; text-decoration: none;">Deconnexion</a></li>
            </ul>
        </nav>
    </header>
    <div class="container">

    <script>
        const toggleButton = document.getElementById('darkModeToggle');
        const body = document.body;

        // Check local storage
        if (localStorage.getItem('darkMode') === 'enabled') {
            body.classList.add('dark-mode');
            toggleButton.textContent = '☀️';
        }

        toggleButton.addEventListener('click', () => {
            body.classList.toggle('dark-mode');
            if (body.classList.contains('dark-mode')) {
                localStorage.setItem('darkMode', 'enabled');
                toggleButton.textContent = '☀️';
            } else {
                localStorage.setItem('darkMode', 'disabled');
                toggleButton.textContent = '🌙';
            }
        });
    </script>
    <link rel="stylesheet" href="../css/dark.css">
