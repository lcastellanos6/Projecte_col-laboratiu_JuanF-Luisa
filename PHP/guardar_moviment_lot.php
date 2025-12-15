<?php
// Connexió BD
$servername = "localhost";
$username   = "root";
$password   = "";
$database   = "web";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Error de connexió: " . $conn->connect_error);
}

// Recollir dades del formulari
$id_lot       = isset($_POST['id_lot']) ? intval($_POST['id_lot']) : null;
$data         = !empty($_POST['data']) ? $_POST['data'] : date("Y-m-d H:i:s");
$quantitat    = isset($_POST['quantitat']) ? floatval($_POST['quantitat']) : 0.0;
$motiu        = isset($_POST['motiu']) ? $_POST['motiu'] : '';
$id_aplicacio = !empty($_POST['id_aplicacio']) ? intval($_POST['id_aplicacio']) : null;
$observacions = !empty($_POST['observacions']) ? $_POST['observacions'] : null;

// Preparar consulta (taula correcte: moviment_estoc)
$stmt = $conn->prepare("
    INSERT INTO moviment_estoc (id_lot, data, quantitat, motiu, id_aplicacio, observacions)
    VALUES (?, ?, ?, ?, ?, ?)
");

if (!$stmt) {
    die("Error en preparar la consulta: " . $conn->error);
}

// Tipus: id_lot (i), data (s), quantitat (d), motiu (s), id_aplicacio (i), observacions (s)
$stmt->bind_param(
    "isdiss",
    $id_lot,
    $data,
    $quantitat,
    $motiu,
    $id_aplicacio,
    $observacions
);

if ($stmt->execute()) {
    echo "<script>alert('Moviment registrat correctament!'); window.location.href='../HTML/moviment_lot.html';</script>";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
