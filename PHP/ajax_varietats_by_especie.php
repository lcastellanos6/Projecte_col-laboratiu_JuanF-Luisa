<?php
require_once __DIR__ . '/db.php';
$conn = db_connect();

$id_especie = filter_input(INPUT_GET, 'id_especie', FILTER_VALIDATE_INT);
$id_especie = $id_especie ? $id_especie : 0;

header('Content-Type: application/json; charset=utf-8');

$data = [];
if ($id_especie > 0) {
    $stmt = $conn->prepare('SELECT id_varietat, nom_comu, nom_cientific FROM varietat WHERE id_especie = ? ORDER BY nom_comu');
    $stmt->bind_param('i', $id_especie);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $data[] = $row;
        }
        $res->free();
    }
    $stmt->close();
} else {
    $res = $conn->query('SELECT id_varietat, nom_comu, nom_cientific FROM varietat ORDER BY nom_comu');
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $data[] = $row;
        }
        $res->free();
    }
}

$conn->close();

echo json_encode($data);
?>
