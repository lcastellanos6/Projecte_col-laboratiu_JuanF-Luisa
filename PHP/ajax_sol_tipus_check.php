<?php
require_once __DIR__ . '/db.php';
$conn = db_connect();

header('Content-Type: application/json; charset=utf-8');

$tipus = trim($_GET['tipus'] ?? '');
$id_sol = filter_input(INPUT_GET, 'id_sol', FILTER_VALIDATE_INT);
$id_sol = $id_sol ? $id_sol : 0;

function normalitza_tipus(string $valor): string {
    $valor = mb_strtolower($valor, 'UTF-8');
    $valor = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $valor);
    $valor = preg_replace('/[^a-z0-9 ]/', '', $valor ?? '');
    $valor = preg_replace('/\s+/', ' ', trim($valor));
    return $valor;
}

if ($tipus === '') {
    echo json_encode(['exists' => false, 'match' => '', 'normalized' => '']);
    $conn->close();
    exit;
}

$tipus_norm = normalitza_tipus($tipus);
$exists = false;
$match = '';

$stmt = $conn->prepare("SELECT id_sol, tipus FROM sol");
$stmt->execute();
$res = $stmt->get_result();
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $row_id = (int)($row['id_sol'] ?? 0);
        if ($id_sol > 0 && $row_id === $id_sol) {
            continue;
        }
        $row_norm = normalitza_tipus($row['tipus'] ?? '');
        if ($row_norm !== '' && $row_norm === $tipus_norm) {
            $exists = true;
            $match = $row['tipus'] ?? '';
            break;
        }
    }
    $res->free();
}
$stmt->close();
$conn->close();

echo json_encode(['exists' => $exists, 'match' => $match, 'normalized' => $tipus_norm]);
?>
