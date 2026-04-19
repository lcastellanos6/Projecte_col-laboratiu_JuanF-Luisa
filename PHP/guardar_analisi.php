<?php
require_once __DIR__ . '/db.php';
$conn = db_connect();

$id_sector = $_POST['id_sector'];
$data_analisi = $_POST['data_analisi'];
$tipus = $_POST['tipus'];
$ph = !empty($_POST['ph']) ? $_POST['ph'] : null;
$conductivitat = !empty($_POST['conductivitat']) ? $_POST['conductivitat'] : null;
$materia_organica = !empty($_POST['materia_organica']) ? $_POST['materia_organica'] : null;
$nitrogen = !empty($_POST['nitrogen']) ? $_POST['nitrogen'] : null;
$fosfor = !empty($_POST['fosfor']) ? $_POST['fosfor'] : null;
$potassi = !empty($_POST['potassi']) ? $_POST['potassi'] : null;
$document_url = !empty($_POST['document_url']) ? $_POST['document_url'] : null;
$observacions = !empty($_POST['observacions']) ? $_POST['observacions'] : null;

$stmt = $conn->prepare("INSERT INTO analisi_agronomic (id_sector, data_analisi, tipus, ph, conductivitat, materia_organica, nitrogen, fosfor, potassi, document_url, observacions) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("issddddddss", $id_sector, $data_analisi, $tipus, $ph, $conductivitat, $materia_organica, $nitrogen, $fosfor, $potassi, $document_url, $observacions);

if ($stmt->execute()) {
    echo "<h3>Anàlisi registrat correctament!</h3>";
    echo "<a href='../HTML/analisi.php'>Tornar</a>";
} else {
    echo "Error: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
