<?php
require_once __DIR__ . '/db.php';

$conn = db_connect();

$id_aplicacio_raw = trim($_POST['id_aplicacio'] ?? '');
$id_fila_raw = trim($_POST['id_fila'] ?? '');
$estat_raw = trim($_POST['estat'] ?? 'Fet');
$volum_caldo_l_raw = trim($_POST['volum_caldo_l'] ?? '');
$data_execucio_raw = trim($_POST['data_execucio'] ?? '');
$id_operari_execucio_raw = trim($_POST['id_operari_execucio'] ?? '');

if ($id_aplicacio_raw === '' || $id_fila_raw === '' || !ctype_digit($id_aplicacio_raw) || !ctype_digit($id_fila_raw)) {
    $conn->close();
    echo "<p style='color:red; font-weight:bold;'>ID aplicació o ID fila no vàlids.</p>";
    exit;
}

$id_aplicacio = (int) $id_aplicacio_raw;
$id_fila = (int) $id_fila_raw;
$estat = in_array($estat_raw, ['Pendent', 'Fet', 'Parcial'], true) ? $estat_raw : 'Fet';
$volum_caldo_l = ($volum_caldo_l_raw !== '' && is_numeric($volum_caldo_l_raw)) ? (float) $volum_caldo_l_raw : null;
$data_execucio = $data_execucio_raw !== '' ? str_replace('T', ' ', $data_execucio_raw) : null;
$id_operari_execucio = ($id_operari_execucio_raw !== '' && ctype_digit($id_operari_execucio_raw)) ? (int) $id_operari_execucio_raw : null;

$stmt = $conn->prepare("
    INSERT INTO aplicacio_fila
        (id_aplicacio, id_fila, estat, volum_caldo_l, data_execucio, id_operari_execucio)
    VALUES (?, ?, ?, ?, ?, ?)
");

if ($stmt) {
    $stmt->bind_param(
        'iisdsi',
        $id_aplicacio,
        $id_fila,
        $estat,
        $volum_caldo_l,
        $data_execucio,
        $id_operari_execucio
    );

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        echo "<h3>Fila tractada registrada correctament!</h3>";
        echo "<a href='../HTML/aplicacio_fila.html?id_aplicacio=" . urlencode((string) $id_aplicacio) . "'>Afegir una altra fila</a>";
        echo " | ";
        echo "<a href='consulta_aplicacio_files.php?id_aplicacio=" . urlencode((string) $id_aplicacio) . "'>Veure files tractades</a>";
        exit;
    }

    $error = $stmt->error;
    $stmt->close();
    $conn->close();
    echo "Error en guardar fila tractada: " . htmlspecialchars($error);
    exit;
}

$conn->close();
echo "<p style='color:red; font-weight:bold;'>No s'ha pogut preparar la inserció.</p>";
?>
