<?php
require_once __DIR__ . '/db.php';

$conn = db_connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_producte = filter_input(INPUT_POST, 'id_producte', FILTER_VALIDATE_INT);
    $tipus_raw = trim($_POST['tipus'] ?? '');
    $nom_comercial = trim($_POST['nom_comercial'] ?? '');
    $materia_activa_raw = trim($_POST['materia_activa'] ?? '');
    $concentracio_raw = trim($_POST['concentracio'] ?? '');
    $espectre_accio_raw = trim($_POST['espectre_accio'] ?? '');
    $cultius_autoritzats_raw = trim($_POST['cultius_autoritzats'] ?? '');
    $dosi_recomendada_raw = trim($_POST['dosi_recomendada'] ?? '');
    $dosi_maxima_raw = trim($_POST['dosi_maxima'] ?? '');
    $termini_seguretat_dies_raw = trim($_POST['termini_seguretat_dies'] ?? '');
    $classificacio_toxicologica_raw = trim($_POST['classificacio_toxicologica'] ?? '');
    $restriccions_usu_raw = trim($_POST['restriccions_usu'] ?? '');
    $compatible_integrada_raw = trim($_POST['compatible_integrada'] ?? '1');

    if (!$id_producte || $nom_comercial === '') {
        $conn->close();
        echo "<p style='color:red; font-weight:bold;'>Dades no vàlides per actualitzar el producte.</p>";
        exit;
    }

    $tipus = in_array($tipus_raw, ['Fitosanitari', 'Fertilitzant'], true) ? $tipus_raw : 'Fitosanitari';
    $materia_activa = $materia_activa_raw !== '' ? $materia_activa_raw : null;
    $concentracio = $concentracio_raw !== '' ? $concentracio_raw : null;
    $espectre_accio = $espectre_accio_raw !== '' ? $espectre_accio_raw : null;
    $cultius_autoritzats = $cultius_autoritzats_raw !== '' ? $cultius_autoritzats_raw : null;
    $dosi_recomendada = $dosi_recomendada_raw !== '' ? $dosi_recomendada_raw : null;
    $dosi_maxima = $dosi_maxima_raw !== '' ? $dosi_maxima_raw : null;
    $termini_seguretat_dies = ($termini_seguretat_dies_raw !== '' && ctype_digit($termini_seguretat_dies_raw)) ? (int) $termini_seguretat_dies_raw : null;
    $classificacio_toxicologica = $classificacio_toxicologica_raw !== '' ? $classificacio_toxicologica_raw : null;
    $restriccions_usu = $restriccions_usu_raw !== '' ? $restriccions_usu_raw : null;
    $compatible_integrada = ($compatible_integrada_raw === '0') ? 0 : 1;

    $stmt = $conn->prepare("
        UPDATE producte
        SET
            tipus = ?,
            nom_comercial = ?,
            materia_activa = ?,
            concentracio = ?,
            espectre_accio = ?,
            cultius_autoritzats = ?,
            dosi_recomendada = ?,
            dosi_maxima = ?,
            termini_seguretat_dies = ?,
            classificacio_toxicologica = ?,
            restriccions_usu = ?,
            compatible_integrada = ?
        WHERE id_producte = ?
    ");

    if ($stmt) {
        $stmt->bind_param(
            'ssssssssissii',
            $tipus,
            $nom_comercial,
            $materia_activa,
            $concentracio,
            $espectre_accio,
            $cultius_autoritzats,
            $dosi_recomendada,
            $dosi_maxima,
            $termini_seguretat_dies,
            $classificacio_toxicologica,
            $restriccions_usu,
            $compatible_integrada,
            $id_producte
        );

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header('Location: producte_detall.php?id_producte=' . $id_producte . '&msg=producte_actualitzat');
            exit;
        }
        $stmt->close();
    }

    $conn->close();
    echo "<p style='color:red; font-weight:bold;'>Error en actualitzar el producte.</p>";
    exit;
}

$id_producte = filter_input(INPUT_GET, 'id_producte', FILTER_VALIDATE_INT);
if (!$id_producte) {
    $conn->close();
    echo "<p style='color:red; font-weight:bold;'>ID de producte no vàlid.</p>";
    exit;
}

