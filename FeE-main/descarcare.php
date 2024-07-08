<?php
// Includem fisierul cu datele rapoartelor si functiile de generare a acestora
require_once 'generate_reports.php';

// Verificam daca formatul cerut este setat
if (isset($_GET['format'])) {
    $format = $_GET['format'];

    // Verificam formatul cerut si generam raportul corespunzator
    switch ($format) {
        case 'HTML':
            $content = generateHTMLReport($rapoarte);
            $filename = 'raport.html';
            $mime = 'text/html';
            break;
        case 'CSV':
            $content = generateCSVReport($rapoarte);
            $filename = 'raport.csv';
            $mime = 'text/csv';
            break;
        case 'JSON':
            $content = generateJSONReport($rapoarte);
            $filename = 'raport.json';
            $mime = 'application/json';
            break;
        default:
            $content = "Formatul cerut nu este suportat.";
            break;
    }

    // Setam capul de raspuns HTTP pentru a indica tipul de continut si numele fisierului
    header("Content-Type: $mime");
    header("Content-Disposition: attachment; filename=$filename");

    // Returnam continutul raportului
    echo $content;
} else {
    echo "Formatul cerut lipsește.";
}
?>