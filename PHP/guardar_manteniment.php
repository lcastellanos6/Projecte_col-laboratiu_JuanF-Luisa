<?php
require_once __DIR__ . '/db.php';
$conn = db_connect();

$id_equip = $_POST['id_equip'];
$data_manteniment = $_POST['data_manteniment'];
$tipus = $_POST['tipus'];
$hores_funcionament = !empty($_POST['hores_funcionament']) ? $_POST['hores_funcionament'] : null;
$cost = !empty($_POST['cost']) ? $_POST['cost'] : null;
$descripcio = $_POST['descripcio'];
$proxim_manteniment = !empty($_POST['proxim_manteniment']) ? $_POST['proxim_manteniment'] : null;

$stmt = $conn->prepare("INSERT INTO manteniment_maquinaria (id_equip, data_manteniment, tipus, hores_funcionament, descripcio, cost, proxim_manteniment) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("issdsds", $id_equip, $data_manteniment, $tipus, $hores_funcionament, $descripcio, $cost, $proxim_manteniment);

if ($stmt->execute()) {
    echo "<h3>Manteniment registrat correctament!</h3>";
    echo "<a href='../HTML/manteniment_maquinaria.php'>Tornar</a>";
} else {
    echo "Error: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
