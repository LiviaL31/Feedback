<?php
session_start();
$conn = new mysqli("localhost", "root", "", "feedback");

if ($conn->connect_error) {
    die("Conexiunea la bază de date a eșuat: " . $conn->connect_error);
}
if(isset($_SESSION['user_id'])){
    $user_id = $_SESSION['user_id'];
}else{
    $user_id = null;
}
$sql = "SELECT DISTINCT formular_id FROM raspunsuri WHERE user_id != {$_SESSION['user_id']}";
$result = $conn->query($sql);
/*
$sql = "SELECT DISTINCT formular_id 
        FROM raspunsuri 
        WHERE formular_id IN (
            SELECT id 
            FROM formulare 
            WHERE user_id = ?
        )";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();*/
$data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($data);
?>
