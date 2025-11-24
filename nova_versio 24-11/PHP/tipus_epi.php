<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "web";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) die("Error de connexió: " . $conn->connect_error);

$nom = $_POST['nom'];
$descripcio = $_POST['descripcio'] ?? null;
$stock_inicial = !empty($_POST['stock_inicial']) ? intval($_POST['stock_inicial']) : 0;
$stock_actual = !empty($_POST['stock_actual']) ? intval($_POST['stock_actual']) : 0;
$stock_minim = !empty($_POST['stock_minim']) ? intval($_POST['stock_minim']) : 0;

$sql = "INSERT INTO epi_tipus (nom, descripcio, stock_inicial, stock_actual, stock_minim) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssiii", $nom, $descripcio, $stock_inicial, $stock_actual, $stock_minim);

if ($stmt->execute()) {
    echo "<p>Tipus d'EPI registrat correctament.</p>";
    echo "<a href='epi_tipus.html'>Tornar</a>";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
