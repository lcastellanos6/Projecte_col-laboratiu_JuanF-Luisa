<?php
require_once __DIR__ . '/db.php';

$conn = db_connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_mov = filter_input(INPUT_POST, 'id_mov', FILTER_VALIDATE_INT);
    $confirmar = trim($_POST['confirmar'] ?? '');

    if (!$id_mov || $confirmar !== 'SI_ELIMINAR') {
        $conn->close();
        echo "<p style='color:red; font-weight:bold;'>Confirmació no vàlida per eliminar el moviment.</p>";
        exit;
    }

    $stmt = $conn->prepare('DELETE FROM moviment_estoc WHERE id_mov = ?');
    if ($stmt) {
        $stmt->bind_param('i', $id_mov);
        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header('Location: consulta_moviment_estoc.php?msg=moviment_eliminat');
            exit;
        }
        $stmt->close();
    }

    $conn->close();
    header('Location: moviment_estoc_eliminar.php?id_mov=' . $id_mov . '&err=no_eliminat');
    exit;
}

$id_mov = filter_input(INPUT_GET, 'id_mov', FILTER_VALIDATE_INT);
$err = trim($_GET['err'] ?? '');
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
        p.nom_comercial,
        me.data,
        me.quantitat,
        me.motiu,
        me.id_aplicacio,
        me.observacions
    FROM moviment_estoc me
    JOIN producte_lot pl ON pl.id_lot = me.id_lot
    JOIN producte p ON p.id_producte = pl.id_producte
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
    <title>Eliminar moviment d'estoc</title>
    <link rel="stylesheet" href="../HTML/styles.css">
</head>
<body>
<div class="page">
    <div class="page-header">
        <h1>Eliminar moviment d'estoc</h1>
    </div>

    <div class="panel">
        <?php if ($err === 'no_eliminat'): ?>
            <p class="alert err">No s'ha pogut eliminar el moviment.</p>
        <?php endif; ?>

        <p><strong>ID moviment:</strong> <?php echo (int) $moviment['id_mov']; ?></p>
        <p><strong>Data:</strong> <?php echo htmlspecialchars($moviment['data'] ?? ''); ?></p>
        <p><strong>ID lot:</strong> <?php echo (int) $moviment['id_lot']; ?></p>
        <p><strong>Núm. lot:</strong> <?php echo htmlspecialchars($moviment['numero_lot'] ?? ''); ?></p>
        <p><strong>Producte:</strong> <?php echo htmlspecialchars($moviment['nom_comercial'] ?? ''); ?></p>
        <p><strong>Quantitat:</strong> <?php echo htmlspecialchars($moviment['quantitat'] ?? ''); ?></p>
        <p><strong>Motiu:</strong> <?php echo htmlspecialchars($moviment['motiu'] ?? ''); ?></p>
        <p><strong>ID aplicació:</strong> <?php echo htmlspecialchars($moviment['id_aplicacio'] ?? ''); ?></p>
        <p><strong>Observacions:</strong> <?php echo htmlspecialchars($moviment['observacions'] ?? ''); ?></p>

        <form method="post" action="moviment_estoc_eliminar.php" class="mt-2">
            <input type="hidden" name="id_mov" value="<?php echo (int) $moviment['id_mov']; ?>">
            <label>Escriu `SI_ELIMINAR` per confirmar</label>
            <input type="text" name="confirmar" required>
            <button type="submit" class="btn btn-primary btn-full mt-2">Eliminar moviment</button>
        </form>

        <a class="btn btn-ghost btn-full mt-2" href="moviment_estoc_detall.php?id_mov=<?php echo (int) $moviment['id_mov']; ?>">Cancel·lar</a>
    </div>
</div>
</body>
</html>
