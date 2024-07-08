<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $evenimentNume = $_POST['evenimentNume'];
    $evenimentTip = $_POST['evenimentTip'];
    $evenimentData = $_POST['evenimentData'];
    $evenimentLocatie = $_POST['evenimentLocatie'];
    $evenimentOrganizator = $_POST['evenimentOrganizator'];
    $evenimentDetalii = $_POST['evenimentDetalii'];
    $evenimentEmotie = $_POST['evenimentEmotie'];

    // Conectare la baza de date
    $conn = new mysqli("localhost", "root", "", "feedback_db");

    if ($conn->connect_error) {
        die("Conexiunea a eșuat: " . $conn->connect_error);
    }

    $sql = "INSERT INTO feedback (user_id, event_name, event_type, event_date, event_location, event_organizer, event_details, event_emotion) VALUES ('$user_id', '$evenimentNume', '$evenimentTip', '$evenimentData', '$evenimentLocatie', '$evenimentOrganizator', '$evenimentDetalii', '$evenimentEmotie')";

    if ($conn->query($sql) === TRUE) {
        echo "Feedback-ul a fost trimis cu succes!";
    } else {
        echo "Eroare: " . $conn->error;
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Procesare Feedback</title>
</head>
<body>
<a href="eveniment.html">Înapoi la formular</a>
</body>
</html>
