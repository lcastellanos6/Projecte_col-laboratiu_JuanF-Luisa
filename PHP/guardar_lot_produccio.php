<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "web";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Error de connexió: " . $conn->connect_error);
}

$collita_id = $_POST['collita_id'];
$codi_lot = $_POST['codi_lot'];
$data_creacio = $_POST['data_creacio'];
$quantitat = $_POST['quantitat'];
$unitat = $_POST['unitat'];
$qualitat = !empty($_POST['qualitat']) ? $_POST['qualitat'] : NULL;
$estat = $_POST['estat'];
$id_client = !empty($_POST['id_client']) ? $_POST['id_client'] : NULL;
$qr_url = !empty($_POST['qr_url']) ? $_POST['qr_url'] : NULL;

$stmt = $conn->prepare("
    INSERT INTO lot_produccio (collita_id, codi_lot, data_creacio, quantitat, unitat, qualitat, estat, id_client, qr_url)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
");
$stmt->bind_param(
    "issdsssis",
    $collita_id,
    $codi_lot,
    $data_creacio,
    $quantitat,
    $unitat,
    $qualitat,
    $estat,
    $id_client,
    $qr_url
);

if ($stmt->execute()) {
    echo "Lot registrat correctament!";
} else {
    echo "Error en guardar: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
