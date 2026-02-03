<?php
require_once __DIR__ . '/db.php';
$conn = db_connect();

$camp = $_GET['camp'] ?? '';
$q = trim($_GET['q'] ?? '');
$id_especie = filter_input(INPUT_GET, 'id_especie', FILTER_VALIDATE_INT);
$id_especie = $id_especie ? $id_especie : 0;

header('Content-Type: application/json; charset=utf-8');

if (!in_array($camp, ['nom_comu', 'nom_cientific'], true)) {
    echo json_encode(['items' => [], 'exact' => false]);
    exit;
}

$items = [];
$exact = false;

if ($q !== '') {
    $like = $q . '%';
    $sql_items = "SELECT $camp AS valor FROM especie WHERE $camp LIKE ? ORDER BY $camp LIMIT 8";
    $stmt = $conn->prepare($sql_items);
    $stmt->bind_param('s', $like);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $items[] = $row['valor'];
        }
        $res->free();
    }
    $stmt->close();

    $sql_exact = "SELECT COUNT(*) AS total FROM especie WHERE LOWER($camp) = LOWER(?)";
    if ($id_especie > 0) {
        $sql_exact .= " AND id_especie <> ?";
        $stmt = $conn->prepare($sql_exact);
        $stmt->bind_param('si', $q, $id_especie);
    } else {
        $stmt = $conn->prepare($sql_exact);
        $stmt->bind_param('s', $q);
    }
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res) {
        $row = $res->fetch_assoc();
        $exact = (int)($row['total'] ?? 0) > 0;
    }
    $stmt->close();
}

$conn->close();

echo json_encode(['items' => $items, 'exact' => $exact]);
?>
