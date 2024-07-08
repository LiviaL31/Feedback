<?php
session_start();

// Configurația bazei de date
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "feedback";

// Conectare la baza de date
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifică conexiunea
if ($conn->connect_error) {
    die("Conexiunea a eșuat: " . $conn->connect_error);
}

// Verificăm dacă utilizatorul este autentificat
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Preluăm feedback-urile utilizatorului autentificat din diverse tabele
$userId = $_SESSION['user_id'];

// Construim interogarea pentru a uni toate tabelele relevante
$query = "
    SELECT 'eveniment' AS tip, id, nume_eveniment AS nume, tip_eveniment AS tip_detaliu, data_eveniment AS data, locatie_eveniment AS locatie, organizator_eveniment AS organizator, detalii_eveniment AS detalii, emotie, data_feedback
    FROM evenimente
    WHERE id_utilizator = $userId

    UNION ALL

    SELECT 'persoana', id, nume_persoana, ocupatie, NULL, NULL, NULL, detalii_persoana, emotie, data_feedback
    FROM persoane
    WHERE id_utilizator = $userId

    UNION ALL

    SELECT 'locatie', id, nume_locatie, tip_locatie, NULL, NULL, NULL, descriere_locatie, emotie, data_feedback
    FROM locatii
    WHERE id_utilizator = $userId

    UNION ALL

    SELECT 'produs', id, nume_produs, tip_produs, NULL, NULL, NULL, descriere_produs, emotie, data_feedback
    FROM produse
    WHERE id_utilizator = $userId

    UNION ALL

    SELECT 'serviciu', id, nume_serviciu, tip_serviciu, NULL, NULL, NULL, detalii_serviciu, emotie, data_feedback
    FROM servicii
    WHERE id_utilizator = $userId

    UNION ALL

    SELECT 'artefact', id, nume_artefact, tip_artefact, NULL, nume_locatie, NULL, descriere_artefact, emotie, data_feedback
    FROM artefacte_artistice
    WHERE id_utilizator = $userId
";

$result = $conn->query($query);

// Verificăm dacă există feedback-uri pentru utilizatorul curent
if ($result->num_rows > 0) {
    $feedbacks = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $feedbacks = []; // Inițializăm array-ul cu feedback-uri goale
}

// Închidem conexiunea
$conn->close();
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Feedback-urile tale</title>
</head>
<body>
    <h1>Feedback-urile tale</h1>
    <ul>
        <?php if (!empty($feedbacks)): ?>
            <?php foreach ($feedbacks as $feedback): ?>
                <li>
                    <strong>Tip:</strong> <?php echo htmlspecialchars($feedback['tip']); ?><br>
                    <strong>Nume:</strong> <?php echo htmlspecialchars($feedback['nume']); ?><br>
                    <strong>Tip Detaliu:</strong> <?php echo htmlspecialchars($feedback['tip_detaliu']); ?><br>
                    <strong>Data:</strong> <?php echo htmlspecialchars($feedback['data']); ?><br>
                    <strong>Locatie:</strong> <?php echo htmlspecialchars($feedback['locatie']); ?><br>
                    <strong>Organizator:</strong> <?php echo htmlspecialchars($feedback['organizator']); ?><br>
                    <strong>Detalii:</strong> <?php echo htmlspecialchars($feedback['detalii']); ?><br>
                    <strong>Emoție:</strong> <?php echo htmlspecialchars($feedback['emotie']); ?><br>
                    <strong>Data Feedback:</strong> <?php echo htmlspecialchars($feedback['data_feedback']); ?><br>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <li>Nu aveți feedback-uri create.</li>
        <?php endif; ?>
    </ul>
    <a href="creare_formular.php">Creează un nou formular</a>
</body>
</html>