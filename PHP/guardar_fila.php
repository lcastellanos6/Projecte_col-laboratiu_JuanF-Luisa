<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "prueba1";

// Conexión
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("❌ Error de connexió: " . $conn->connect_error);

// Recoger datos del formulario
$id_increment = $_POST['id_increment'] ?? '';
$numero_fila = $_POST['numero_fila'] ?? '';

// Valor dummy para la columna GEOMETRY
$geometria_fila = "POINT(0 0)";

// Validación básica
if (empty($id_increment) || empty($numero_fila)) {
    echo "<p style='color:red;'>⚠️ Cal indicar el ID de la plantació i el número de fila.</p>";
    exit;
}

// Preparar la inserción
$sql = "INSERT INTO Fila (id_increment, numero_fila, geometria_fila) 
        VALUES (?, ?, ST_GeomFromText(?))";

$stmt = $conn->prepare($sql);
if (!$stmt) die("❌ Error en preparar la consulta: " . $conn->error);

// Bind de parámetros
$stmt->bind_param("iis", $id_increment, $numero_fila, $geometria_fila);

// Ejecutar
if ($stmt->execute()) {
    echo "<p style='color:green;'>✅ Fila guardada correctament!</p>";
    echo "<p>ID assignat: <b>" . $conn->insert_id . "</b></p>";
} else {
    echo "<p style='color:red;'>❌ Error en guardar la fila: " . $stmt->error . "</p>";
}

$stmt->close();
$conn->close();
?>
