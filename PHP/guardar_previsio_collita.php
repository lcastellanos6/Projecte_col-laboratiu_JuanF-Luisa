<?php
require_once __DIR__ . '/db.php';

$conn = db_connect();

$id_parcela_raw = trim($_POST['id_parcela'] ?? '');
$id_varietat_raw = trim($_POST['id_varietat'] ?? '');
$campanya_any_raw = trim($_POST['campanya_any'] ?? '');
$estimacio_produccio_raw = trim($_POST['estimacio_produccio'] ?? '');
$unitat_raw = trim($_POST['unitat'] ?? '');
$data_estimada_collita_raw = trim($_POST['data_estimada_collita'] ?? '');
$model_predictiu_raw = trim($_POST['model_predictiu'] ?? '');

if ($id_parcela_raw === '' || $id_varietat_raw === '' || $campanya_any_raw === '' || !ctype_digit($id_parcela_raw) || !ctype_digit($id_varietat_raw)) {
    $conn->close();
    echo "<p style='color:red; font-weight:bold;'>Dades obligatòries no vàlides.</p>";
    exit;
}

$id_parcela = (int) $id_parcela_raw;
$id_varietat = (int) $id_varietat_raw;
$campanya_any = ctype_digit($campanya_any_raw) ? (int) $campanya_any_raw : 0;
if ($campanya_any < 2000 || $campanya_any > 2100) {
    $conn->close();
    echo "<p style='color:red; font-weight:bold;'>Campanya any fora de rang.</p>";
    exit;
}

$estimacio_produccio = ($estimacio_produccio_raw !== '' && is_numeric($estimacio_produccio_raw)) ? (float) $estimacio_produccio_raw : null;
$unitat = in_array($unitat_raw, ['kg', 'Tn'], true) ? $unitat_raw : 'kg';
$data_estimada_collita = $data_estimada_collita_raw !== '' ? $data_estimada_collita_raw : null;
$model_predictiu = $model_predictiu_raw !== '' ? $model_predictiu_raw : null;

$stmt = $conn->prepare("
    INSERT INTO previsio_collita
        (id_parcela, id_varietat, campanya_any, estimacio_produccio, unitat, data_estimada_collita, model_predictiu)
    VALUES (?, ?, ?, ?, ?, ?, ?)
");

if ($stmt) {
    $stmt->bind_param(
        'iiidsss',
        $id_parcela,
        $id_varietat,
        $campanya_any,
        $estimacio_produccio,
        $unitat,
        $data_estimada_collita,
        $model_predictiu
    );

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        echo "<h3>Previsió de producció registrada correctament!</h3>";
        echo "<a href='../HTML/previsio_collita.html'>Afegir una altra</a>";
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
