<?php
session_start();

$host = 'localhost';
$username = 'root';
$password = 'root';
$database = 'feedback';


$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Conexiunea la bază de date a eșuat: " . $conn->connect_error);
}

// numărul total de feedback-uri pentru fiecare categorie în funcție de emotie
function count_feedbacks_by_emotion($conn, $emotion) {
    $counts = [];

    // Lista de tabele pentru care se va calcula numărul de feedback-uri
    $tables = ['artefacte_artistice', 'evenimente', 'locatii', 'persoane', 'produse', 'servicii'];

    foreach ($tables as $table) {
        $sql = "SELECT COUNT(*) as total FROM $table WHERE emotie = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $emotion);
        $stmt->execute();
        $result = $stmt->get_result();
        $counts[$table] = $result->fetch_assoc()['total'];
    }

    return $counts;
}

// Preluare feedback din baza de date în funcție de emotie (dacă este setată)
$feedbacks = [];
$counts = [];
if (isset($_GET['emotion']) && !empty($_GET['emotion'])) {
    $emotion = $_GET['emotion'];
    $counts = count_feedbacks_by_emotion($conn, $emotion);
    $sql = "SELECT * FROM feedback WHERE emotie = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $emotion);
    $stmt->execute();
    $result = $stmt->get_result();
    $feedbacks = $result->fetch_all(MYSQLI_ASSOC);
} else {
    // Dacă nu este setată o emotie, afișăm toate feedback-urile și calculăm numărul total de feedback-uri pentru fiecare categorie
    $tables = ['artefacte_artistice', 'evenimente', 'locatii', 'persoane', 'produse', 'servicii'];
    foreach ($tables as $table) {
        $sql = "SELECT COUNT(*) as total FROM $table";
        $result = $conn->query($sql);
        $counts[$table] = $result->fetch_assoc()['total'];
    }
    $sql = "SELECT * FROM feedback";
    $result = $conn->query($sql);
    $feedbacks = $result->fetch_all(MYSQLI_ASSOC);
}

// Deconectare de la baza de date
$conn->close();
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapoarte Feedback</title>
    <link rel="stylesheet" href="style.css">
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var emotion = document.getElementById('emotion').value;
            var data = google.visualization.arrayToDataTable([
                ['Categorie', 'Număr de feedback-uri'],
                ['Artefacte artistice', <?php echo $counts['artefacte_artistice'] ?? 0; ?>],
                ['Evenimente', <?php echo $counts['evenimente'] ?? 0; ?>],
                ['Locații', <?php echo $counts['locatii'] ?? 0; ?>],
                ['Persoane', <?php echo $counts['persoane'] ?? 0; ?>],
                ['Produse', <?php echo $counts['produse'] ?? 0; ?>],
                ['Servicii', <?php echo $counts['servicii'] ?? 0; ?>]
            ]);

            var options = {
                title: 'Număr total de feedback-uri pentru fiecare categorie (' + emotion + ')',
                pieHole: 0.4,
            };

            var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
            chart.draw(data, options);
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Rapoarte Feedback</h1>
        <form action="rapoarte.php" method="GET">
            <label for="emotion">Selectați o emoție:</label>
            <select id="emotion" name="emotion" onchange="this.form.submit()">
            <option value="">Toate</option>
                <option value="fericire" <?php if (isset($_GET['emotion']) && $_GET['emotion'] == 'fericire') echo 'selected'; ?>>Fericire</option>
                <option value="tristete" <?php if (isset($_GET['emotion']) && $_GET['emotion'] == 'tristete') echo 'selected'; ?>>Tristețe</option>
                <option value="frica" <?php if (isset($_GET['emotion']) && $_GET['emotion'] == 'frica') echo 'selected'; ?>>Frică</option>
                <option value="furie" <?php if (isset($_GET['emotion']) && $_GET['emotion'] == 'furie') echo 'selected'; ?>>Furie</option>
                <option value="dezgust" <?php if (isset($_GET['emotion']) && $_GET['emotion'] == 'dezgust') echo 'selected'; ?>>Dezgust</option>
                <option value="surpriza" <?php if (isset($_GET['emotion']) && $_GET['emotion'] == 'surpriza') echo 'selected'; ?>>Surpriză</option>
                <option value="anticiptare" <?php if (isset($_GET['emotion']) && $_GET['emotion'] == 'anticiptare') echo 'selected'; ?>>Anticipare</option>
                <option value="acceptare" <?php if (isset($_GET['emotion']) && $_GET['emotion'] == 'acceptare') echo 'selected'; ?>>Acceptare</option>
            </select>
            <input type="submit" value="Filtrează">
        </form>
        
        <div id="chart_div" style="width: 100%; height: 400px;"></div>
        
        <table>
            <thead>
                <tr>
                    <th>Nume lucru</th>
                    <th>Tip lucru</th>
                    <th>Detalii</th>
                    <th>Emotie</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($feedbacks as $feedback): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($feedback['nume_lucru']); ?></td>
                        <td><?php echo htmlspecialchars($feedback['tip_lucru']); ?></td>
                        <td><?php echo htmlspecialchars($feedback['detalii']); ?></td>
                        <td><?php echo htmlspecialchars($feedback['emotie']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
