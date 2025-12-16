<?php
header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "web";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Connexió fallida: " . $conn->connect_error]);
    exit;
}

// Filtres GET (opcionals)
$filters = [];
$params = [];

if (!empty($_GET['tipus_sol'])) {
    $filters[] = "tipus_sol = ?";
    $params[] = $_GET['tipus_sol'];
}
if (!empty($_GET['orientacio'])) {
    $filters[] = "orientacio = ?";
    $params[] = $_GET['orientacio'];
}
if (!empty($_GET['min_pendent'])) {
    $filters[] = "pendent >= ?";
    $params[] = $_GET['min_pendent'];
}
if (!empty($_GET['max_pendent'])) {
    $filters[] = "pendent <= ?";
    $params[] = $_GET['max_pendent'];
}

// Construir consulta
$sql = "SELECT id_parcela, ref_cadastral, nom, superficie, descripcio, municipi, 
               ST_AsGeoJSON(geometria) as geojson, geometria_kml, foto_url, edafo, documentacio, pendent, orientacio, tipus_sol, created_at
        FROM parcela";

if ($filters) {
    $sql .= " WHERE " . implode(" AND ", $filters);
}

$stmt = $conn->prepare($sql);
if ($params) {
    // crear tipus per bind_param: tots strings o floats? assumim strings i números; construir tipus dinàmic
    $types = '';
    foreach ($params as $p) {
        if (is_numeric($p)) $types .= 'd'; else $types .= 's';
    }
    $stmt->bind_param($types, ...$params);
}

if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(["error" => "Error en la consulta: " . $stmt->error]);
    exit;
}

$res = $stmt->get_result();

$features = [];
while ($row = $res->fetch_assoc()) {
    $geom = null;
    if (!empty($row['geojson'])) {
        $geom = json_decode($row['geojson'], true);
    }
    // propietats que volem exposar
    $props = [
        "id_parcela" => (int)$row['id_parcela'],
        "ref_cadastral" => $row['ref_cadastral'],
        "nom" => $row['nom'],
        "superficie" => $row['superficie'],
        "descripcio" => $row['descripcio'],
        "municipi" => $row['municipi'],
        "geometria_kml" => $row['geometria_kml'],
        "foto_url" => $row['foto_url'],
        "edafo" => $row['edafo'],
        "documentacio" => $row['documentacio'],
        "pendent" => $row['pendent'],
        "orientacio" => $row['orientacio'],
        "tipus_sol" => $row['tipus_sol'],
        "created_at" => $row['created_at']
    ];
    $features[] = [
        "type" => "Feature",
        "id" => (int)$row['id_parcela'],
        "geometry" => $geom,
        "properties" => $props
    ];
}

$fc = ["type" => "FeatureCollection", "features" => $features];
echo json_encode($fc, JSON_UNESCAPED_UNICODE);
$stmt->close();
$conn->close();
?>
