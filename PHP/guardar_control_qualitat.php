<?php
require_once __DIR__ . '/db.php';

$conn = db_connect();

$lot_id_raw = trim($_POST['lot_id'] ?? '');
$data_control = trim($_POST['data_control'] ?? '');
$calibre_raw = trim($_POST['calibre'] ?? '');
$color_raw = trim($_POST['color'] ?? '');
$fermesa_raw = trim($_POST['fermesa'] ?? '');
$defectes_raw = trim($_POST['defectes'] ?? '');
$sabor_raw = trim($_POST['sabor'] ?? '');
$aroma_raw = trim($_POST['aroma'] ?? '');
$observacions_raw = trim($_POST['observacions'] ?? '');
$qualificacio_final_raw = trim($_POST['qualificacio_final'] ?? '');

if ($lot_id_raw === '' || $data_control === '' || !ctype_digit($lot_id_raw)) {
    $conn->close();
    echo "<p style='color:red; font-weight:bold;'>Dades obligatòries no vàlides.</p>";
    exit;
}

$lot_id = (int) $lot_id_raw;
$calibre = $calibre_raw !== '' ? $calibre_raw : null;
$color = $color_raw !== '' ? $color_raw : null;
$fermesa = $fermesa_raw !== '' ? $fermesa_raw : null;
$defectes = $defectes_raw !== '' ? $defectes_raw : null;
$sabor = $sabor_raw !== '' ? $sabor_raw : null;
$aroma = $aroma_raw !== '' ? $aroma_raw : null;
$observacions = $observacions_raw !== '' ? $observacions_raw : null;
$qualificacio_final = $qualificacio_final_raw !== '' ? $qualificacio_final_raw : null;

$stmt = $conn->prepare("
    INSERT INTO control_qualitat
        (lot_id, data_control, calibre, color, fermesa, defectes, sabor, aroma, observacions, qualificacio_final)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

if ($stmt) {
    $stmt->bind_param(
        'isssssssss',
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

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        echo "<h3>Control de qualitat registrat correctament!</h3>";
        echo "<a href='../HTML/control_qualitat.html'>Afegir un altre</a>";
        exit;
    }

    $error = $stmt->error;
    $stmt->close();
    $conn->close();
    echo "Error en guardar: " . htmlspecialchars($error);
    exit;
}

$conn->close();
echo "<p style='color:red; font-weight:bold;'>No s'ha pogut preparar la inserció.</p>";
?>
