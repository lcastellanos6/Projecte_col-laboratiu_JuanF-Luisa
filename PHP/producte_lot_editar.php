<?php
require_once __DIR__ . '/db.php';

$conn = db_connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_lot = filter_input(INPUT_POST, 'id_lot', FILTER_VALIDATE_INT);
    $id_producte_raw = trim($_POST['id_producte'] ?? '');
    $numero_lot = trim($_POST['numero_lot'] ?? '');
    $data_caducitat_raw = trim($_POST['data_caducitat'] ?? '');
    $id_magatzem_raw = trim($_POST['id_magatzem'] ?? '');
    $quantitat_disponible_raw = trim($_POST['quantitat_disponible'] ?? '');
    $unitat_raw = trim($_POST['unitat'] ?? '');
    $fabricant_raw = trim($_POST['fabricant'] ?? '');
    $proveidor_raw = trim($_POST['proveidor'] ?? '');
    $data_compra_raw = trim($_POST['data_compra'] ?? '');
    $preu_unitari_raw = trim($_POST['preu_unitari'] ?? '');

    if (!$id_lot || $numero_lot === '' || $id_producte_raw === '' || $quantitat_disponible_raw === '') {
        $conn->close();
        echo "<p style='color:red; font-weight:bold;'>Dades no vàlides per actualitzar el lot.</p>";
        exit;
    }

    $id_producte = ctype_digit($id_producte_raw) ? (int) $id_producte_raw : 0;
    if ($id_producte <= 0 || !is_numeric($quantitat_disponible_raw)) {
        $conn->close();
        echo "<p style='color:red; font-weight:bold;'>ID producte o quantitat no vàlids.</p>";
        exit;
    }

    $data_caducitat = $data_caducitat_raw !== '' ? $data_caducitat_raw : null;
    $id_magatzem = ($id_magatzem_raw !== '' && ctype_digit($id_magatzem_raw)) ? (int) $id_magatzem_raw : null;
    $quantitat_disponible = (float) $quantitat_disponible_raw;
    $unitat = in_array($unitat_raw, ['L', 'mL', 'kg', 'g'], true) ? $unitat_raw : 'L';
    $fabricant = $fabricant_raw !== '' ? $fabricant_raw : null;
    $proveidor = $proveidor_raw !== '' ? $proveidor_raw : null;
    $data_compra = $data_compra_raw !== '' ? $data_compra_raw : null;
    $preu_unitari = ($preu_unitari_raw !== '' && is_numeric($preu_unitari_raw)) ? (float) $preu_unitari_raw : null;

    $stmt = $conn->prepare("
        UPDATE producte_lot
        SET
            id_producte = ?,
            numero_lot = ?,
            data_caducitat = ?,
            id_magatzem = ?,
            quantitat_disponible = ?,
            unitat = ?,
            fabricant = ?,
            proveidor = ?,
            data_compra = ?,
            preu_unitari = ?
        WHERE id_lot = ?
    ");

    if ($stmt) {
        $stmt->bind_param(
            'isssdssssdi',
            $id_producte,
            $numero_lot,
            $data_caducitat,
            $id_magatzem,
            $quantitat_disponible,
            $unitat,
            $fabricant,
            $proveidor,
            $data_compra,
            $preu_unitari,
            $id_lot
        );

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header('Location: producte_lot_detall.php?id_lot=' . $id_lot . '&msg=lot_actualitzat');
            exit;
        }
        $stmt->close();
    }

    $conn->close();
    echo "<p style='color:red; font-weight:bold;'>Error en actualitzar el lot.</p>";
    exit;
}

$id_lot = filter_input(INPUT_GET, 'id_lot', FILTER_VALIDATE_INT);
if (!$id_lot) {
    $conn->close();
    echo "<p style='color:red; font-weight:bold;'>ID de lot no vàlid.</p>";
    exit;
}

$stmt = $conn->prepare("
    SELECT
        id_lot,
        id_producte,
        numero_lot,
        data_caducitat,
        id_magatzem,
        quantitat_disponible,
        unitat,
        fabricant,
        proveidor,
        data_compra,
        preu_unitari
    FROM producte_lot
    WHERE id_lot = ?
    LIMIT 1
");

$lot = null;
if ($stmt) {
    $stmt->bind_param('i', $id_lot);
    if ($stmt->execute()) {
        $res = $stmt->get_result();
        $lot = $res ? $res->fetch_assoc() : null;
    }
    $stmt->close();
}

$conn->close();

if (!$lot) {
    echo "<p style='color:red; font-weight:bold;'>Lot no trobat.</p>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Editar lot de producte</title>
    <link rel="stylesheet" href="../HTML/styles.css">
</head>
<body>
<div class="page">
    <div class="page-header">
        <h1>Editar lot de producte</h1>
    </div>

    <div class="panel">
        <form method="post" action="producte_lot_editar.php">
            <input type="hidden" name="id_lot" value="<?php echo (int) $lot['id_lot']; ?>">

            <label>ID Producte *</label>
            <input type="number" name="id_producte" value="<?php echo htmlspecialchars($lot['id_producte'] ?? ''); ?>" required>

            <label>Número de lot *</label>
            <input type="text" name="numero_lot" value="<?php echo htmlspecialchars($lot['numero_lot'] ?? ''); ?>" required>

            <label>Data de caducitat</label>
            <input type="date" name="data_caducitat" value="<?php echo htmlspecialchars($lot['data_caducitat'] ?? ''); ?>">

            <label>ID Magatzem</label>
            <input type="number" name="id_magatzem" value="<?php echo htmlspecialchars($lot['id_magatzem'] ?? ''); ?>">

            <label>Quantitat disponible *</label>
            <input type="number" step="0.001" name="quantitat_disponible" value="<?php echo htmlspecialchars($lot['quantitat_disponible'] ?? ''); ?>" required>

            <label>Unitat *</label>
            <select name="unitat" required>
                <?php foreach (['L', 'mL', 'kg', 'g'] as $u): ?>
                    <option value="<?php echo $u; ?>" <?php echo (($lot['unitat'] ?? '') === $u) ? 'selected' : ''; ?>><?php echo $u; ?></option>
                <?php endforeach; ?>
            </select>

            <label>Fabricant</label>
            <input type="text" name="fabricant" value="<?php echo htmlspecialchars($lot['fabricant'] ?? ''); ?>">

            <label>Proveïdor</label>
            <input type="text" name="proveidor" value="<?php echo htmlspecialchars($lot['proveidor'] ?? ''); ?>">

            <label>Data de compra</label>
            <input type="date" name="data_compra" value="<?php echo htmlspecialchars($lot['data_compra'] ?? ''); ?>">

            <label>Preu unitari</label>
            <input type="number" step="0.0001" name="preu_unitari" value="<?php echo htmlspecialchars($lot['preu_unitari'] ?? ''); ?>">

            <button type="submit" class="btn btn-primary btn-full mt-2">Guardar canvis</button>
            <a class="btn btn-ghost btn-full mt-2" href="producte_lot_detall.php?id_lot=<?php echo (int) $lot['id_lot']; ?>">Cancel·lar</a>
        </form>
    </div>
</div>
</body>
</html>
