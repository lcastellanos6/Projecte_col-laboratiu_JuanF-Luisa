<?php

$conn = new mysqli("localhost","root","","web");

if ($conn->connect_error) {
    die("Error BD");
}

$conn->set_charset("utf8");

$id_fila = $_POST['id_fila'];
$data = $_POST['data'];
$tipus = $_POST['tipus'];
$producte = $_POST['producte'];
$dosi_ml = $_POST['dosi_ml'];

$sql = "INSERT INTO tractament (id_fila, data, tipus, producte, dosi_ml)
VALUES (?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

$stmt->bind_param("issss", $id_fila, $data, $tipus, $producte, $dosi_ml);

if ($stmt->execute()) {
    echo "✅ Tractament guardat correctament";
} else {
    echo "❌ Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
