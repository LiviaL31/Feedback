<?php
// Conectare la baza de date
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "feedback";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verifică conexiunea
if ($conn->connect_error) {
    die("Conexiunea a eșuat: " . $conn->connect_error);
}

// Verifică dacă formularul este trimis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tip_feedback = $conn->real_escape_string($_POST['tip_feedback']);
    $feedback = $conn->real_escape_string($_POST['feedback']);

    // Interogare SQL pentru inserarea feedback-ului anonim
    $sql = "INSERT INTO feedback (tip_feedback, feedback) VALUES ('$tip_feedback', '$feedback')";

    if ($conn->query($sql) === TRUE) {
        echo "Feedback-ul a fost trimis cu succes!";
    } else {
        echo "Eroare: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>