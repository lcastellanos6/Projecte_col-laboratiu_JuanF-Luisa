<?php
require_once __DIR__ . '/db.php';

$conn = db_connect();

$id_lot = filter_input(INPUT_GET, 'id_lot', FILTER_VALIDATE_INT);
$msg = trim($_GET['msg'] ?? '');
if (!$id_lot) {
    $conn->close();
    echo "<p style='color:red; font-weight:bold;'>ID de lot no vàlid.</p>";
    exit;
}

$stmt = $conn->prepare("
    SELECT
        pl.id_lot,
        pl.id_producte,
        p.nom_comercial,
        p.tipus,
        pl.numero_lot,
        pl.data_caducitat,
        pl.id_magatzem,
        m.nom AS magatzem_nom,
        m.ubicacio AS magatzem_ubicacio,
        pl.quantitat_disponible,
        pl.unitat,
        pl.fabricant,
        pl.proveidor,
        pl.data_compra,
        pl.preu_unitari
    FROM producte_lot pl
    JOIN producte p ON p.id_producte = pl.id_producte
    LEFT JOIN magatzem m ON m.id_magatzem = pl.id_magatzem
    WHERE pl.id_lot = ?
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
    <title>Detall de lot de producte</title>
    <link rel="stylesheet" href="../HTML/styles.css">
</head>
<body>
<div class="page">
    <div class="page-header">
        <h1>Detall de lot de producte</h1>
    </div>

    <div class="panel">
        <?php if ($msg === 'lot_actualitzat'): ?>
            <p class="alert ok">Lot actualitzat correctament.</p>
        <?php endif; ?>

        <p><strong>ID lot:</strong> <?php echo (int) $lot['id_lot']; ?></p>
        <p><strong>ID producte:</strong> <?php echo (int) $lot['id_producte']; ?></p>
        <p><strong>Producte:</strong> <?php echo htmlspecialchars($lot['nom_comercial'] ?? ''); ?></p>
        <p><strong>Tipus producte:</strong> <?php echo htmlspecialchars($lot['tipus'] ?? ''); ?></p>
        <p><strong>Número de lot:</strong> <?php echo htmlspecialchars($lot['numero_lot'] ?? ''); ?></p>
        <p><strong>Data de caducitat:</strong> <?php echo htmlspecialchars($lot['data_caducitat'] ?? ''); ?></p>
        <p><strong>ID magatzem:</strong> <?php echo htmlspecialchars($lot['id_magatzem'] ?? ''); ?></p>
        <p><strong>Magatzem:</strong> <?php echo htmlspecialchars($lot['magatzem_nom'] ?? ''); ?></p>
        <p><strong>Ubicació magatzem:</strong> <?php echo htmlspecialchars($lot['magatzem_ubicacio'] ?? ''); ?></p>
        <p><strong>Quantitat disponible:</strong> <?php echo htmlspecialchars($lot['quantitat_disponible'] ?? ''); ?></p>
        <p><strong>Unitat:</strong> <?php echo htmlspecialchars($lot['unitat'] ?? ''); ?></p>
        <p><strong>Fabricant:</strong> <?php echo htmlspecialchars($lot['fabricant'] ?? ''); ?></p>
        <p><strong>Proveïdor:</strong> <?php echo htmlspecialchars($lot['proveidor'] ?? ''); ?></p>
        <p><strong>Data de compra:</strong> <?php echo htmlspecialchars($lot['data_compra'] ?? ''); ?></p>
        <p><strong>Preu unitari:</strong> <?php echo htmlspecialchars($lot['preu_unitari'] ?? ''); ?></p>

        <a class="btn btn-primary btn-full mt-2" href="producte_lot_editar.php?id_lot=<?php echo (int) $lot['id_lot']; ?>">Editar lot</a>
        <a class="btn btn-ghost btn-full mt-2" href="producte_lot_eliminar.php?id_lot=<?php echo (int) $lot['id_lot']; ?>">Eliminar lot</a>
        <a class="btn btn-ghost btn-full mt-2" href="consulta_productes.php">Tornar a consulta productes</a>
    </div>
</div>
</body>
</html>
