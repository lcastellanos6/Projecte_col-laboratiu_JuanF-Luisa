<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "web";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("❌ Error de connexió: " . $conn->connect_error);

// Recollir dades del formulari
$nom = $_POST['nom'] ?? '';
$superficie = !empty($_POST['superficie']) ? floatval($_POST['superficie']) : null;
$geometria_geojson = $_POST['geometria_geojson'] ?? '';
$geometria_kml = $_POST['geometria_kml'] ?? '';
$foto_url = $_POST['foto_url'] ?? '';
$estat_productiu = $_POST['estat_productiu'] ?? 'Plantat';

// Validació mínima
if (empty(trim($geometria_geojson))) {
    echo "<p style='color:red; font-weight:bold;'>⚠️ Cal indicar el GeoJSON del sector.</p>";
    echo "<p><a href='../HTML/sector_nou.html'>Tornar al formulari</a></p>";
    exit;
}
if (empty(trim($geometria_kml))) {
    echo "<p style='color:red; font-weight:bold;'>⚠️ Cal indicar el KML del sector.</p>";
    echo "<p><a href='../HTML/sector_nou.html'>Tornar al formulari</a></p>";
    exit;
}

// Inserció amb geometria GeoJSON
$sql = "INSERT INTO sector (
    nom, superficie, geometria, geometria_kml, foto_url, estat_productiu
) VALUES (?, ?, ST_GeomFromGeoJSON(?), ?, ?, ?)";

$stmt = $conn->prepare($sql);
if (!$stmt) die("❌ Error en preparar la consulta: " . $conn->error);

$stmt->bind_param("sdssss", $nom, $superficie, $geometria_geojson, $geometria_kml, $foto_url, $estat_productiu);

if ($stmt->execute()) {
    header("Location: consulta_parcela_sector.php");
    exit;
} else {
    echo "<p style='color:red;'>❌ Error en guardar el sector: " . $stmt->error . "</p>";
}

$stmt->close();
$conn->close();
?>





