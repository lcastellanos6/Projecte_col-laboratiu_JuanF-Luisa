$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("❌ Error de connexió: " . $conn->connect_error);

// Recollir dades del formulari
$nom = $_POST['nom'] ?? '';
$superficie = !empty($_POST['superficie']) ? floatval($_POST['superficie']) : null;
$geometria_kml = $_POST['geometria_kml'] ?? '';
$foto_url = $_POST['foto_url'] ?? '';
$estat_productiu = $_POST['estat_productiu'] ?? 'Plantat';

// Validació mínima
if (empty(trim($geometria_kml))) {
    echo "<p style='color:red; font-weight:bold;'>⚠️ Cal indicar el KML del sector.</p>";
    echo "<p><a href='formulari_sector.html'>Tornar al formulari</a></p>";
    exit;
}

// Inserció (ja no fem servir ST_GeomFromGeoJSON)
$sql = "INSERT INTO Sector (
    nom, superficie, geometria_kml, foto_url, estat_productiu
) VALUES (?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
if (!$stmt) die("❌ Error en preparar la consulta: " . $conn->error);

$stmt->bind_param("sdsss", $nom, $superficie, $geometria_kml, $foto_url, $estat_productiu);

if ($stmt->execute()) {
    echo "<p style='color:green; font-weight:bold;'>✅ Sector guardat correctament!</p>";
    echo "<p>ID assignat: <b>" . $conn->insert_id . "</b></p>";
} else {
    echo "<p style='color:red;'>❌ Error en guardar el sector: " . $stmt->error . "</p>";
}

$stmt->close();
$conn->close();
?>
