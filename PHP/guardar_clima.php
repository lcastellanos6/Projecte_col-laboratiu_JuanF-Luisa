<?php
require_once __DIR__ . '/db.php';

$conn = db_connect();

$id_plantacio_raw = trim($_POST['id_plantacio'] ?? '');
$any_temp_raw = trim($_POST['any_temp'] ?? '');
$temperatura_mitjana_raw = trim($_POST['temperatura_mitjana'] ?? '');
$precipitacio_total_raw = trim($_POST['precipitacio_total'] ?? '');
$altres_factors_raw = trim($_POST['altres_factors'] ?? '');

if ($id_plantacio_raw === '' || $any_temp_raw === '' || !ctype_digit($id_plantacio_raw) || !ctype_digit($any_temp_raw)) {
    $conn->close();
    echo "<p style='color:red; font-weight:bold;'>ID plantació i any són obligatoris.</p>";
    exit;
}

$id_plantacio = (int) $id_plantacio_raw;
$any_temp = (int) $any_temp_raw;
$temperatura_mitjana = ($temperatura_mitjana_raw !== '' && is_numeric($temperatura_mitjana_raw)) ? (float) $temperatura_mitjana_raw : null;
$precipitacio_total = ($precipitacio_total_raw !== '' && is_numeric($precipitacio_total_raw)) ? (float) $precipitacio_total_raw : null;
$altres_factors = $altres_factors_raw !== '' ? $altres_factors_raw : null;

$stmt = $conn->prepare("
    INSERT INTO clima (id_plantacio, any_temp, temperatura_mitjana, precipitacio_total, altres_factors)
    VALUES (?, ?, ?, ?, ?)
");

if ($stmt) {
    $stmt->bind_param(
        'iidds',
        $id_plantacio,
        $any_temp,
        $temperatura_mitjana,
        $precipitacio_total,
        $altres_factors
    );

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        echo "<h3>Registre climàtic guardat correctament!</h3>";
        echo "<a href='../HTML/clima.html'>Afegir un altre</a>";
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
