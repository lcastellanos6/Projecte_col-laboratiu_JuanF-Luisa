<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "prueba1";

// Conexión
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("❌ Error de connexió: " . $conn->connect_error);

// Recoger datos del formulario
$id_parcela = $_POST['id_parcela'] ?? '';
$tipus = $_POST['tipus'] ?? '';
$descripcio = $_POST['descripcio'] ?? '';
$geometria_kml = $_POST['geometria_kml'] ?? '';
$foto_url = $_POST['foto_url'] ?? '';

// Valor dummy para la columna GEOMETRY
$geometria = "POINT(0 0)";

// Validación básica
if (empty($id_parcela) || empty($tipus)) {
    echo "<p style='color:red;'>⚠️ Cal indicar el ID de la parcela i el tipus d'infraestructura.</p>";
    echo "<p><a href='index.php'>Tornar al formulari</a></p>";
    exit;
}

// Preparar la inserción
$sql = "INSERT INTO Infraestructura (id_parcela, tipus, descripcio, geometria, geometria_kml, foto_url) 
        VALUES (?, ?, ?, ST_GeomFromText(?), ?, ?)";
$stmt = $conn->prepare($sql);
if (!$stmt) die("❌ Error en preparar la consulta: " . $conn->error);

// Bind de parámetros
$stmt->bind_param("isssss", $id_parcela, $tipus, $descripcio, $geometria, $geometria_kml, $foto_url);

// Ejecutar
if ($stmt->execute()) {
    echo "<p style='color:green;'>✅ Infraestructura guardada correctament!</p>";
    echo "<p>ID assignat: <b>" . $conn->insert_id . "</b></p>";
} else {
    echo "<p style='color:red;'>❌ Error en guardar la infraestructura: " . $stmt->error . "</p>";
}

// Cerrar
$stmt->close();
$conn->close();
?>
