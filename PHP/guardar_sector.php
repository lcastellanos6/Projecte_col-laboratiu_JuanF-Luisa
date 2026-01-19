<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "web";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("❌ Error de connexió: " . $conn->connect_error);

// Recollir dades del formulari
$nom = $_POST['nom'] ?? '';
$superficie = !empty($_POST['superficie']) ? floatval($_POST['superficie']) : null;
$geometria = $_POST['geometria'] ?? '';
$geometria_kml = $_POST['geometria_kml'] ?? '';
$foto_url = $_POST['foto_url'] ?? '';
$estat_productiu = $_POST['estat_productiu'] ?? 'Plantat';

// Validació mínima
if (empty(trim($nom))) {
    $_SESSION['form_data'] = $_POST;
    header("Location: ../HTML/sector_nou.php?error=required");
    exit;
}
if (empty(trim($geometria))) {
    $_SESSION['form_data'] = $_POST;
    header("Location: ../HTML/sector_nou.php?error=geometry");
    exit;
}
if (empty(trim($geometria_kml))) {
    $geometria_kml = $geometria;
}

// Inserció amb geometria GeoJSON
$sql = "INSERT INTO sector (
    nom, superficie, geometria, geometria_kml, foto_url, estat_productiu
) VALUES (?, ?, ST_GeomFromGeoJSON(?), ?, ?, ?)";

$stmt = $conn->prepare($sql);
if (!$stmt) die("❌ Error en preparar la consulta: " . $conn->error);

$stmt->bind_param("sdssss", $nom, $superficie, $geometria, $geometria_kml, $foto_url, $estat_productiu);

try {
    if ($stmt->execute()) {
        unset($_SESSION['form_data']);
        header("Location: consulta_parcela_sector.php");
        exit;
    }
} catch (mysqli_sql_exception $e) {
    $_SESSION['form_data'] = $_POST;
    header("Location: ../HTML/sector_nou.php?error=save");
    exit;
}

$_SESSION['form_data'] = $_POST;
header("Location: ../HTML/sector_nou.php?error=save");
exit;

$stmt->close();
$conn->close();
?>





