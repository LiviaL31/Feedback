<?php
session_start();
require 'config/database.php';

$user_id = $_SESSION['user_id'];

$conn = Database::connect();
$stmt = $conn->prepare("SELECT * FROM forms WHERE user_id = :user_id AND expiry_date <= CURRENT_DATE");
$stmt->execute(['user_id' => $user_id]);
$forms = $stmt->fetchAll(PDO::FETCH_ASSOC);

$response = [];
foreach ($forms as $form) {
    $stmt = $conn->prepare("SELECT * FROM feedback WHERE form_id = :form_id");
    $stmt->execute(['form_id' => $form['id']]);
    $feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $response[] = [
        'form' => $form,
        'feedbacks' => $feedbacks
    ];
}

echo json_encode($response);
?>