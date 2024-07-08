<?php
$conn = new mysqli("localhost", "root", "", "feedback");

if ($conn->connect_error) {
    die("Conexiunea la bază de date a eșuat: " . $conn->connect_error);
}

$formularId = isset($_GET['formularId']) ? $_GET['formularId'] : '';

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

// Returnăm un obiect JSON care conține doar datele pentru grafic
echo json_encode($data);
?>