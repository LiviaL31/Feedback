<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Verifică dacă utilizatorul este autentificat
if (!isset($_SESSION['loggedin'])) {
    http_response_code(401); // Unauthorized
    die("Nu sunteti autentificat");
}
if (!isset($_SESSION['formId'])) {
    http_response_code(400); // Bad Request
    die("ID-ul formularului nu este setat.");
}

$formId = $_SESSION['formId'];


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli("localhost", "root", "", "feedback");
    // Conectare la baza de date

   if ($conn->connect_error) {
    die("Conexiunea la bază de date a eșuat: " . $conn->connect_error);
   }


    // Valori preluate din formular
    /*$formId = $_POST['formId'];
    $titlu = $_POST['titlu'];
    $tip = $_POST['tip'];
    $date = $_POST['date'];
    $Locatie = $_POST['Locatie'];
    $Organizator = $_POST['Organizator'];
    $Detalii = $_POST['Detalii'];
    $intrebari = $_POST['intrebari'];
    $tipEmotie = $_POST['tipEmotie']; 
    $userId = $_SESSION['user_id'];
    $createdAt = date('Y-m-d H:i:s');*/

//$formId = $_POST['formId'];
    $userId = $_SESSION['user_id'];
    $createdAt = date('Y-m-d H:i:s');

    $titlu = $conn->real_escape_string($_POST['titlu']);
    $tip = $conn->real_escape_string($_POST['tip']);
    $date = $conn->real_escape_string($_POST['date']);
    $locatie = $conn->real_escape_string($_POST['Locatie']);
    $organizator = $conn->real_escape_string($_POST['Organizator']);
    $detalii = $conn->real_escape_string($_POST['Detalii']);
    $intrebari = $conn->real_escape_string(implode("\n", $_POST['intrebari']));
    $tipEmotie = $conn->real_escape_string($_POST['tipEmotie']);

    // Pregătește declarația SQL pentru inserare
    $sql = "INSERT INTO raspunsuri ( formular_id, user_id, created_at, descriere, Emotie)
               VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Eroare în pregătirea declarației: " . $conn->error);
    }

    // Leagă parametrii și execută declarația
    $stmt->bind_param("iisss", $formId, $userId, $createdAt, json_encode($intrebari), $tipEmotie);

    if ($stmt->execute() === false) {
        die("Eroare la executarea declarației: " . $stmt->error);
    }else{
        header("Location:meniu.html");
        exit();
    }

   // echo "Formular completat cu succes!";
    $stmt->close();
    $conn->close();
}
?>
