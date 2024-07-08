<?php
session_start();
header('Content-Type: application/json'); // Setează header-ul pentru a returna JSON

$response = [];

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['user_id'])) {
    $response['success'] = false;
    $response['message'] = 'Utilizatorul nu este autentificat!';
    echo json_encode($response);
    exit();
}

$conn = new mysqli("localhost", "root", "", "feedback");

if ($conn->connect_error) {
    $response['success'] = false;
    $response['message'] = "Conexiunea la bază de date a eșuat: " . $conn->connect_error;
    echo json_encode($response);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];
        $evenimentNume = htmlspecialchars($_POST['evenimentNume']);
        $evenimentTip = htmlspecialchars($_POST['tipFormular']);
        $evenimentData = $_POST['evenimentData'];
        $evenimentLocatie = htmlspecialchars($_POST['evenimentLocatie']);
        $evenimentOrganizator = htmlspecialchars($_POST['evenimentOrganizator']);
        $evenimentDetalii = htmlspecialchars($_POST['evenimentDetalii']);
        $formularStart = $_POST['formularStart'];
        $formularEnd = $_POST['formularEnd'];
        $creat = date("Y-m-d H:i:s");

        if (strtotime($formularStart) < strtotime(date("Y-m-d")) || strtotime($formularEnd) < strtotime($formularStart)) {
            $response['success'] = false;
            $response['message'] = 'Datele de început și sfârșit nu sunt valide.';
            echo json_encode($response);
            exit();
        }

        $stmt = $conn->prepare("INSERT INTO formulare (user_id, nume, tip, data, Locatie, Organizator, detalii, creat, start, end) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('isssssssss', $userId, $evenimentNume, $evenimentTip, $evenimentData, $evenimentLocatie, $evenimentOrganizator, $evenimentDetalii, $creat, $formularStart, $formularEnd);

        if ($stmt->execute()) {
            $response['success'] = true;
        } else {
            $response['success'] = false;
            $response['message'] = "Eroare: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $response['success'] = false;
        $response['message'] = 'Utilizatorul nu este autentificat!';
    }

    $conn->close();
    echo json_encode($response);
}
?>
