<?php
require_once __DIR__ . '/db.php';

$conn = db_connect();

$id_producte = filter_input(INPUT_GET, 'id_producte', FILTER_VALIDATE_INT);
$msg = trim($_GET['msg'] ?? '');
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
    <title>Detall de producte</title>
    <link rel="stylesheet" href="../HTML/styles.css">
</head>
<body>
<div class="page">
    <div class="page-header">
        <h1>Detall de producte</h1>
    </div>

    <div class="panel">
        <?php if ($msg === 'producte_actualitzat'): ?>
            <p class="alert ok">Producte actualitzat correctament.</p>
        <?php endif; ?>

        <p><strong>ID producte:</strong> <?php echo (int) $producte['id_producte']; ?></p>
        <p><strong>Tipus:</strong> <?php echo htmlspecialchars($producte['tipus'] ?? ''); ?></p>
        <p><strong>Nom comercial:</strong> <?php echo htmlspecialchars($producte['nom_comercial'] ?? ''); ?></p>
        <p><strong>Matèria activa:</strong> <?php echo htmlspecialchars($producte['materia_activa'] ?? ''); ?></p>
        <p><strong>Concentració:</strong> <?php echo htmlspecialchars($producte['concentracio'] ?? ''); ?></p>
        <p><strong>Espectre d'acció:</strong> <?php echo htmlspecialchars($producte['espectre_accio'] ?? ''); ?></p>
        <p><strong>Cultius autoritzats:</strong> <?php echo htmlspecialchars($producte['cultius_autoritzats'] ?? ''); ?></p>
        <p><strong>Dosi recomanada:</strong> <?php echo htmlspecialchars($producte['dosi_recomendada'] ?? ''); ?></p>
        <p><strong>Dosi màxima:</strong> <?php echo htmlspecialchars($producte['dosi_maxima'] ?? ''); ?></p>
        <p><strong>Termini seguretat (dies):</strong> <?php echo htmlspecialchars($producte['termini_seguretat_dies'] ?? ''); ?></p>
        <p><strong>Classificació toxicològica:</strong> <?php echo htmlspecialchars($producte['classificacio_toxicologica'] ?? ''); ?></p>
        <p><strong>Restriccions d'ús:</strong> <?php echo htmlspecialchars($producte['restriccions_usu'] ?? ''); ?></p>
        <p><strong>Compatible integrada:</strong> <?php echo !empty($producte['compatible_integrada']) ? 'Sí' : 'No'; ?></p>

        <a class="btn btn-primary btn-full mt-2" href="producte_editar.php?id_producte=<?php echo (int) $producte['id_producte']; ?>">Editar producte</a>
        <a class="btn btn-ghost btn-full mt-2" href="producte_eliminar.php?id_producte=<?php echo (int) $producte['id_producte']; ?>">Eliminar producte</a>
        <a class="btn btn-ghost btn-full mt-2" href="consulta_productes.php">Tornar a consulta productes</a>
    </div>
</div>
</body>
</html>
