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

// Verifică conexiunea
if ($conn->connect_error) {
    die("Conexiunea a eșuat: " . $conn->connect_error);
}

// Verifică dacă formularul este trimis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);

    // Verifică dacă emailul există în baza de date conturi_utilizator
    $stmt = $conn->prepare("SELECT id FROM conturi_utilizator WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Actualizează parola în baza de date
        $stmt = $conn->prepare("UPDATE conturi_utilizator SET parola = ? WHERE email = ?");
        $stmt->bind_param("ss", $new_password, $email);

        if ($stmt->execute()) {
           // $success_message = "Parola a fost resetată cu succes.";
            header("Location: login.html");
            exit();
        } else {
            $error_message = "Eroare la resetarea parolei.";
        }
    } else {
        $error_message = "Adresa de email nu a fost găsită.";
    }
}

// Închiderea conexiunii cu baza de date
$conn->close();

// Afișarea mesajelor de succes sau de eroare în fișierul HTML
if (isset($error_message)) {
    include('resetare_parola.html');
} elseif (isset($success_message)) {
 //   echo "<h2>$success_message</h2>";
   // echo "<p>Veți fi redirecționat către pagina de login în curând.</p>";
   // header("refresh:3;url=login.html"); // Redirecționare către pagina de login după 3 secunde
} else {
    include('resetare_parola.html'); // Încărcare pagina cu formularul pentru resetare parolă
}
?>
