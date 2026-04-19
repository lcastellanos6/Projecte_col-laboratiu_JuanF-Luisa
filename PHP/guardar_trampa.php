<?php
require_once __DIR__ . '/db.php';
$conn = db_connect();

$id_sector = $_POST['id_sector'];
$tipus = $_POST['tipus'];
$model = !empty($_POST['model']) ? $_POST['model'] : null;
$observacions = !empty($_POST['observacions']) ? $_POST['observacions'] : null;

$stmt = $conn->prepare("INSERT INTO trampa (id_sector, tipus, model, observacions) VALUES (?, ?, ?, ?)");
$stmt->bind_param("isss", $id_sector, $tipus, $model, $observacions);

if ($stmt->execute()) {
    echo "<h3>Trampa registrada correctament!</h3>";
    echo "<a href='../HTML/trampa.php'>Tornar</a>";
} else {
    echo "Error: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
