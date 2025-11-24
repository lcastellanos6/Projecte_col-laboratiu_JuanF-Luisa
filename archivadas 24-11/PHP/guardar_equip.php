<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "prueba1";

// Conexión
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("❌ Error de connexió: " . $conn->connect_error);

// Recoger datos del formulario
$tipus = $_POST['tipus'] ?? '';
$descripcio = $_POST['descripcio'] ?? '';

// Validación básica
if (empty($tipus)) {
    echo "<p style='color:red;'>⚠️ Cal indicar el tipus de l'equip.</p>";
    exit;
}

// Preparar la inserción
$sql = "INSERT INTO Equip (tipus, descripcio) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
if (!$stmt) die("❌ Error en preparar la consulta: " . $conn->error);

$stmt->bind_param("ss", $tipus, $descripcio);

// Ejecutar
if ($stmt->execute()) {
    echo "<p style='color:green;'>✅ Equip guardat correctament!</p>";
    echo "<p>ID assignat: <b>" . $conn->insert_id . "</b></p>";
} else {
    echo "<p style='color:red;'>❌ Error en guardar l'equip: " . $stmt->error . "</p>";
}

$stmt->close();
$conn->close();
?>