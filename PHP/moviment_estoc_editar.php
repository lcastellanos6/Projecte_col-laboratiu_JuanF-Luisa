<?php
require_once __DIR__ . '/db.php';

$conn = db_connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_mov = filter_input(INPUT_POST, 'id_mov', FILTER_VALIDATE_INT);
    $id_lot_raw = trim($_POST['id_lot'] ?? '');
    $data_raw = trim($_POST['data'] ?? '');
    $quantitat_raw = trim($_POST['quantitat'] ?? '');
    $motiu_raw = trim($_POST['motiu'] ?? '');
    $id_aplicacio_raw = trim($_POST['id_aplicacio'] ?? '');
    $observacions_raw = trim($_POST['observacions'] ?? '');

    if (!$id_mov || $id_lot_raw === '' || $quantitat_raw === '' || $motiu_raw === '') {
        $conn->close();
        echo "<p style='color:red; font-weight:bold;'>Dades no vàlides per actualitzar el moviment.</p>";
        exit;
    }

    $id_lot = ctype_digit($id_lot_raw) ? (int) $id_lot_raw : 0;
    if ($id_lot <= 0 || !is_numeric($quantitat_raw)) {
        $conn->close();
        echo "<p style='color:red; font-weight:bold;'>Valors no vàlids en lot o quantitat.</p>";
        exit;
    }

    $quantitat = (float) $quantitat_raw;
    $data = $data_raw !== '' ? str_replace('T', ' ', $data_raw) : null;
    $motius_permesos = ['Compra', 'Ajust', 'Aplicacio'];
    $motiu = in_array($motiu_raw, $motius_permesos, true) ? $motiu_raw : null;
    $id_aplicacio = ($id_aplicacio_raw !== '' && ctype_digit($id_aplicacio_raw)) ? (int) $id_aplicacio_raw : null;
    $observacions = $observacions_raw !== '' ? $observacions_raw : null;

    if ($motiu === null) {
        $conn->close();
        echo "<p style='color:red; font-weight:bold;'>Motiu no vàlid.</p>";
        exit;
    }

    $sql = "
        UPDATE moviment_estoc
        SET
            id_lot = ?,
            data = COALESCE(?, data),
            quantitat = ?,
            motiu = ?,
            id_aplicacio = ?,
            observacions = ?
        WHERE id_mov = ?
    ";

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param(
            'isdsssi',
            $id_lot,
            $data,
            $quantitat,
            $motiu,
            $id_aplicacio,
            $observacions,
            $id_mov
        );

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header('Location: moviment_estoc_detall.php?id_mov=' . $id_mov . '&msg=moviment_actualitzat');
            exit;
        }
        $stmt->close();
    }

    $conn->close();
    echo "<p style='color:red; font-weight:bold;'>Error en actualitzar el moviment.</p>";
    exit;
}

$id_mov = filter_input(INPUT_GET, 'id_mov', FILTER_VALIDATE_INT);
if (!$id_mov) {
    $conn->close();
    echo "<p style='color:red; font-weight:bold;'>ID de moviment no vàlid.</p>";
    exit;
}

$stmt = $conn->prepare("
    SELECT
        id_mov,
        id_lot,
        data,
        quantitat,
        motiu,
        id_aplicacio,
        observacions
    FROM moviment_estoc
    WHERE id_mov = ?
    LIMIT 1
");
$moviment = null;
if ($stmt) {
    $stmt->bind_param('i', $id_mov);
    if ($stmt->execute()) {
        $res = $stmt->get_result();
        $moviment = $res ? $res->fetch_assoc() : null;
    }
    $stmt->close();
}

$conn->close();

if (!$moviment) {
    echo "<p style='color:red; font-weight:bold;'>Moviment no trobat.</p>";
    exit;
}

$data_val = '';
if (!empty($moviment['data'])) {
    $ts = strtotime((string) $moviment['data']);
    if ($ts !== false) {
        $data_val = date('Y-m-d\TH:i', $ts);
    }
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Editar moviment d'estoc</title>
    <link rel="stylesheet" href="../HTML/styles.css">
</head>
<body>
<div class="page">
    <div class="page-header">
        <h1>Editar moviment d'estoc</h1>
    </div>

    <div class="panel">
        <form method="post" action="moviment_estoc_editar.php">
            <input type="hidden" name="id_mov" value="<?php echo (int) $moviment['id_mov']; ?>">

            <label>ID Lot *</label>
            <input type="number" name="id_lot" value="<?php echo htmlspecialchars($moviment['id_lot'] ?? ''); ?>" required>

            <label>Data</label>
            <input type="datetime-local" name="data" value="<?php echo htmlspecialchars($data_val); ?>">

            <label>Quantitat *</label>
            <input type="number" step="0.001" name="quantitat" value="<?php echo htmlspecialchars($moviment['quantitat'] ?? ''); ?>" required>

            <label>Motiu *</label>
            <select name="motiu" required>
                <?php foreach (['Compra', 'Ajust', 'Aplicacio'] as $m): ?>
                    <option value="<?php echo $m; ?>" <?php echo (($moviment['motiu'] ?? '') === $m) ? 'selected' : ''; ?>><?php echo $m; ?></option>
                <?php endforeach; ?>
            </select>

            <label>ID Aplicació</label>
            <input type="number" name="id_aplicacio" value="<?php echo htmlspecialchars($moviment['id_aplicacio'] ?? ''); ?>">

            <label>Observacions</label>
            <textarea name="observacions"><?php echo htmlspecialchars($moviment['observacions'] ?? ''); ?></textarea>

            <button type="submit" class="btn btn-primary btn-full mt-2">Guardar canvis</button>
            <a class="btn btn-ghost btn-full mt-2" href="moviment_estoc_detall.php?id_mov=<?php echo (int) $moviment['id_mov']; ?>">Cancel·lar</a>
        </form>
    </div>
</div>
</body>
</html>
