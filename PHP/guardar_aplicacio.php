<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "web";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Error de connexió: " . $conn->connect_error);
}

$id_pla = !empty($_POST['id_pla']) ? $_POST['id_pla'] : NULL;
$data = $_POST['data'];
$hora_inici = !empty($_POST['hora_inici']) ? $_POST['hora_inici'] : NULL;
$hora_fi = !empty($_POST['hora_fi']) ? $_POST['hora_fi'] : NULL;
$metode = !empty($_POST['metode']) ? $_POST['metode'] : NULL;
$condicions = !empty($_POST['condicions_ambientals']) ? $_POST['condicions_ambientals'] : NULL;
$id_operari = !empty($_POST['id_operari']) ? $_POST['id_operari'] : NULL;
$id_equip = !empty($_POST['id_equip']) ? $_POST['id_equip'] : NULL;
$observacions = !empty($_POST['observacions']) ? $_POST['observacions'] : NULL;

$stmt = $conn->prepare("INSERT INTO aplicacio 
(id_pla, data, hora_inici, hora_fi, metode, condicions_ambientals, id_operari, id_equip, observacions)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param(
    "isssssiss",
    $id_pla,
    $data,
    $hora_inici,
    $hora_fi,
    $metode,
    $condicions,
    $id_operari,
    $id_equip,
    $observacions
);

if ($stmt->execute()) {
    echo "<h3>Aplicació registrada correctament!</h3>";
    echo "<a href='../HTML/aplicacio.html'>Tornar</a>";
} else {
    echo "Error: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
