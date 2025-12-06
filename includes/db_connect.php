<?php
// connexion a la base de donnees
$serveur = "localhost";
$utilisateur = "root";
$mot_de_passe = "";
$base = "bib";

$connexion = mysqli_connect($serveur, $utilisateur, $mot_de_passe, $base);

// verification de la connexion
if (!$connexion) {
    die("echec de la connexion : " . mysqli_connect_error());
}

// encodage utf8 pour les accents
mysqli_set_charset($connexion, "utf8");
?>