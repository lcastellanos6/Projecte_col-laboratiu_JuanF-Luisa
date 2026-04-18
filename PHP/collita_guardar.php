<?php
require_once __DIR__ . '/db.php';

$conn = db_connect();

$plantacio_id_raw = trim((string)($_POST['plantacio_id'] ?? ''));
$data_inici = trim((string)($_POST['data_inici'] ?? ''));
$data_fi = trim((string)($_POST['data_fi'] ?? ''));
$quantitat_total_raw = trim((string)($_POST['quantitat_total'] ?? ''));
$unitat = trim((string)($_POST['unitat'] ?? 'kg'));
$condicions_ambientals = trim((string)($_POST['condicions_ambientals'] ?? ''));
$id_estat_raw = trim((string)($_POST['id_estat'] ?? ''));
$maduresa = trim((string)($_POST['maduresa'] ?? ''));
$incidencies = trim((string)($_POST['incidencies'] ?? ''));
$id_operari_raw = trim((string)($_POST['id_operari'] ?? ''));
$id_equip_raw = trim((string)($_POST['id_equip'] ?? ''));

if ($plantacio_id_raw === '' || $data_inici === '' || !ctype_digit($plantacio_id_raw)) {
    $conn->close();
    header('Location: collita_nova.php?error=required');
    exit;
}

$plantacio_id = (int)$plantacio_id_raw;
$data_fi = $data_fi !== '' ? $data_fi : null;
$quantitat_total = $quantitat_total_raw !== '' ? (float)$quantitat_total_raw : null;
$condicions_ambientals = $condicions_ambientals !== '' ? $condicions_ambientals : null;
$maduresa = $maduresa !== '' ? $maduresa : null;
$incidencies = $incidencies !== '' ? $incidencies : null;

$allowedUnits = ['kg', 'caixa', 'bin'];
if (!in_array($unitat, $allowedUnits, true)) {
    $unitat = 'kg';
}

$id_estat = ($id_estat_raw !== '' && ctype_digit($id_estat_raw)) ? (int)$id_estat_raw : null;
$id_operari = ($id_operari_raw !== '' && ctype_digit($id_operari_raw)) ? (int)$id_operari_raw : null;
$id_equip = ($id_equip_raw !== '' && ctype_digit($id_equip_raw)) ? (int)$id_equip_raw : null;

$stmt = $conn->prepare("
    INSERT INTO collita
    (data_inici, data_fi, plantacio_id, quantitat_total, unitat, condicions_ambientals, id_estat, maduresa, incidencies, id_operari, id_equip)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

if (!$stmt) {
    $conn->close();
    header('Location: collita_nova.php?error=save');
    exit;
}

$stmt->bind_param(
    'ssidsisssii',
    $data_inici,
    $data_fi,
    $plantacio_id,
    $quantitat_total,
    $unitat,
    $condicions_ambientals,
    $id_estat,
    $maduresa,
    $incidencies,
    $id_operari,
    $id_equip
);

try {
    if (!$stmt->execute()) {
        $stmt->close();
        $conn->close();
        header('Location: collita_nova.php?error=save');
        exit;
    }
} catch (mysqli_sql_exception $e) {
    $stmt->close();
    $conn->close();
    header('Location: collita_nova.php?error=save');
    exit;
}

$collita_id = (int)$conn->insert_id;
$stmt->close();

if ($id_operari !== null) {
    $stmtOp = $conn->prepare("INSERT IGNORE INTO collita_operari (collita_id, id_operari, rol) VALUES (?, ?, 'Recol·lector')");
    if ($stmtOp) {
        $stmtOp->bind_param('ii', $collita_id, $id_operari);
        $stmtOp->execute();
        $stmtOp->close();
    }
}

if ($id_equip !== null) {
    $stmtEq = $conn->prepare("INSERT IGNORE INTO collita_equip (collita_id, id_equip) VALUES (?, ?)");
    if ($stmtEq) {
        $stmtEq->bind_param('ii', $collita_id, $id_equip);
        $stmtEq->execute();
        $stmtEq->close();
    }
}

$conn->close();
header('Location: collita_nova.php?ok=1');
exit;
?>