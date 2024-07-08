<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "feedback";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexiunea la baza de date a eșuat: " . $conn->connect_error);
}

$formularId = isset($_GET['formularId']) ? $_GET['formularId'] : '';

// Interogare pentru a extrage datele relevante pentru formularul specificat
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

// Generare tabel HTML
$html = '<html><head><title>Raport HTML</title></head><body>';
$html .= '<h2>Raport HTML pentru formularul cu ID-ul ' . $formularId . '</h2>';
$html .= '<table border="1"><tr><th>Emotie</th><th>Numar Feedback</th></tr>';
foreach ($data as $item) {
    $html .= '<tr><td>' . $item['emotie'] . '</td><td>' . $item['numar_feedback'] . '</td></tr>';
}
$html .= '</table></body></html>';

// Returnare conținut HTML
header('Content-Type: text/html');
echo $html;
?>
