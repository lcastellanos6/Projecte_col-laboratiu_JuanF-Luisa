<?php
session_start();

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

if (empty(trim((string)$ref_cadastral)) || empty(trim((string)$nom))) {
    $_SESSION['form_data'] = $_POST;
    header("Location: ../HTML/parcela_nou.php?error=required");
    exit;
}

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
    $_SESSION['form_data'] = $_POST;
    header("Location: ../HTML/parcela_nou.php?error=geometry");
    exit;
}

// -----------------------------------------
// INSERT FINAL
// -----------------------------------------
$sql = "INSERT INTO Parcela (
    ref_cadastral, nom, superficie, descripcio, municipi,
    geometria, geometria_kml, foto_url, edafo, documentacio,
    pendent, orientacio
) VALUES (?, ?, ?, ?, ?, ST_GeomFromGeoJSON(?), ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "ssdsssssssds",
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
    $orientacio
);

try {
    if ($stmt->execute()) {
        $id_parcela = $conn->insert_id;
        $sol_ids = $_POST['id_sol'] ?? [];
        if (!is_array($sol_ids)) {
            $sol_ids = [$sol_ids];
        }
        $sol_ids = array_values(array_unique(array_filter($sol_ids, 'ctype_digit')));
        if (!empty($sol_ids)) {
            $insert_sol = $conn->prepare("INSERT INTO parcela_sol (id_parcela, id_sol) VALUES (?, ?)");
            foreach ($sol_ids as $id_sol) {
                $id_sol = (int)$id_sol;
                $insert_sol->bind_param("ii", $id_parcela, $id_sol);
                $insert_sol->execute();
            }
            $insert_sol->close();
        }
        unset($_SESSION['form_data']);
        header("Location: consulta_parcela_sector.php");
        exit;
    }
} catch (mysqli_sql_exception $e) {
    $_SESSION['form_data'] = $_POST;
    if ($e->getCode() === 1062) {
        header("Location: ../HTML/parcela_nou.php?error=ref_cadastral_dup");
        exit;
    }
    header("Location: ../HTML/parcela_nou.php?error=save");
    exit;
}

$_SESSION['form_data'] = $_POST;
header("Location: ../HTML/parcela_nou.php?error=save");
exit;

$stmt->close();
$conn->close();

?>
