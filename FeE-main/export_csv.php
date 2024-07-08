<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "feedback";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexiunea la baza de date a eșuat: " . $conn->connect_error);
}

// Verificare și preluare parametru formularId din GET
$formularId = isset($_GET['formularId']) ? $_GET['formularId'] : '';

// Interogare pentru a extrage datele relevante pentru formularul specificat
$sql = "SELECT emotie, COUNT(*) as numar_feedback FROM raspunsuri WHERE formular_id = ? GROUP BY emotie";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $formularId);
$stmt->execute();
$result = $stmt->get_result();

// Headers pentru descărcarea unui fișier CSV
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="raport.csv"');

// Deschidem pointerul către output, să putem scrie în el direct
$output = fopen('php://output', 'w');

// Scriem antetul pentru fișierul CSV
fputcsv($output, array('Emotie', 'Numar Feedback'));

// Iterăm prin rândurile din rezultatul interogării și le scriem în fișierul CSV
while ($row = $result->fetch_assoc()) {
    fputcsv($output, $row);
}

// Închidem pointerul către output
fclose($output);

// Închidem conexiunea la baza de date
$stmt->close();
$conn->close();
?>
