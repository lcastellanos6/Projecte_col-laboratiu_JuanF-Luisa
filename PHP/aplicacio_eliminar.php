<?php
require_once __DIR__ . '/db.php';

$conn = db_connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_aplicacio = filter_input(INPUT_POST, 'id_aplicacio', FILTER_VALIDATE_INT);
    $confirmar = trim($_POST['confirmar'] ?? '');

    if (!$id_aplicacio || $confirmar !== 'SI_ELIMINAR') {
        $conn->close();
        echo "<p style='color:red; font-weight:bold;'>Confirmació no vàlida per eliminar l'aplicació.</p>";
        exit;
    }

    $stmtMov = $conn->prepare('SELECT COUNT(*) AS total FROM moviment_estoc WHERE id_aplicacio = ?');
    $moviments = 0;
    if ($stmtMov) {
        $stmtMov->bind_param('i', $id_aplicacio);
        if ($stmtMov->execute()) {
            $resMov = $stmtMov->get_result();
            $rowMov = $resMov ? $resMov->fetch_assoc() : null;
            $moviments = (int) ($rowMov['total'] ?? 0);
        }
        $stmtMov->close();
    }

    if ($moviments > 0) {
        $conn->close();
        header('Location: aplicacio_eliminar.php?id_aplicacio=' . $id_aplicacio . '&err=te_moviments');
        exit;
    }

    $stmtDel = $conn->prepare('DELETE FROM aplicacio WHERE id_aplicacio = ?');
    if ($stmtDel) {
        $stmtDel->bind_param('i', $id_aplicacio);
        if ($stmtDel->execute()) {
            $stmtDel->close();
            $conn->close();
            header('Location: consulta_aplicacions.php?msg=aplicacio_eliminada');
            exit;
        }
        $stmtDel->close();
    }

    $conn->close();
    header('Location: aplicacio_eliminar.php?id_aplicacio=' . $id_aplicacio . '&err=no_eliminada');
    exit;
}

$id_aplicacio = filter_input(INPUT_GET, 'id_aplicacio', FILTER_VALIDATE_INT);
$err = trim($_GET['err'] ?? '');
if (!$id_aplicacio) {
    $conn->close();
    echo "<p style='color:red; font-weight:bold;'>ID d'aplicació no vàlid.</p>";
    exit;
}

$stmt = $conn->prepare("
    SELECT
        a.id_aplicacio,
        a.data,
        a.metode,
        a.id_pla,
        pt.nom AS pla_nom
    FROM aplicacio a
    LEFT JOIN pla_tractament pt ON pt.id_pla = a.id_pla
    WHERE a.id_aplicacio = ?
    LIMIT 1
");
$aplicacio = null;
if ($stmt) {
    $stmt->bind_param('i', $id_aplicacio);
    if ($stmt->execute()) {
        $res = $stmt->get_result();
        $aplicacio = $res ? $res->fetch_assoc() : null;
    }
    $stmt->close();
}

if (!$aplicacio) {
    $conn->close();
    echo "<p style='color:red; font-weight:bold;'>Aplicació no trobada.</p>";
    exit;
}

$counts = [
    'aplicacio_producte' => 0,
    'aplicacio_fila' => 0,
    'moviment_estoc' => 0,
];

$q1 = $conn->prepare('SELECT COUNT(*) AS total FROM aplicacio_producte WHERE id_aplicacio = ?');
if ($q1) {
    $q1->bind_param('i', $id_aplicacio);
    if ($q1->execute()) {
        $r1 = $q1->get_result();
        $row1 = $r1 ? $r1->fetch_assoc() : null;
        $counts['aplicacio_producte'] = (int) ($row1['total'] ?? 0);
    }
    $q1->close();
}

$q2 = $conn->prepare('SELECT COUNT(*) AS total FROM aplicacio_fila WHERE id_aplicacio = ?');
if ($q2) {
    $q2->bind_param('i', $id_aplicacio);
    if ($q2->execute()) {
        $r2 = $q2->get_result();
        $row2 = $r2 ? $r2->fetch_assoc() : null;
        $counts['aplicacio_fila'] = (int) ($row2['total'] ?? 0);
    }
    $q2->close();
}

$q3 = $conn->prepare('SELECT COUNT(*) AS total FROM moviment_estoc WHERE id_aplicacio = ?');
if ($q3) {
    $q3->bind_param('i', $id_aplicacio);
    if ($q3->execute()) {
        $r3 = $q3->get_result();
        $row3 = $r3 ? $r3->fetch_assoc() : null;
        $counts['moviment_estoc'] = (int) ($row3['total'] ?? 0);
    }
    $q3->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Eliminar aplicació</title>
    <link rel="stylesheet" href="../HTML/styles.css">
</head>
<body>
<div class="page">
    <div class="page-header">
        <h1>Eliminar aplicació</h1>
    </div>

    <div class="panel">
        <?php if ($err === 'te_moviments'): ?>
            <p class="alert err">No es pot eliminar: hi ha moviments d'estoc vinculats a aquesta aplicació.</p>
        <?php elseif ($err === 'no_eliminada'): ?>
            <p class="alert err">No s'ha pogut eliminar l'aplicació.</p>
        <?php endif; ?>

        <p><strong>ID aplicació:</strong> <?php echo (int) $aplicacio['id_aplicacio']; ?></p>
        <p><strong>Data:</strong> <?php echo htmlspecialchars($aplicacio['data'] ?? ''); ?></p>
        <p><strong>Mètode:</strong> <?php echo htmlspecialchars($aplicacio['metode'] ?? ''); ?></p>
        <p><strong>ID pla:</strong> <?php echo htmlspecialchars($aplicacio['id_pla'] ?? ''); ?></p>
        <p><strong>Nom pla:</strong> <?php echo htmlspecialchars($aplicacio['pla_nom'] ?? ''); ?></p>

        <p><strong>Registres relacionats:</strong></p>
        <p>- `aplicacio_producte`: <?php echo (int) $counts['aplicacio_producte']; ?> (s'eliminaran en cascada)</p>
        <p>- `aplicacio_fila`: <?php echo (int) $counts['aplicacio_fila']; ?> (s'eliminaran en cascada)</p>
        <p>- `moviment_estoc`: <?php echo (int) $counts['moviment_estoc']; ?> (bloqueja eliminació)</p>

        <?php if ((int) $counts['moviment_estoc'] > 0): ?>
            <a class="btn btn-ghost btn-full mt-2" href="consulta_moviment_estoc.php?id_aplicacio=<?php echo (int) $aplicacio['id_aplicacio']; ?>">Veure moviments vinculats</a>
        <?php endif; ?>

        <form method="post" action="aplicacio_eliminar.php" class="mt-2">
            <input type="hidden" name="id_aplicacio" value="<?php echo (int) $aplicacio['id_aplicacio']; ?>">
            <label>Escriu `SI_ELIMINAR` per confirmar</label>
            <input type="text" name="confirmar" required>
            <button type="submit" class="btn btn-primary btn-full mt-2">Eliminar aplicació</button>
        </form>

        <a class="btn btn-ghost btn-full mt-2" href="aplicacio_detall.php?id_aplicacio=<?php echo (int) $aplicacio['id_aplicacio']; ?>">Cancel·lar</a>
    </div>
</div>
</body>
</html>
