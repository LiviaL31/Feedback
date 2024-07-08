<?php
require 'config/database.php';

$form_id = $_POST['form_id'];
$responses = $_POST['responses'];
$emotion = $_POST['emotion'];

$conn = Database::connect();
$stmt = $conn->prepare("INSERT INTO feedback (form_id, responses, emotion) VALUES (:form_id, :responses, :emotion)");
$stmt->execute([
    'form_id' => $form_id,
    'responses' => json_encode($responses),
    'emotion' => $emotion
]);

echo "Feedback trimis cu succes";
?>