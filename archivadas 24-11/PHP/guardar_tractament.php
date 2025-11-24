<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "prueba1";

// Conexión
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("❌ Error de connexió: " . $conn->connect_error);

// Recibir datos del formulario
$id_fila = $_POST['id_fila'] ?? '';
$data = $_POST['data'] ?? '';
$tipus = $_POST['tipus'] ?? null;
$producte = $_POST['producte'] ?? null;
$dosi_ml = $_POST['dosi_ml'] ?? null;

// Validación
if (empty($id_fila) || empty($data)) {
    echo "<p style='color:red;'>⚠️ Cal indicar la fila i la data del tractament.</p>";
    exit;
}

// Inserción
$sql = "INSERT INTO tractament (id_fila, data, tipus, producte, dosi_ml) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
if (!$stmt) die("❌ Error en preparar la consulta: " . $conn->error);

$stmt->bind_param("issss", $id_fila, $data, $tipus, $producte, $dosi_ml);

// Ejecutar
if ($stmt->execute()) {
    echo "<p style='color:green;'>✅ Tractament guardat correctament!</p>";
    echo "<p>ID assignat: <b>" . $conn->insert_id . "</b></p>";
} else {
    echo "<p style='color:red;'>❌ Error en guardar el tractament: " . $stmt->error . "</p>";
}

$stmt->close();
$conn->close();
?>