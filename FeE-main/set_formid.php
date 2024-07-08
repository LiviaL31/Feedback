<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//header('Content-Type: application/json');
if (isset($_GET['id'])) {
    $formId = $_GET['id'];
    $_SESSION['formId'] = $_GET['id'];
    //echo json_encode(['success' => true, 'formId' => $formId]);
    header('Location: completare_formular.html?id='. $_GET['id']);
   exit();
} else {
    echo 'ID-ul formularului lipseste.';
    exit();
}
?>
