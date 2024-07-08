<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Conectare la baza de date
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "feedback";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificăm conexiunea
if ($conn->connect_error) {
    die("Conexiunea a eșuat: " . $conn->connect_error);
}

// Verificăm dacă utilizatorul este autentificat
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Verificăm dacă a fost trimisă cererea POST și există un ID de formular
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['detaliiFeedbackID'])) {
    $feedback_id = $_POST['detaliiFeedbackID'];

    // Pregătim interogarea pentru a verifica dacă utilizatorul este creatorul formularului
    $stmt = $conn->prepare("SELECT user_id FROM formulare WHERE id = ?");
    $stmt->bind_param("i", $feedback_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($creator_id);
        $stmt->fetch();

        // Verificăm dacă utilizatorul curent este creatorul formularului
        if ($_SESSION['user_id'] == $creator_id) {
            // Numărăm răspunsurile asociate formularului
            $stmt = $conn->prepare("SELECT COUNT(*) FROM raspunsuri WHERE formular_id = ?");
            $stmt->bind_param("i", $feedback_id);
            $stmt->execute();
            $stmt->bind_result($numar_raspunsuri);
            $stmt->fetch();
            $stmt->close();

            // Salvăm numărul de răspunsuri într-o variabilă pentru a fi afișat în detalii.html
            $mesaj_raspunsuri = "Acest formular a primit " . $numar_raspunsuri . " răspunsuri.";
        } else {
            // Redirecționăm către pagina de completare a formularului pentru utilizatorii care nu sunt creatori
            header("Location: completare_formular.html?id=" . $feedback_id);
            exit();
        }
    } else {
        // Formularul cu ID-ul specificat nu există
        $mesaj_raspunsuri = "Formularul cu ID-ul " . $feedback_id . " nu există.";
    }
} else {
    // Nu s-a putut procesa formularul
    $mesaj_raspunsuri = "Nu s-a putut procesa formularul.";
}

// Închidem conexiunea la baza de date
$conn->close();
//echo($mesaj_raspunsuri);
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalii Feedback</title>
    <link rel="stylesheet" href="style.css"> <!-- Adăugați aici calea către fișierul CSS -->
</head>
<body>

<header>
    <h1>Detalii Feedback</h1>
    <br>
    <nav>
        <ul>
            <li><a href="eveniment.html" class="button">Creează formular</a></li>
            <li><a href="rapoarte_generale.html" class="button">Rapoarte Generale</a></li>
            <li><a href="meniu.html" class="button">Pagina Mea</a></li>
            <li><a href="logout.php" class="button logout-button">Delogare</a></li>
        </ul>
    </nav>
</header>

<div class="container">
    <h2>Găsește un formular după ID</h2>
    <div id="detaliiFeedbackResult">
      <?php if (isset($mesaj_raspunsuri)): ?>
          <div class="message"><?php echo $mesaj_raspunsuri; ?></div>
      <?php endif; ?>
  </div><br>
    <form action="cauta_formular.php" method="post" id="detaliiFeedbackForm">
        <label for="detaliiFeedbackID">ID Feedback:</label>
        <input type="text" id="detaliiFeedbackID" name="detaliiFeedbackID" required>
        <br>
        <button type="submit">Caută Feedback</button>
    </form>
    <br>
    
</div>

</body>
</html>

