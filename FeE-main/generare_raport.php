<?php
session_start();

$conn = new mysqli("localhost", "root", "root", "feedback");

if ($conn->connect_error) {
    die("Conexiunea la bază de date a eșuat: " . $conn->connect_error);
}

// Funcție pentru a genera raportul
function generateAggregateReport($conn) {
    $reports = [];
    $tables = ['artefacte_artistice', 'evenimente', 'locatii', 'persoane', 'produse', 'servicii'];
    foreach ($tables as $table) {
        $sql = "SELECT COUNT(*) as total FROM $table";
        $result = $conn->query($sql);
        if ($result) {
            $row = $result->fetch_assoc();
            $reports[$table] = $row['total'];
        } else {
            $reports[$table] = 0;
        }
    }
    return $reports;
}

// Include funcțiile de generare a rapoartelor
require_once 'generare_rapoarte.php';

// Generăm datele pentru raport
$rapoarte = generateAggregateReport($conn);

// Verificăm dacă formatul cerut este setat
if (isset($_GET['format'])) {
    $format = $_GET['format'];

    // Verificăm formatul cerut și generăm raportul corespunzător
    switch ($format) {
        case 'HTML':
            $content = generateHTMLReport($rapoarte);
            header('Content-Type: text/html');
            break;
        case 'CSV':
            $content = generateCSVReport($rapoarte);
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="raport.csv"');
            break;
        case 'JSON':
            $content = generateJSONReport($rapoarte);
            header('Content-Type: application/json');
            break;
        default:
            $content = "Formatul cerut nu este suportat.";
            break;
    }

    // Returnăm conținutul raportului
    echo $content;
} else {
    echo "Formatul cerut lipsește.";
}

$conn->close();
?>