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
$qualitat = $_POST['qualitat'];
$estat = $_POST['estat'];
$id_client = $_POST['id_client'];
$qr_url = $_POST['qr_url'];

$sql = "INSERT INTO lot (collita_id, codi_lot, data_creacio, quantitat, unitat, qualitat, estat, id_client, qr_url)
        VALUES ('$collita_id', '$codi_lot', '$data_creacio', '$quantitat', '$unitat', '$qualitat', '$estat', '$id_client', '$qr_url')";

if ($conn->query($sql) === TRUE) {
    echo "Lot registrat correctament!";
} else {
    echo "Error en guardar: " . $conn->error;
}

$conn->close();
?>
