<?php
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

if (!est_etudiant()) {
    redirection('../index.php');
}

// verification existence librairie
if (!file_exists('../fpdf/fpdf.php')) {
    die("Erreur : Le fichier fpdf/fpdf.php est manquant. Veuillez le telecharger.");
}

require('../fpdf/fpdf.php');

if (isset($_GET['id'])) {
    $id_emprunt = (int)$_GET['id'];
    $id_etudiant = $_SESSION['user_id'];

    // recuperation infos emprunt
    $requete = "SELECT e.*, l.Titre, a.Nom_Auteur, l.ISBN, s.Nom, s.Prenom 
                FROM emprunt e 
                JOIN exemplaire ex ON e.ID_Exemplaire = ex.ID_Exemplaire
                JOIN livre l ON ex.ISBN = l.ISBN 
                LEFT JOIN auteur a ON l.ID_Auteur = a.ID_Auteur
                JOIN student s ON e.ID_Etudiant = s.ID_Etudiant
                WHERE e.ID_Emprunt = $id_emprunt AND e.ID_Etudiant = $id_etudiant";
    
    $resultat = mysqli_query($connexion, $requete);
    
    if ($resultat && mysqli_num_rows($resultat) == 1) {
        $data = mysqli_fetch_assoc($resultat);

        // creation pdf
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        
        $pdf->Cell(40, 10, 'Recu d\'emprunt');
        $pdf->Ln(20);
        
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(40, 10, 'Etudiant : ' . $data['Nom'] . ' ' . $data['Prenom']);
        $pdf->Ln(10);
        $pdf->Cell(40, 10, 'Livre : ' . $data['Titre']);
        $pdf->Ln(10);
        $pdf->Cell(40, 10, 'Auteur : ' . $data['Nom_Auteur']);
        $pdf->Ln(10);
        $pdf->Cell(40, 10, 'ISBN : ' . $data['ISBN']);
        $pdf->Ln(10);
        $pdf->Cell(40, 10, 'Date d\'emprunt : ' . $data['Date_Emprunt']);
        $pdf->Ln(10);
        $pdf->Cell(40, 10, 'A rendre le : ' . $data['Date_Retour_Prevu']);
        $pdf->Ln(20);
        
        $pdf->SetFont('Arial', 'I', 10);
        $pdf->Cell(0, 10, 'Ceci est un document genere automatiquement.', 0, 1, 'C');
        
        $pdf->Output();
    } else {
        echo "Emprunt non trouve.";
    }
} else {
    redirection("mes_emprunts.php");
}
?>
