<?php
require_once __DIR__ . '/db.php';

$conn = db_connect();

$id = intval($_POST['id'] ?? 0);
$ref_cadastral = $_POST['ref_cadastral'] ?? '';
$nom = $_POST['nom'] ?? '';
$superficie = (isset($_POST['superficie']) && $_POST['superficie'] !== '') ? floatval($_POST['superficie']) : null;
$descripcio = $_POST['descripcio'] ?? null;
$municipi = $_POST['municipi'] ?? '';
$geometria = $_POST['geometria'] ?? '';
$geometria_kml = $_POST['geometria_kml'] ?? '';
$edafo = $_POST['edafo'] ?? null;
$documentacio = $_POST['documentacio'] ?? null;
$pendent = (isset($_POST['pendent']) && $_POST['pendent'] !== '') ? floatval($_POST['pendent']) : null;
$orientacio = $_POST['orientacio'] ?? null;

// Recuperar foto actual
$stmt = $conn->prepare("SELECT foto_url FROM parcela WHERE id_parcela=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res ? $res->fetch_assoc() : null;
$stmt->close();
$foto_url = $row['foto_url'] ?? null;

// Pujada de foto nova
if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
    $uploadDir = __DIR__ . DIRECTORY_SEPARATOR . "uploads" . DIRECTORY_SEPARATOR;
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $fileName = time() . "_" . basename($_FILES['foto']['name']);
    $filePath = $uploadDir . $fileName;
    if (move_uploaded_file($_FILES['foto']['tmp_name'], $filePath)) {
        $foto_url = "uploads/" . $fileName;
    }
}

$has_geometria = !empty($geometria);

if ($has_geometria) {
    $geometria_geometry = geojson_feature_to_geometry_json($geometria);
    if ($geometria_geometry === null) {
        $has_geometria = false;
    }
}

if ($has_geometria) {
    $stmt = $conn->prepare("
    UPDATE parcela SET
      ref_cadastral=?,
      nom=?,
      superficie=?,
      descripcio=?,
      municipi=?,
      geometria=ST_GeomFromGeoJSON(?),
      geometria_kml=?,
      foto_url=?,
      edafo=?,
      documentacio=?,
      pendent=?,
      orientacio=?
    WHERE id_parcela=?
    ");
    $stmt->bind_param(
        "ssdsssssssdsi",
        $ref_cadastral,
        $nom,
        $superficie,
        $descripcio,
        $municipi,
        $geometria_geometry,
        $geometria_kml,
        $foto_url,
        $edafo,
        $documentacio,
        $pendent,
        $orientacio,
        $id
    );
} else {
    $stmt = $conn->prepare("
    UPDATE parcela SET
      ref_cadastral=?,
      nom=?,
      superficie=?,
      descripcio=?,
      municipi=?,
      foto_url=?,
      edafo=?,
      documentacio=?,
      pendent=?,
      orientacio=?
    WHERE id_parcela=?
    ");
    $stmt->bind_param(
        "ssdsssssdsi",
        $ref_cadastral,
        $nom,
        $superficie,
        $descripcio,
        $municipi,
        $foto_url,
        $edafo,
        $documentacio,
        $pendent,
        $orientacio,
        $id
    );
}
$stmt->execute();
$stmt->close();

$sol_ids = $_POST['id_sol'] ?? [];
if (!is_array($sol_ids)) {
    $sol_ids = [$sol_ids];
}
$sol_ids = array_values(array_unique(array_filter($sol_ids, 'ctype_digit')));

$stmt = $conn->prepare("DELETE FROM parcela_sol WHERE id_parcela=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

if (!empty($sol_ids)) {
    $insert_sol = $conn->prepare("INSERT INTO parcela_sol (id_parcela, id_sol) VALUES (?, ?)");
    foreach ($sol_ids as $id_sol) {
        $id_sol = (int)$id_sol;
        $insert_sol->bind_param("ii", $id, $id_sol);
        $insert_sol->execute();
    }
    $insert_sol->close();
}

header("Location: consulta_parcela_sector.php");
exit;
