<?php
require_once __DIR__ . '/db.php';

$q = trim($_GET['q'] ?? '');
$tipus = $_GET['tipus'] ?? '';
$id_magatzem = $_GET['id_magatzem'] ?? '';
$limit = 10;

if ($q === '') {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([]);
    exit;
}

$conn = db_connect();

$sql = "
    SELECT DISTINCT p.nom_comercial
    FROM producte p
    LEFT JOIN producte_lot pl ON pl.id_producte = p.id_producte
";
$condicions = ["p.nom_comercial LIKE ?"];
$params = [$q . '%'];
$types = 's';

if ($tipus !== '') {
    $condicions[] = "p.tipus = ?";
    $params[] = $tipus;
    $types .= 's';
}
if ($id_magatzem !== '' && ctype_digit($id_magatzem)) {
    $condicions[] = "pl.id_magatzem = ?";
    $params[] = (int) $id_magatzem;
    $types .= 'i';
}

$sql .= " WHERE " . implode(" AND ", $condicions) . " ORDER BY (p.nom_comercial = ?) DESC, p.nom_comercial LIMIT ?";
$params[] = $q;
$types .= 's';
$params[] = $limit;
$types .= 'i';

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$items = [];
while ($row = $result->fetch_assoc()) {
    $items[] = $row['nom_comercial'];
}

$stmt->close();
$conn->close();

header('Content-Type: application/json; charset=utf-8');
echo json_encode($items);
