<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "feedback";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verifică conexiunea
if ($conn->connect_error) {
    die("Conexiunea a eșuat: " . $conn->connect_error);
}

$message = "";
$message_type = "";

// Continuă cu restul codului pentru a crea un cont
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nume_utilizator = $_POST['username'];
    $parola = $_POST['password'];
    $email = $_POST['email'];

    // Previne SQL Injection
    $nume_utilizator = $conn->real_escape_string($nume_utilizator);
    $parola = $conn->real_escape_string($parola);
    $email = $conn->real_escape_string($email);

    //verificare daca email ul exista
    $check_email_query = "SELECT * FROM conturi_utilizator WHERE email = '$email'";
    $check_email_result = $conn->query($check_email_query);
    if ($check_email_result->num_rows > 0) {
        $message = "Email-ul introdus există deja în sistem.";
        $message_type = "error";
    }else{

    // Hash parola pentru securitate
    $hashed_password = password_hash($parola, PASSWORD_DEFAULT);

    // Inserare utilizator în baza de date
    $stmt = $conn->prepare("INSERT INTO conturi_utilizator (nume, parola, email) VALUES (?, ?, ?)");
    if ($stmt === false) {
        die("Eroare în pregătirea declarației: " . $conn->error);
    }
    $stmt->bind_param("sss", $nume_utilizator, $hashed_password, $email);

    if ($stmt->execute()) {
        $message = "Cont creat cu succes!";
        $message_type = "success";
    } else {
        $message = "Eroare la crearea contului: " . $stmt->error;
        $message_type = "error";
    }

    $stmt->close();
}
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback on Everything - Creare Cont</title>
    <link rel="stylesheet" href="style-login.css">
</head>
<body>
    <div class="container">
        <h1>Feedback on Everything</h1>
        <div id="error-message">
            <?php if (!empty($message)): ?>
                <div class="error-message <?php echo $message_type; ?>"><?php echo $message; ?></div>
            <?php endif; ?>
        </div>
        <form action="creare-cont.php" method="post">
            <input type="text" name="username" placeholder="Nume Utilizator" autocomplete="off" required>
            <input type="email" name="email" placeholder="Email" autocomplete="off" required>
            <input type="password" name="password" placeholder="Parolă" autocomplete="off" required>
            <input type="submit" value="Creează Cont">
        </form>

        <div class="register-link">
            Aveți deja un cont? <a href="login.html">Autentificați-vă aici</a>
        </div>
    </div>
</body>
</html>
