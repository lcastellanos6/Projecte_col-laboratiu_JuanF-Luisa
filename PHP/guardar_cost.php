<?php
require_once __DIR__ . '/db.php';
$conn = db_connect();

$id_sector = !empty($_POST['id_sector']) ? $_POST['id_sector'] : null;
$data_cost = $_POST['data_cost'];
$concepte = $_POST['concepte'];
$import = $_POST['import'];
$categoria = $_POST['categoria'];
$observacions = !empty($_POST['observacions']) ? $_POST['observacions'] : null;

$stmt = $conn->prepare("INSERT INTO cost_explotacio (id_sector, data_cost, concepte, import, categoria, observacions) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("isddss", $id_sector, $data_cost, $concepte, $import, $categoria, $observacions);

if ($stmt->execute()) {
    echo "<h3>Cost registrat correctament!</h3>";
    echo "<a href='../HTML/cost_explotacio.php'>Tornar</a>";
} else {
    echo "Error: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
