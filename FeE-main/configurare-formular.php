<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401); // Unauthorized
    //die("Nu sunteți autentificat.");
   die( json_encode(['error' => "Nu sunteți autentificat."]));
    //exit;
}

if (!isset($_GET['id'])) {
    http_response_code(400); // Bad Request
    die(json_encode(['error' => "ID-ul formularului nu a fost specificat."]));
}

// Conectare la baza de date
$conn = new mysqli("localhost", "root", "", "feedback");

if ($conn->connect_error) {
   die(json_encode(['error' => "Conexiunea la bază de date a eșuat: " . $conn->connect_error]));
  // echo json_encode(['error' => "Conexiunea la bază de date a eșuat: " . $conn->connect_error]);
    //exit;

}

// Preia ID-ul formularului din parametrii URL
$formId = $_GET['id'];

// Pregătește interogarea SQL pentru a prelua detaliile formularului
$sql = "SELECT id, nume, tip, data, Locatie, Organizator, detalii, end FROM formulare WHERE id = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die(json_encode(['error' => "Eroare în pregătirea declarației SQL: " . $conn->error]));
    //exit;
}

$stmt->bind_param("i", $formId);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
    // Returnează datele sub formă de JSON
    //if($data['user_id'] == $_SESSION['user_id']){
  //  header('Content-Type: application/json');
    echo json_encode($data);
} else {
    http_response_code(404); // Not Found
    die(json_encode(['error' => "Formularul nu a fost găsit."]));
}

$stmt->close();
$conn->close();
?>
