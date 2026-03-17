<?php
require_once __DIR__ . '/db.php';

$conn = db_connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_producte = filter_input(INPUT_POST, 'id_producte', FILTER_VALIDATE_INT);
    $confirmar = trim($_POST['confirmar'] ?? '');

    if (!$id_producte || $confirmar !== 'SI_ELIMINAR') {
        $conn->close();
        echo "<p style='color:red; font-weight:bold;'>Confirmació no vàlida per eliminar el producte.</p>";
        exit;
    }

    $dependents = 0;
    $q = $conn->prepare('SELECT COUNT(*) AS total FROM producte_lot WHERE id_producte = ?');
    if ($q) {
        $q->bind_param('i', $id_producte);
        if ($q->execute()) {
            $r = $q->get_result();
            $row = $r ? $r->fetch_assoc() : null;
            $dependents += (int) ($row['total'] ?? 0);
        }
        $q->close();
    }
    $q = $conn->prepare('SELECT COUNT(*) AS total FROM aplicacio_producte WHERE id_producte = ?');
    if ($q) {
        $q->bind_param('i', $id_producte);
        if ($q->execute()) {
            $r = $q->get_result();
            $row = $r ? $r->fetch_assoc() : null;
            $dependents += (int) ($row['total'] ?? 0);
        }
        $q->close();
    }
    $q = $conn->prepare('SELECT COUNT(*) AS total FROM pla_producte WHERE id_producte = ?');
    if ($q) {
        $q->bind_param('i', $id_producte);
        if ($q->execute()) {
            $r = $q->get_result();
            $row = $r ? $r->fetch_assoc() : null;
            $dependents += (int) ($row['total'] ?? 0);
        }
        $q->close();
    }

    if ($dependents > 0) {
        $conn->close();
        header('Location: producte_eliminar.php?id_producte=' . $id_producte . '&err=te_dependents');
        exit;
    }

    $stmt = $conn->prepare('DELETE FROM producte WHERE id_producte = ?');
    if ($stmt) {
        $stmt->bind_param('i', $id_producte);
        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header('Location: consulta_productes.php?msg=producte_eliminat');
            exit;
        }
        $stmt->close();
    }

    $conn->close();
    header('Location: producte_eliminar.php?id_producte=' . $id_producte . '&err=no_eliminat');
    exit;
}

$id_producte = filter_input(INPUT_GET, 'id_producte', FILTER_VALIDATE_INT);
$err = trim($_GET['err'] ?? '');
if (!$id_producte) {
    $conn->close();
    echo "<p style='color:red; font-weight:bold;'>ID de producte no vàlid.</p>";
    exit;
}

$stmt = $conn->prepare("
    SELECT id_producte, tipus, nom_comercial
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

if (!$producte) {
    $conn->close();
    echo "<p style='color:red; font-weight:bold;'>Producte no trobat.</p>";
    exit;
}

$counts = ['producte_lot' => 0, 'aplicacio_producte' => 0, 'pla_producte' => 0];
foreach (array_keys($counts) as $taula) {
    $q = $conn->prepare("SELECT COUNT(*) AS total FROM {$taula} WHERE id_producte = ?");
    if ($q) {
        $q->bind_param('i', $id_producte);
        if ($q->execute()) {
            $r = $q->get_result();
            $row = $r ? $r->fetch_assoc() : null;
            $counts[$taula] = (int) ($row['total'] ?? 0);
        }
        $q->close();
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Eliminar producte</title>
    <link rel="stylesheet" href="../HTML/styles.css">
</head>
<body>
<div class="page">
    <div class="page-header">
        <h1>Eliminar producte</h1>
    </div>

    <div class="panel">
        <?php if ($err === 'te_dependents'): ?>
            <p class="alert err">No es pot eliminar: el producte té registres dependents.</p>
        <?php elseif ($err === 'no_eliminat'): ?>
            <p class="alert err">No s'ha pogut eliminar el producte.</p>
        <?php endif; ?>

        <p><strong>ID producte:</strong> <?php echo (int) $producte['id_producte']; ?></p>
        <p><strong>Tipus:</strong> <?php echo htmlspecialchars($producte['tipus'] ?? ''); ?></p>
        <p><strong>Nom comercial:</strong> <?php echo htmlspecialchars($producte['nom_comercial'] ?? ''); ?></p>

        <p><strong>Registres relacionats:</strong></p>
        <p>- `producte_lot`: <?php echo (int) $counts['producte_lot']; ?> (bloqueja eliminació)</p>
        <p>- `aplicacio_producte`: <?php echo (int) $counts['aplicacio_producte']; ?> (bloqueja eliminació)</p>
        <p>- `pla_producte`: <?php echo (int) $counts['pla_producte']; ?> (bloqueja eliminació)</p>

        <form method="post" action="producte_eliminar.php" class="mt-2">
            <input type="hidden" name="id_producte" value="<?php echo (int) $producte['id_producte']; ?>">
            <label>Escriu `SI_ELIMINAR` per confirmar</label>
            <input type="text" name="confirmar" required>
            <button type="submit" class="btn btn-primary btn-full mt-2">Eliminar producte</button>
        </form>

        <a class="btn btn-ghost btn-full mt-2" href="producte_detall.php?id_producte=<?php echo (int) $producte['id_producte']; ?>">Cancel·lar</a>
    </div>
</div>
</body>
</html>
