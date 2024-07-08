<?php
session_start(); // Începe sesiunea
//session_unset(); // Elimină toate variabilele de sesiune
//session_destroy(); // Distruge sesiunea

// Șterge cookie-urile
//setcookie('username', '', time() - 3600, "/");
//setcookie('password', '', time() - 3600, "/");
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {

header("Location: login.php"); // Redirecționează utilizatorul către pagina de autentificare
exit();
}
?>
