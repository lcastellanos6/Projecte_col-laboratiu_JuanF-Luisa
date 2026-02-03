<?php
require_once __DIR__ . '/db.php';
$conn = db_connect();

$id_especie = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id_especie) {
    $conn->close();
    header('Location: consulta_cultius_varietats.php?err=especie_eliminar');
    exit;
}

$stmt = $conn->prepare('SELECT COUNT(*) AS total FROM varietat WHERE id_especie = ?');
$stmt->bind_param('i', $id_especie);
$stmt->execute();
$res = $stmt->get_result();
$te_varietats = false;
if ($res) {
    $row = $res->fetch_assoc();
    $te_varietats = (int)($row['total'] ?? 0) > 0;
}
$stmt->close();

if ($te_varietats) {
    $conn->close();
    header('Location: consulta_cultius_varietats.php?err=especie_eliminar_dependencia');
    exit;
}

$stmt = $conn->prepare('DELETE FROM especie WHERE id_especie = ?');
$stmt->bind_param('i', $id_especie);
if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    header('Location: consulta_cultius_varietats.php?msg=especie_eliminada');
    exit;
}

$stmt->close();
$conn->close();
header('Location: consulta_cultius_varietats.php?err=especie_eliminar');
exit;
?>
