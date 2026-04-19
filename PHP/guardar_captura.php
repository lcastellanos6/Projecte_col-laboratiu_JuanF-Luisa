<?php
require_once __DIR__ . '/db.php';
$conn = db_connect();

$id_trampa = $_POST['id_trampa'];
$data_registre = $_POST['data_registre'];
$plaga_objectiu = $_POST['plaga_objectiu'];
$quantitat_capturada = $_POST['quantitat_capturada'];
$observacions = !empty($_POST['observacions']) ? $_POST['observacions'] : null;

$stmt = $conn->prepare("INSERT INTO monitoratge_plaga (id_trampa, data_registre, plaga_objectiu, quantitat_capturada, observacions) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("issis", $id_trampa, $data_registre, $plaga_objectiu, $quantitat_capturada, $observacions);

if ($stmt->execute()) {
    echo "<h3>Captura registrada correctament!</h3>";
    echo "<a href='../HTML/monitoratge_captures.php'>Tornar</a>";
} else {
    echo "Error: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
