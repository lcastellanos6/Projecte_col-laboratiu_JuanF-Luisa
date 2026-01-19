<?php
$conn = new mysqli("localhost","root","","web");

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
$tipus_sol = $_POST['tipus_sol'] ?? '';

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

$has_geometria = !empty($geometria);

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
      tipus_sol=?,
      pendent=?,
      orientacio=?
    WHERE id_parcela=?
    ");
    $stmt->bind_param(
        "ssdssssssssdsi",
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
        $tipus_sol,
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
      tipus_sol=?,
      pendent=?,
      orientacio=?
    WHERE id_parcela=?
    ");
    $stmt->bind_param(
        "ssdsssssdssi",
        $ref_cadastral,
        $nom,
        $superficie,
        $descripcio,
        $municipi,
        $foto_url,
        $edafo,
        $documentacio,
        $tipus_sol,
        $pendent,
        $orientacio,
        $id
    );
}
$stmt->execute();
$stmt->close();

header("Location: consulta_parcela_sector.php");
exit;
