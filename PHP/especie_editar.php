<?php
require_once __DIR__ . '/db.php';
$conn = db_connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_especie = filter_input(INPUT_POST, 'id_especie', FILTER_VALIDATE_INT);
    $nom_cientific = trim($_POST['nom_cientific'] ?? '');
    $nom_comu = trim($_POST['nom_comu'] ?? '');

    if (!$id_especie || $nom_cientific === '' || $nom_comu === '') {
        $conn->close();
        header('Location: consulta_cultius_varietats.php?err=especie_actualitzar');
        exit;
    }

    $stmt = $conn->prepare('UPDATE especie SET nom_cientific = ?, nom_comu = ? WHERE id_especie = ?');
    $stmt->bind_param('ssi', $nom_cientific, $nom_comu, $id_especie);

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header('Location: consulta_cultius_varietats.php?msg=especie_actualitzada');
        exit;
    }

    $stmt->close();
    $conn->close();
    header('Location: consulta_cultius_varietats.php?err=especie_actualitzar');
    exit;
}

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
    <title>Editar espècie</title>
    <link rel="stylesheet" href="../HTML/styles.css">
</head>
<body>
<div class="page">
    <div class="page-header">
        <h1>Editar espècie</h1>
    </div>

    <div class="panel">
        <form method="post" action="especie_editar.php">
            <input type="hidden" name="id_especie" value="<?php echo (int)$especie['id_especie']; ?>">

            <label>Nom científic:</label>
            <input type="text" name="nom_cientific" value="<?php echo htmlspecialchars($especie['nom_cientific'] ?? ''); ?>" required>

            <label>Nom comú:</label>
            <input type="text" name="nom_comu" value="<?php echo htmlspecialchars($especie['nom_comu'] ?? ''); ?>" required>

            <button type="submit" class="btn btn-primary btn-full mt-2">Guardar canvis</button>
            <a class="btn btn-ghost btn-full mt-2" href="consulta_cultius_varietats.php">Cancel·lar</a>
        </form>
    </div>
</div>
</body>
</html>
