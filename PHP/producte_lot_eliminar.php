<?php
require_once __DIR__ . '/db.php';

$conn = db_connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_lot = filter_input(INPUT_POST, 'id_lot', FILTER_VALIDATE_INT);
    $confirmar = trim($_POST['confirmar'] ?? '');

    if (!$id_lot || $confirmar !== 'SI_ELIMINAR') {
        $conn->close();
        echo "<p style='color:red; font-weight:bold;'>Confirmació no vàlida per eliminar el lot.</p>";
        exit;
    }

    $moviments = 0;
    $q = $conn->prepare('SELECT COUNT(*) AS total FROM moviment_estoc WHERE id_lot = ?');
    if ($q) {
        $q->bind_param('i', $id_lot);
        if ($q->execute()) {
            $r = $q->get_result();
            $row = $r ? $r->fetch_assoc() : null;
            $moviments = (int) ($row['total'] ?? 0);
        }
        $q->close();
    }

    if ($moviments > 0) {
        $conn->close();
        header('Location: producte_lot_eliminar.php?id_lot=' . $id_lot . '&err=te_moviments');
        exit;
    }

    $stmt = $conn->prepare('DELETE FROM producte_lot WHERE id_lot = ?');
    if ($stmt) {
        $stmt->bind_param('i', $id_lot);
        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header('Location: consulta_productes.php?msg=lot_eliminat');
            exit;
        }
        $stmt->close();
    }

    $conn->close();
    header('Location: producte_lot_eliminar.php?id_lot=' . $id_lot . '&err=no_eliminat');
    exit;
}

$id_lot = filter_input(INPUT_GET, 'id_lot', FILTER_VALIDATE_INT);
$err = trim($_GET['err'] ?? '');
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
        pl.numero_lot,
        pl.quantitat_disponible,
        pl.unitat
    FROM producte_lot pl
    JOIN producte p ON p.id_producte = pl.id_producte
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

if (!$lot) {
    $conn->close();
    echo "<p style='color:red; font-weight:bold;'>Lot no trobat.</p>";
    exit;
}

$moviments = 0;
$q = $conn->prepare('SELECT COUNT(*) AS total FROM moviment_estoc WHERE id_lot = ?');
if ($q) {
    $q->bind_param('i', $id_lot);
    if ($q->execute()) {
        $r = $q->get_result();
        $row = $r ? $r->fetch_assoc() : null;
        $moviments = (int) ($row['total'] ?? 0);
    }
    $q->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Eliminar lot de producte</title>
    <link rel="stylesheet" href="../HTML/styles.css">
</head>
<body>
<div class="page">
    <div class="page-header">
        <h1>Eliminar lot de producte</h1>
    </div>

    <div class="panel">
        <?php if ($err === 'te_moviments'): ?>
            <p class="alert err">No es pot eliminar: hi ha moviments d'estoc vinculats a aquest lot.</p>
        <?php elseif ($err === 'no_eliminat'): ?>
            <p class="alert err">No s'ha pogut eliminar el lot.</p>
        <?php endif; ?>

        <p><strong>ID lot:</strong> <?php echo (int) $lot['id_lot']; ?></p>
        <p><strong>ID producte:</strong> <?php echo (int) $lot['id_producte']; ?></p>
        <p><strong>Producte:</strong> <?php echo htmlspecialchars($lot['nom_comercial'] ?? ''); ?></p>
        <p><strong>Número de lot:</strong> <?php echo htmlspecialchars($lot['numero_lot'] ?? ''); ?></p>
        <p><strong>Quantitat disponible:</strong> <?php echo htmlspecialchars($lot['quantitat_disponible'] ?? ''); ?></p>
        <p><strong>Unitat:</strong> <?php echo htmlspecialchars($lot['unitat'] ?? ''); ?></p>
        <p><strong>Moviments vinculats:</strong> <?php echo (int) $moviments; ?> (bloqueja eliminació)</p>

        <?php if ($moviments > 0): ?>
            <a class="btn btn-ghost btn-full mt-2" href="consulta_moviment_estoc.php?id_lot=<?php echo (int) $lot['id_lot']; ?>">Veure moviments vinculats</a>
        <?php endif; ?>

        <form method="post" action="producte_lot_eliminar.php" class="mt-2">
            <input type="hidden" name="id_lot" value="<?php echo (int) $lot['id_lot']; ?>">
            <label>Escriu `SI_ELIMINAR` per confirmar</label>
            <input type="text" name="confirmar" required>
            <button type="submit" class="btn btn-primary btn-full mt-2">Eliminar lot</button>
        </form>

        <a class="btn btn-ghost btn-full mt-2" href="producte_lot_detall.php?id_lot=<?php echo (int) $lot['id_lot']; ?>">Cancel·lar</a>
    </div>
</div>
</body>
</html>
