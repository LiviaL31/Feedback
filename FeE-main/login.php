<?php
session_start(); // Începe sesiunea
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Conectare la baza de date
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "feedback";

// Creare conexiune
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifică conexiunea
if ($conn->connect_error) {
die("Conexiunea a eșuat: " . $conn->connect_error);
}
$error_message='';
// Verifică dacă formularul este trimis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
$user = $_POST['username'];
$pass = $_POST['password'];
//$remember_me = isset($_POST['remember_me']);


// Previne SQL Injection
$user = $conn->real_escape_string($user);
//$pass = $conn->real_escape_string($pass);

// Interogare SQL
$sql = "SELECT id,parola FROM conturi_utilizator WHERE email='$user'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    // Verifică dacă utilizatorul este deja autentificat
//if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
  //header("Location: meniu.html");
   // exit();
//}
$row = $result->fetch_assoc();
if (password_verify($pass, $row['parola'])) {
    // Dacă există un utilizator cu aceste detalii
    $_SESSION['loggedin'] = true; 
    $_SESSION['username'] = $user;
    $_SESSION['user_id'] = $row['id'];


    header("Location: meniu.html");
    exit();
}
}else {
    $error_message= "Numele de utilizator sau parola este incorect!";
}
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback on Everything - Autentificare</title>
    <link rel="stylesheet" href="style-login.css">
</head>
<body>
    <div class="container">
        <h1>Feedback on Everything</h1>
        <?php if (!empty($error_message)): ?>
    <div class="error-message"><?php echo $error_message; ?></div>
<?php endif; ?>

        <form action="login.php" method="post">
            <input type="text" name="username" placeholder="Email" autocomplete="off" required>
            <input type="password" name="password" placeholder="Parolă" autocomplete="off" required>
            <input type="submit" value="Autentificare">
        </form>
        <div class="register-link">
            Nu aveți un cont? <a href="register.html">Înregistrați-vă aici</a>
            <br><br>
            V-ați uitat parola?
            <a href="resetare_parola.html" class="button">Resetare Parolă</a>
        </div>
    </div>
</body>
</html>