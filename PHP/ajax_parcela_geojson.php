<?php
require_once __DIR__ . '/db.php';

header('Content-Type: application/json; charset=utf-8');

$id_parcela = $_GET['id_parcela'] ?? '';
if ($id_parcela === '' || !ctype_digit($id_parcela)) {
    echo json_encode(['error' => 'id_parcela_invalid']);
    exit;
}

$conn = db_connect();
$stmt = $conn->prepare('SELECT ST_AsGeoJSON(geometria) AS geojson FROM parcela WHERE id_parcela = ?');
$stmt->bind_param('i', $id_parcela);
$stmt->execute();
$result = $stmt->get_result();
$row = $result ? $result->fetch_assoc() : null;

$geojson = null;
if ($row && !empty($row['geojson'])) {
    $geojson = json_decode($row['geojson'], true);
}

$stmt->close();
$conn->close();

echo json_encode(['geojson' => $geojson]);
