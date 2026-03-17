<?php
require_once __DIR__ . '/db.php';

$conn = db_connect();

$id_aplicacio = filter_input(INPUT_GET, 'id_aplicacio', FILTER_VALIDATE_INT);
$msg = trim($_GET['msg'] ?? '');
if (!$id_aplicacio) {
    $conn->close();
    echo "<p style='color:red; font-weight:bold;'>ID d'aplicació no vàlid.</p>";
    exit;
}

$sql = "
    SELECT
        a.id_aplicacio,
        a.id_pla,
        pt.nom AS pla_nom,
        a.data,
        a.hora_inici,
        a.hora_fi,
        a.metode,
        a.condicions_ambientals,
        a.id_operari,
        o.nom AS operari_nom,
        a.id_equip,
        e.tipus AS equip_tipus,
        a.observacions
    FROM aplicacio a
    LEFT JOIN pla_tractament pt ON pt.id_pla = a.id_pla
    LEFT JOIN operari o ON o.id_operari = a.id_operari
    LEFT JOIN equip e ON e.id_equip = a.id_equip
    WHERE a.id_aplicacio = ?
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
    <title>Detall d'aplicació</title>
    <link rel="stylesheet" href="../HTML/styles.css">
</head>
<body>
<div class="page">
    <div class="page-header">
        <h1>Detall d'aplicació</h1>
    </div>

    <div class="panel">
        <?php if ($msg === 'aplicacio_actualitzada'): ?>
            <p class="alert ok">Aplicació actualitzada correctament.</p>
        <?php endif; ?>

        <p><strong>ID aplicació:</strong> <?php echo (int) $aplicacio['id_aplicacio']; ?></p>
        <p><strong>Data:</strong> <?php echo htmlspecialchars($aplicacio['data'] ?? ''); ?></p>
        <p><strong>ID pla:</strong> <?php echo htmlspecialchars($aplicacio['id_pla'] ?? ''); ?></p>
        <p><strong>Nom pla:</strong> <?php echo htmlspecialchars($aplicacio['pla_nom'] ?? ''); ?></p>
        <p><strong>Mètode:</strong> <?php echo htmlspecialchars($aplicacio['metode'] ?? ''); ?></p>
        <p><strong>Hora inici:</strong> <?php echo htmlspecialchars($aplicacio['hora_inici'] ?? ''); ?></p>
        <p><strong>Hora fi:</strong> <?php echo htmlspecialchars($aplicacio['hora_fi'] ?? ''); ?></p>
        <p><strong>ID operari:</strong> <?php echo htmlspecialchars($aplicacio['id_operari'] ?? ''); ?></p>
        <p><strong>Operari:</strong> <?php echo htmlspecialchars($aplicacio['operari_nom'] ?? ''); ?></p>
        <p><strong>ID equip:</strong> <?php echo htmlspecialchars($aplicacio['id_equip'] ?? ''); ?></p>
        <p><strong>Tipus equip:</strong> <?php echo htmlspecialchars($aplicacio['equip_tipus'] ?? ''); ?></p>
        <p><strong>Condicions ambientals:</strong> <?php echo htmlspecialchars($aplicacio['condicions_ambientals'] ?? ''); ?></p>
        <p><strong>Observacions:</strong> <?php echo htmlspecialchars($aplicacio['observacions'] ?? ''); ?></p>

        <a class="btn btn-primary btn-full mt-2" href="aplicacio_editar.php?id_aplicacio=<?php echo (int) $aplicacio['id_aplicacio']; ?>">Editar aplicació</a>
        <a class="btn btn-ghost btn-full mt-2" href="aplicacio_eliminar.php?id_aplicacio=<?php echo (int) $aplicacio['id_aplicacio']; ?>">Eliminar aplicació</a>
        <a class="btn btn-ghost btn-full mt-2" href="consulta_aplicacions.php">Tornar a consulta aplicacions</a>
    </div>
</div>
</body>
</html>