$stmt = $conn->prepare("
    SELECT
        id_producte,
        tipus,
        nom_comercial,
        materia_activa,
        concentracio,
        espectre_accio,
        cultius_autoritzats,
        dosi_recomendada,
        dosi_maxima,
        termini_seguretat_dies,
        classificacio_toxicologica,
        restriccions_usu,
        compatible_integrada
    FROM producte
    WHERE id_producte = ?
    LIMIT 1
");

$producte = null;
if ($stmt) {
    $stmt->bind_param('i', $id_producte);
    if ($stmt->execute()) {
        $res = $stmt->get_result();
        $producte = $res ? $res->fetch_assoc() : null;
    }
    $stmt->close();
}

$conn->close();

if (!$producte) {
    echo "<p style='color:red; font-weight:bold;'>Producte no trobat.</p>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Editar producte</title>
    <link rel="stylesheet" href="../HTML/styles.css">
</head>
<body>
<div class="page">
    <div class="page-header">
        <h1>Editar producte</h1>
    </div>

    <div class="panel">
        <form method="post" action="producte_editar.php">
            <input type="hidden" name="id_producte" value="<?php echo (int) $producte['id_producte']; ?>">

            <label>Tipus</label>
            <select name="tipus" required>
                <?php foreach (['Fitosanitari', 'Fertilitzant'] as $t): ?>
                    <option value="<?php echo $t; ?>" <?php echo (($producte['tipus'] ?? '') === $t) ? 'selected' : ''; ?>><?php echo $t; ?></option>
                <?php endforeach; ?>
            </select>

            <label>Nom comercial *</label>
            <input type="text" name="nom_comercial" value="<?php echo htmlspecialchars($producte['nom_comercial'] ?? ''); ?>" required>

            <label>Matèria activa</label>
            <input type="text" name="materia_activa" value="<?php echo htmlspecialchars($producte['materia_activa'] ?? ''); ?>">

            <label>Concentració</label>
            <input type="text" name="concentracio" value="<?php echo htmlspecialchars($producte['concentracio'] ?? ''); ?>">

            <label>Espectre d'acció</label>
            <textarea name="espectre_accio"><?php echo htmlspecialchars($producte['espectre_accio'] ?? ''); ?></textarea>

            <label>Cultius autoritzats</label>
            <textarea name="cultius_autoritzats"><?php echo htmlspecialchars($producte['cultius_autoritzats'] ?? ''); ?></textarea>

            <label>Dosi recomanada</label>
            <input type="text" name="dosi_recomendada" value="<?php echo htmlspecialchars($producte['dosi_recomendada'] ?? ''); ?>">

            <label>Dosi màxima</label>
            <input type="text" name="dosi_maxima" value="<?php echo htmlspecialchars($producte['dosi_maxima'] ?? ''); ?>">

            <label>Termini seguretat (dies)</label>
            <input type="number" name="termini_seguretat_dies" value="<?php echo htmlspecialchars($producte['termini_seguretat_dies'] ?? ''); ?>">

            <label>Classificació toxicològica</label>
            <input type="text" name="classificacio_toxicologica" value="<?php echo htmlspecialchars($producte['classificacio_toxicologica'] ?? ''); ?>">

            <label>Restriccions d'ús</label>
            <textarea name="restriccions_usu"><?php echo htmlspecialchars($producte['restriccions_usu'] ?? ''); ?></textarea>

            <label>Compatible integrada</label>
            <select name="compatible_integrada">
                <option value="1" <?php echo !empty($producte['compatible_integrada']) ? 'selected' : ''; ?>>Sí</option>
                <option value="0" <?php echo empty($producte['compatible_integrada']) ? 'selected' : ''; ?>>No</option>
            </select>

            <button type="submit" class="btn btn-primary btn-full mt-2">Guardar canvis</button>
            <a class="btn btn-ghost btn-full mt-2" href="producte_detall.php?id_producte=<?php echo (int) $producte['id_producte']; ?>">Cancel·lar</a>
        </form>
    </div>
</div>
</body>
</html>
