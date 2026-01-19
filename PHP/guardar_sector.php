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
$id_parcela = $_POST['id_parcela'] ?? '';

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
if (empty(trim((string)$id_parcela))) {
    $_SESSION['form_data'] = $_POST;
    header("Location: ../HTML/sector_nou.php?error=parcela_required");
    exit;
}
if (empty(trim($geometria_kml))) {
    $geometria_kml = $geometria;
}

$id_parcela = (int)$id_parcela;
$check = $conn->prepare("SELECT id_parcela FROM parcela WHERE id_parcela = ?");
if (!$check) {
    $_SESSION['form_data'] = $_POST;
    header("Location: ../HTML/sector_nou.php?error=save");
    exit;
}
$check->bind_param("i", $id_parcela);
$check->execute();
$check->store_result();
if ($check->num_rows === 0) {
    $check->close();
    $_SESSION['form_data'] = $_POST;
    header("Location: ../HTML/sector_nou.php?error=parcela_invalid");
    exit;
}
$check->close();

// Inserció amb geometria GeoJSON
$sql = "INSERT INTO sector (
    nom, superficie, geometria, geometria_kml, foto_url, estat_productiu
) VALUES (?, ?, ST_GeomFromGeoJSON(?), ?, ?, ?)";

$conn->begin_transaction();

$stmt = $conn->prepare($sql);
if (!$stmt) {
    $conn->rollback();
    $_SESSION['form_data'] = $_POST;
    header("Location: ../HTML/sector_nou.php?error=save");
    exit;
}

$stmt->bind_param("sdssss", $nom, $superficie, $geometria, $geometria_kml, $foto_url, $estat_productiu);

if (!$stmt->execute()) {
    $stmt->close();
    $conn->rollback();
    $_SESSION['form_data'] = $_POST;
    header("Location: ../HTML/sector_nou.php?error=save");
    exit;
}

$id_sector = $conn->insert_id;
$stmt->close();

$stmt_rel = $conn->prepare("INSERT INTO sector_parcela (id_sector, id_parcela) VALUES (?, ?)");
if (!$stmt_rel) {
    $conn->rollback();
    $_SESSION['form_data'] = $_POST;
    header("Location: ../HTML/sector_nou.php?error=save");
    exit;
}
$stmt_rel->bind_param("ii", $id_sector, $id_parcela);
if (!$stmt_rel->execute()) {
    $stmt_rel->close();
    $conn->rollback();
    $_SESSION['form_data'] = $_POST;
    header("Location: ../HTML/sector_nou.php?error=save");
    exit;
}
$stmt_rel->close();

if (!$conn->commit()) {
    $conn->rollback();
    $_SESSION['form_data'] = $_POST;
    header("Location: ../HTML/sector_nou.php?error=save");
    exit;
}

unset($_SESSION['form_data']);
header("Location: consulta_parcela_sector.php");
exit;

$conn->close();
?>





