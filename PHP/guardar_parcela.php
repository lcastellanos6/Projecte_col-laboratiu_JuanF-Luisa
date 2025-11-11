<?php
// Dades de connexió
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "prueba1"; 

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("❌ Error de connexió: " . $conn->connect_error);

// Recollir valors del formulari (amb validació bàsica)
$ref_cadastral = $_POST['ref_cadastral'] ?? '';
$nom = $_POST['nom'] ?? '';
$superficie = !empty($_POST['superficie']) ? floatval($_POST['superficie']) : null;
$descripcio = $_POST['descripcio'] ?? '';
$municipi = $_POST['municipi'] ?? '';
$geometria_kml = $_POST['geometria_kml'] ?? ''; // En aquest formulari només hi ha geometria_kml
$foto_url = $_POST['foto_url'] ?? '';
$edafo = $_POST['edafo'] ?? '';
$documentacio = $_POST['documentacio'] ?? '';
$pendent = !empty($_POST['pendent']) ? floatval($_POST['pendent']) : null;
$orientacio = $_POST['orientacio'] ?? '';
$tipus_sol = $_POST['tipus_sol'] ?? '';

// ⚠️ Validació: la geometria (KML) és obligatòria
if (empty($geometria_kml)) {
    echo "<p style='color:red; font-weight:bold;'>⚠️ No s'ha indicat cap geometria (KML). Si us plau, introdueix les dades abans de guardar.</p>";
    echo "<p><a href='formulari_parcela.html'>Tornar al formulari</a></p>";
    exit;
}

// ✅ Com que no tens mapa, deixem 'geometria' com un placeholder GEOJSON bàsic (punt)
$geometria_fake = '{"type":"Point","coordinates":[0,0]}';

// Consulta preparada
$sql = "INSERT INTO Parcela (
    ref_cadastral, nom, superficie, descripcio, municipi,
    geometria, geometria_kml, foto_url, edafo, documentacio,
    pendent, orientacio, tipus_sol
) VALUES (?, ?, ?, ?, ?, ST_GeomFromGeoJSON(?), ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("❌ Error en preparar la consulta: " . $conn->error);
}

// Vincular paràmetres
$stmt->bind_param(
    "ssdsssssssdss",
    $ref_cadastral, $nom, $superficie, $descripcio, $municipi,
    $geometria_fake, $geometria_kml, $foto_url, $edafo, $documentacio,
    $pendent, $orientacio, $tipus_sol
);

// Executar i comprovar resultat
if ($stmt->execute()) {
    echo "<p style='color:green; font-weight:bold;'>✅ Parcel·la guardada correctament!</p>";
    echo "<p>ID assignat: <b>" . $conn->insert_id . "</b></p>";
} else {
    echo "<p style='color:red;'>❌ Error en guardar la parcel·la: " . $stmt->error . "</p>";
}

$stmt->close();
$conn->close();
?>