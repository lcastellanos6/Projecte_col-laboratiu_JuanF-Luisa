<?php
require_once __DIR__ . '/db.php';
$conn = db_connect();

$id_sector = $_POST['id_sector'];
$temperatura_aire = !empty($_POST['temperatura_aire']) ? $_POST['temperatura_aire'] : null;
$humitat_aire = !empty($_POST['humitat_aire']) ? $_POST['humitat_aire'] : null;
$humitat_sol = !empty($_POST['humitat_sol']) ? $_POST['humitat_sol'] : null;
$pluja = !empty($_POST['pluja']) ? $_POST['pluja'] : null;
$evaporacio = !empty($_POST['evaporacio']) ? $_POST['evaporacio'] : null;

$stmt = $conn->prepare("INSERT INTO sensor_dades (id_sector, temperatura_aire, humitat_aire, humitat_sol, pluja, evaporacio) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("iddddd", $id_sector, $temperatura_aire, $humitat_aire, $humitat_sol, $pluja, $evaporacio);

if ($stmt->execute()) {
    echo "<h3>Lectura registrada correctament!</h3>";
    echo "<a href='../HTML/sensor_dades.php'>Tornar</a>";
} else {
    echo "Error: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
