<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "feedback";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexiunea a eșuat: " . $conn->connect_error);
}

// Verificăm dacă există un parametru formularId în cererea GET
$formularId = isset($_GET['formularId']) ? $_GET['formularId'] : '';

// Debug pentru a verifica valoarea formularId
var_dump($formularId);

// Interogare pentru a extrage numărul de feedback-uri pentru fiecare emoție pentru formularul specificat
$sql = "SELECT emotie, COUNT(*) as numar_feedback FROM raspunsuri WHERE formular_id = ? GROUP BY emotie";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $formularId);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        'emotie' => $row['emotie'],
        'numar_feedback' => (int)$row['numar_feedback']
    ];
}

$stmt->close();
$conn->close();

// Setare antet pentru a indica că se va returna un fișier JSON
header('Content-Type: application/json');
header('Content-Disposition: attachment; filename="raport.json"');

// Returnarea conținutului JSON pentru descărcare
echo json_encode($data, JSON_PRETTY_PRINT);
?>
