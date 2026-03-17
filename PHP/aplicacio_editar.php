<?php
require_once __DIR__ . '/db.php';

$conn = db_connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_aplicacio = filter_input(INPUT_POST, 'id_aplicacio', FILTER_VALIDATE_INT);
    $data = trim($_POST['data'] ?? '');
    $id_pla_raw = trim($_POST['id_pla'] ?? '');
    $hora_inici_raw = trim($_POST['hora_inici'] ?? '');
    $hora_fi_raw = trim($_POST['hora_fi'] ?? '');
    $metode_raw = trim($_POST['metode'] ?? '');
    $condicions_ambientals_raw = trim($_POST['condicions_ambientals'] ?? '');
    $id_operari_raw = trim($_POST['id_operari'] ?? '');
    $id_equip_raw = trim($_POST['id_equip'] ?? '');
    $observacions_raw = trim($_POST['observacions'] ?? '');

    if (!$id_aplicacio || $data === '') {
        $conn->close();
        echo "<p style='color:red; font-weight:bold;'>Dades no vàlides per actualitzar l'aplicació.</p>";
        exit;
    }

    $id_pla = ($id_pla_raw !== '' && ctype_digit($id_pla_raw)) ? (int) $id_pla_raw : null;
    $hora_inici = $hora_inici_raw !== '' ? $hora_inici_raw : null;
    $hora_fi = $hora_fi_raw !== '' ? $hora_fi_raw : null;
    $metodes_permesos = ['Fertirrigacio', 'Foliar', 'Sòl', 'Altres'];
    $metode = in_array($metode_raw, $metodes_permesos, true) ? $metode_raw : null;
    $condicions_ambientals = $condicions_ambientals_raw !== '' ? $condicions_ambientals_raw : null;
    $id_operari = ($id_operari_raw !== '' && ctype_digit($id_operari_raw)) ? (int) $id_operari_raw : null;
    $id_equip = ($id_equip_raw !== '' && ctype_digit($id_equip_raw)) ? (int) $id_equip_raw : null;
    $observacions = $observacions_raw !== '' ? $observacions_raw : null;

    $sql = "
        UPDATE aplicacio
        SET
            id_pla = ?,
            data = ?,
            hora_inici = ?,
            hora_fi = ?,
            metode = ?,
            condicions_ambientals = ?,
            id_operari = ?,
            id_equip = ?,
            observacions = ?
        WHERE id_aplicacio = ?
    ";

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param(
            'isssssiisi',
            $id_pla,
            $data,
            $hora_inici,
            $hora_fi,
            $metode,
            $condicions_ambientals,
            $id_operari,
            $id_equip,
            $observacions,
            $id_aplicacio
        );

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header('Location: aplicacio_detall.php?id_aplicacio=' . $id_aplicacio . '&msg=aplicacio_actualitzada');
            exit;
        }
        $stmt->close();
    }

    $conn->close();
    echo "<p style='color:red; font-weight:bold;'>Error en actualitzar l'aplicació.</p>";
    exit;
}

$id_aplicacio = filter_input(INPUT_GET, 'id_aplicacio', FILTER_VALIDATE_INT);
if (!$id_aplicacio) {
    $conn->close();
    echo "<p style='color:red; font-weight:bold;'>ID d'aplicació no vàlid.</p>";
    exit;
}

$sql = "
    SELECT
        id_aplicacio,
        id_pla,
        data,
        hora_inici,
        hora_fi,
        metode,
        condicions_ambientals,
        id_operari,
        id_equip,
        observacions
    FROM aplicacio
    WHERE id_aplicacio = ?
    LIMIT 1
";

$stmt = $conn->prepare($sql);
$aplicacio = null;
if ($stmt) {
    $stmt->bind_param('i', $id_aplicacio);
    if ($stmt->execute()) {
        $res = $stmt->get_result();
        $aplicacio = $res ? $res->fetch_assoc() : null;
    }
    $stmt->close();
}
$conn->close();

if (!$aplicacio) {
    echo "<p style='color:red; font-weight:bold;'>Aplicació no trobada.</p>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Editar aplicació</title>
    <link rel="stylesheet" href="../HTML/styles.css">
</head>
<body>
<div class="page">
    <div class="page-header">
        <h1>Editar aplicació</h1>
    </div>

    <div class="panel">
        <form method="post" action="aplicacio_editar.php">
            <input type="hidden" name="id_aplicacio" value="<?php echo (int) $aplicacio['id_aplicacio']; ?>">

            <label>Data *</label>
            <input type="date" name="data" value="<?php echo htmlspecialchars($aplicacio['data'] ?? ''); ?>" required>

            <label>ID Pla</label>
            <input type="number" name="id_pla" value="<?php echo htmlspecialchars($aplicacio['id_pla'] ?? ''); ?>">

            <label>Hora inici</label>
            <input type="time" name="hora_inici" value="<?php echo htmlspecialchars($aplicacio['hora_inici'] ?? ''); ?>">

            <label>Hora fi</label>
            <input type="time" name="hora_fi" value="<?php echo htmlspecialchars($aplicacio['hora_fi'] ?? ''); ?>">

            <label>Mètode</label>
            <select name="metode">
                <option value="">(Sense mètode)</option>
                <?php foreach (['Fertirrigacio', 'Foliar', 'Sòl', 'Altres'] as $m): ?>
                    <option value="<?php echo $m; ?>" <?php echo (($aplicacio['metode'] ?? '') === $m) ? 'selected' : ''; ?>><?php echo $m; ?></option>
                <?php endforeach; ?>
            </select>

            <label>Condicions ambientals</label>
            <textarea name="condicions_ambientals"><?php echo htmlspecialchars($aplicacio['condicions_ambientals'] ?? ''); ?></textarea>

            <label>ID Operari</label>
            <input type="number" name="id_operari" value="<?php echo htmlspecialchars($aplicacio['id_operari'] ?? ''); ?>">

            <label>ID Equip</label>
            <input type="number" name="id_equip" value="<?php echo htmlspecialchars($aplicacio['id_equip'] ?? ''); ?>">

            <label>Observacions</label>
            <textarea name="observacions"><?php echo htmlspecialchars($aplicacio['observacions'] ?? ''); ?></textarea>

            <button type="submit" class="btn btn-primary btn-full mt-2">Guardar canvis</button>
            <a class="btn btn-ghost btn-full mt-2" href="aplicacio_detall.php?id_aplicacio=<?php echo (int) $aplicacio['id_aplicacio']; ?>">Cancel·lar</a>
        </form>
    </div>
</div>
</body>
</html>
