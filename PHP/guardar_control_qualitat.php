<?php
require_once __DIR__ . '/db.php';

$conn = db_connect();

// Dades
$lot_id_raw = trim($_POST['lot_id'] ?? '');
$data_control = trim($_POST['data_control'] ?? '');

$calibre = trim($_POST['calibre'] ?? '') ?: null;
$color = trim($_POST['color'] ?? '') ?: null;
$fermesa = trim($_POST['fermesa'] ?? '') ?: null;
$defectes = trim($_POST['defectes'] ?? '') ?: null;
$sabor = trim($_POST['sabor'] ?? '') ?: null;
$aroma = trim($_POST['aroma'] ?? '') ?: null;
$observacions = trim($_POST['observacions'] ?? '') ?: null;
$qualificacio_final = trim($_POST['qualificacio_final'] ?? '') ?: null;

// Validació
if ($lot_id_raw === '' || $data_control === '' || !ctype_digit($lot_id_raw)) {
    echo "Dades incorrectes";
    exit;
}

$lot_id = (int)$lot_id_raw;

// SQL
$stmt = $conn->prepare("
    INSERT INTO control_qualitat
    (lot_id, data_control, calibre, color, fermesa, defectes, sabor, aroma, observacions, qualificacio_final)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "isssssssss",
    $lot_id,
    $data_control,
    $calibre,
    $color,
    $fermesa,
    $defectes,
    $sabor,
    $aroma,
    $observacions,
    $qualificacio_final
);

// Execució
if ($stmt->execute()) {
    echo "<h3>Control guardat correctament!</h3>";
    echo "<a href='../HTML/control_qualitat.html'>Tornar</a>";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
