<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "web";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Error de connexió: " . $conn->connect_error);

// VARIABLES FORM
$ref_cadastral = $_POST['ref_cadastral'];
$nom = $_POST['nom'] ?? null;
$superficie = $_POST['superficie'] ?? null;
$descripcio = $_POST['descripcio'] ?? null;
$municipi = $_POST['municipi'] ?? null;

$geometria = $_POST['geometria'];          // GeoJSON
$geometria_kml = $_POST['geometria_kml'];  // text GeoJSON

$edafo = $_POST['edafo'] ?? null;
$documentacio = $_POST['documentacio'] ?? null;
$pendent = $_POST['pendent'] ?? null;
$orientacio = $_POST['orientacio'] ?? null;
$tipus_sol = $_POST['tipus_sol'] ?? null;

// -----------------------------------------
// PUJAR FOTO
// -----------------------------------------
$foto_url = null;

if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {

    $uploadDir = "uploads/";

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = time() . "_" . basename($_FILES['foto']['name']);
    $filePath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['foto']['tmp_name'], $filePath)) {
        $foto_url = $filePath;
    }
}

// -----------------------------------------
// VALIDAR GEOMETRIA
// -----------------------------------------
if (empty($geometria)) {
    die("❌ Error: No hi ha geometria. Torna enrere i dibuixa la parcel·la.");
}

// -----------------------------------------
// INSERT FINAL
// -----------------------------------------
$sql = "INSERT INTO Parcela (
    ref_cadastral, nom, superficie, descripcio, municipi,
    geometria, geometria_kml, foto_url, edafo, documentacio,
    pendent, orientacio, tipus_sol
) VALUES (?, ?, ?, ?, ?, ST_GeomFromGeoJSON(?), ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "ssdsssssssdss",
    $ref_cadastral,
    $nom,
    $superficie,
    $descripcio,
    $municipi,
    $geometria,
    $geometria_kml,
    $foto_url,
    $edafo,
    $documentacio,
    $pendent,
    $orientacio,
    $tipus_sol
);

if ($stmt->execute()) {
    echo "<h2 style='color:green'>✔ Parcel·la guardada correctament</h2>";
    echo "<p><b>ID:</b> " . $conn->insert_id . "</p>";
    echo "<p><a href='formulari_parcela.html'>➕ Afegir una altra parcel·la</a></p>";
} else {
    echo "<p style='color:red'>Error: " . $stmt->error . "</p>";
}

$stmt->close();
$conn->close();

?>



