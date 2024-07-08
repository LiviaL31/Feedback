<?php
function generateHTMLReport($data) {
    $html = '<table border="1"><tr><th>Categorie</th><th>Număr de feedback-uri</th></tr>';
    foreach ($data as $category => $count) {
        $html .= "<tr><td>$category</td><td>$count</td></tr>";
    }
    $html .= '</table>';
    return $html;
}

function generateCSVReport($data) {
    $csv = "Categorie,Număr de feedback-uri\n";
    foreach ($data as $category => $count) {
        $csv .= "$category,$count\n";
    }
    return $csv;
}

function generateJSONReport($data) {
    return json_encode($data);
}
?>
