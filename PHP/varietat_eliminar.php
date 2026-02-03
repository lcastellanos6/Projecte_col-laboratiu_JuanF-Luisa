<?php
require_once __DIR__ . '/db.php';
$conn = db_connect();

$id_varietat = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id_varietat) {
    $conn->close();
    header('Location: consulta_cultius_varietats.php?err=varietat_eliminar');
    exit;
}

$stmt = $conn->prepare('SELECT COUNT(*) AS total FROM plantacio WHERE id_varietat = ?');
$stmt->bind_param('i', $id_varietat);
$stmt->execute();
$res = $stmt->get_result();
$te_plantacions = false;
if ($res) {
    $row = $res->fetch_assoc();
    $te_plantacions = (int)($row['total'] ?? 0) > 0;
}
$stmt->close();

$stmt = $conn->prepare('SELECT COUNT(*) AS total FROM sector_varietat WHERE id_varietat = ?');
$stmt->bind_param('i', $id_varietat);
$stmt->execute();
$res = $stmt->get_result();
$te_sectors = false;
if ($res) {
    $row = $res->fetch_assoc();
    $te_sectors = (int)($row['total'] ?? 0) > 0;
}
$stmt->close();

if ($te_plantacions || $te_sectors) {
    $conn->close();
    header('Location: consulta_cultius_varietats.php?err=varietat_eliminar_dependencia');
    exit;
}

$stmt = $conn->prepare('DELETE FROM varietat WHERE id_varietat = ?');
$stmt->bind_param('i', $id_varietat);
if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    header('Location: consulta_cultius_varietats.php?msg=varietat_eliminada');
    exit;
}

$stmt->close();
$conn->close();
header('Location: consulta_cultius_varietats.php?err=varietat_eliminar');
exit;
?>
