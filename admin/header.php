<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

if (!est_admin()) {
    redirection('../index.php');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Administration - Bibliotheque</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <header style="background-color: #d9534f;"> <!-- Rouge pour admin -->
        <h1>Administration Bibliotheque</h1>
        <nav>
            <ul>
                <li><a href="index.php">Tableau de Bord</a></li>
                <li><a href="gestion_livres.php">Livres</a></li>
                <li><a href="gestion_auteurs.php">Auteurs</a></li>
                <li><a href="gestion_categories.php">Categories</a></li>
                <li><a href="gestion_etudiants.php">Etudiants</a></li>
                <li><a href="demandes_prets.php">Demandes Prets</a></li>
                <li><a href="prolongations.php">Prolongations</a></li>
                <li><a href="retours.php">Retours</a></li>
                <li><a href="messagerie.php">Messagerie</a></li>
                <li><a href="moderation_avis.php">Avis</a></li>
                <li><a href="gestion_suggestions.php">Suggestions</a></li>
                <li><button id="darkModeToggle" style="background:none; border:none; cursor:pointer; font-size:20px; color:white;">🌙</button></li>
                <li><a href="../logout.php" style="border: 1px solid white; padding: 5px; border-radius: 3px;">Deconnexion</a></li>
            </ul>
        </nav>
    </header>
    <div class="container">

    <script>
        const toggleButton = document.getElementById('darkModeToggle');
        const body = document.body;

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
