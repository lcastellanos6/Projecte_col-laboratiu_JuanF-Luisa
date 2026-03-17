<?php
require_once __DIR__ . '/db.php';

$conn = db_connect();

$id_mov = filter_input(INPUT_GET, 'id_mov', FILTER_VALIDATE_INT);
$msg = trim($_GET['msg'] ?? '');
if (!$id_mov) {
    $conn->close();
    echo "<p style='color:red; font-weight:bold;'>ID de moviment no vàlid.</p>";
    exit;
}

$sql = "
    SELECT
        me.id_mov,
        me.id_lot,
        pl.numero_lot,
        pl.id_producte,
        p.nom_comercial,
        me.data,
        me.quantitat,
        me.motiu,
        me.id_aplicacio,
        a.data AS data_aplicacio,
        a.metode AS metode_aplicacio,
        me.observacions
    FROM moviment_estoc me
    JOIN producte_lot pl ON pl.id_lot = me.id_lot
    JOIN producte p ON p.id_producte = pl.id_producte
    LEFT JOIN aplicacio a ON a.id_aplicacio = me.id_aplicacio
    WHERE me.id_mov = ?
    LIMIT 1
";

$stmt = $conn->prepare($sql);
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
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Detall de moviment d'estoc</title>
    <link rel="stylesheet" href="../HTML/styles.css">
</head>
<body>
<div class="page">
    <div class="page-header">
        <h1>Detall de moviment d'estoc</h1>
    </div>

    <div class="panel">
        <?php if ($msg === 'moviment_actualitzat'): ?>
            <p class="alert ok">Moviment actualitzat correctament.</p>
        <?php endif; ?>

        <p><strong>ID moviment:</strong> <?php echo (int) $moviment['id_mov']; ?></p>
        <p><strong>Data:</strong> <?php echo htmlspecialchars($moviment['data'] ?? ''); ?></p>
        <p><strong>ID lot:</strong> <?php echo (int) $moviment['id_lot']; ?></p>
        <p><strong>Núm. lot:</strong> <?php echo htmlspecialchars($moviment['numero_lot'] ?? ''); ?></p>
        <p><strong>ID producte:</strong> <?php echo (int) $moviment['id_producte']; ?></p>
        <p><strong>Producte:</strong> <?php echo htmlspecialchars($moviment['nom_comercial'] ?? ''); ?></p>
        <p><strong>Quantitat:</strong> <?php echo htmlspecialchars($moviment['quantitat'] ?? ''); ?></p>
        <p><strong>Motiu:</strong> <?php echo htmlspecialchars($moviment['motiu'] ?? ''); ?></p>
        <p><strong>ID aplicació:</strong> <?php echo htmlspecialchars($moviment['id_aplicacio'] ?? ''); ?></p>
        <p><strong>Data aplicació:</strong> <?php echo htmlspecialchars($moviment['data_aplicacio'] ?? ''); ?></p>
        <p><strong>Mètode aplicació:</strong> <?php echo htmlspecialchars($moviment['metode_aplicacio'] ?? ''); ?></p>
        <p><strong>Observacions:</strong> <?php echo htmlspecialchars($moviment['observacions'] ?? ''); ?></p>

        <a class="btn btn-primary btn-full mt-2" href="moviment_estoc_editar.php?id_mov=<?php echo (int) $moviment['id_mov']; ?>">Editar moviment</a>
        <a class="btn btn-ghost btn-full mt-2" href="moviment_estoc_eliminar.php?id_mov=<?php echo (int) $moviment['id_mov']; ?>">Eliminar moviment</a>
        <a class="btn btn-ghost btn-full mt-2" href="consulta_moviment_estoc.php">Tornar a consulta moviments</a>
    </div>
</div>
</body>
</html>
