<?php
require_once __DIR__ . '/db.php';
$conn = db_connect();

$id_especie = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id_especie) {
    $conn->close();
    echo "<p style='color:red; font-weight:bold;'>ID d'espècie no vàlid.</p>";
    exit;
}

$stmt = $conn->prepare('SELECT id_especie, nom_cientific, nom_comu FROM especie WHERE id_especie = ?');
$stmt->bind_param('i', $id_especie);
$stmt->execute();
$res = $stmt->get_result();
$especie = $res ? $res->fetch_assoc() : null;
$stmt->close();
$conn->close();

if (!$especie) {
    echo "<p style='color:red; font-weight:bold;'>Espècie no trobada.</p>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Detall d'espècie</title>
    <link rel="stylesheet" href="../HTML/styles.css">
</head>
<body>
<div class="page">
    <div class="page-header">
        <h1>Detall d'espècie</h1>
    </div>

    <div class="panel">
        <p><strong>Nom científic:</strong> <?php echo htmlspecialchars($especie['nom_cientific'] ?? ''); ?></p>
        <p><strong>Nom comú:</strong> <?php echo htmlspecialchars($especie['nom_comu'] ?? ''); ?></p>

        <a class="btn btn-primary btn-full mt-2" href="especie_editar.php?id=<?php echo (int)$especie['id_especie']; ?>">Editar espècie</a>
        <a class="btn btn-ghost btn-full mt-2" href="consulta_cultius_varietats.php">Tornar a la consulta</a>
    </div>
</div>
</body>
</html>
