<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// Verifică dacă utilizatorul este autentificat
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['user_id'])) {
    http_response_code(401); // Unauthorized
    die();
}

// Conectare la baza de date (poate fi într-un fișier separat, ex. database.php)
$conn = new mysqli("localhost", "root", "", "feedback");

if ($conn->connect_error) {
    die("Conexiunea la bază de date a eșuat: " . $conn->connect_error);
}

// Preia ID-ul utilizatorului curent 
$userId = $_SESSION['user_id'];

// Pregătește interogarea SQL pentru a prelua formularele utilizatorului curent
$sqlForms = "SELECT id, tip, nume, creat, start, end FROM formulare WHERE user_id = ?";
$stmtForms = $conn->prepare($sqlForms);
if ($stmtForms === false) {
    die("Eroare în pregătirea declarației: " . $conn->error);
}
$stmtForms->bind_param("i", $userId);
$stmtForms->execute();
$resultForms = $stmtForms->get_result();

// Inițializează un array pentru a stoca formularele
$formulare = [];
while ($row = $resultForms->fetch_assoc()) {
    $formulare[] = $row;
}

// Pregătește interogarea SQL pentru a prelua formularele active ale altor utilizatori
$currentDate = date('Y-m-d');
$sqlActiveForms = "SELECT id, tip, nume, end FROM formulare WHERE user_id != ? AND end >= ? ";
$stmtActiveForms = $conn->prepare($sqlActiveForms);
if ($stmtActiveForms === false) {
    die("Eroare în pregătirea declarației: " . $conn->error);
}
$stmtActiveForms->bind_param("is", $userId, $currentDate);
$stmtActiveForms->execute();
$resultActiveForms = $stmtActiveForms->get_result();

// Inițializează un array pentru a stoca formularele active ale altor utilizatori
$activeForms = [];
while ($row = $resultActiveForms->fetch_assoc()) {
    $activeForms[] = $row;
}

// Închide declarația pregătită și conexiunea la baza de date
$stmtForms->close();
$stmtActiveForms->close();
$conn->close();

// Returnează datele sub formă de JSON
header('Content-Type: application/json');
echo json_encode([
    'userForms' => $formulare,
    'activeForms' => $activeForms]);
?>
