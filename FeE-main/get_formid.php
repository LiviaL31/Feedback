<?php
session_start();
header('Content-Type: application/json');
if (isset($_SESSION['formId'])) {
    echo json_encode(['formId' => $_SESSION['formId']]);
} else {
    echo json_encode(['error' => 'ID-ul formularului nu este setat în sesiune.']);
}
?>
