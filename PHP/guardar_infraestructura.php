<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "web";

// Connexio
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("❌ Error de connexió: " . $conn->connect_error);

// Recollir dades del formulari
$id_parcela = $_POST['id_parcela'] ?? '';
$tipus = $_POST['tipus'] ?? '';
$descripcio = $_POST['descripcio'] ?? '';
$geometria_kml = $_POST['geometria_kml'] ?? '';

// Valor dummy per a la columna GEOMETRY
$geometria = "POINT(0 0)";

// Validacio basica
if (empty($id_parcela) || empty($tipus)) {
    echo "<p style='color:red;'>⚠️ Cal indicar el ID de la parcel·la i el tipus d'infraestructura.</p>";
    echo "<p><a href='infraestructura.php'>Tornar al formulari</a></p>";
    exit;
}

// Pujar foto (opcional)
$foto_url = null;
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = __DIR__ . '/../uploads/infraestructura/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $fileName = time() . "_" . basename($_FILES['foto']['name']);
    $targetPath = $uploadDir . $fileName;
    if (move_uploaded_file($_FILES['foto']['tmp_name'], $targetPath)) {
        $foto_url = 'uploads/infraestructura/' . $fileName;
    }
}

// Preparar la insercio
$sql = "INSERT INTO Infraestructura (id_parcela, tipus, descripcio, geometria, geometria_kml, foto_url) 
        VALUES (?, ?, ?, ST_GeomFromText(?), ?, ?)";
$stmt = $conn->prepare($sql);
if (!$stmt) die("❌ Error en preparar la consulta: " . $conn->error);

// Bind de parametres
$stmt->bind_param("isssss", $id_parcela, $tipus, $descripcio, $geometria, $geometria_kml, $foto_url);

// Executar
if ($stmt->execute()) {
    echo "<p style='color:green;'>✅ Infraestructura guardada correctament!</p>";
    echo "<p>ID assignat: <b>" . $conn->insert_id . "</b></p>";
} else {
    echo "<p style='color:red;'>❌ Error en guardar la infraestructura: " . $stmt->error . "</p>";
}
echo "<p><a href='infraestructura.php'>Tornar</a></p>";

// Tancar
$stmt->close();
$conn->close();
?>

