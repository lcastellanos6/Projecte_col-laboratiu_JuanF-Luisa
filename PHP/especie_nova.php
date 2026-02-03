<?php
require_once __DIR__ . '/db.php';
$conn = db_connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom_cientific = trim($_POST['nom_cientific'] ?? '');
    $nom_comu = trim($_POST['nom_comu'] ?? '');

    if ($nom_cientific === '' || $nom_comu === '') {
        $conn->close();
        header('Location: consulta_cultius_varietats.php?err=especie_guardar');
        exit;
    }

    $stmt = $conn->prepare('INSERT INTO especie (nom_cientific, nom_comu) VALUES (?, ?)');
    $stmt->bind_param('ss', $nom_cientific, $nom_comu);

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header('Location: consulta_cultius_varietats.php?msg=especie_creada');
        exit;
    }

    $stmt->close();
    $conn->close();
    header('Location: consulta_cultius_varietats.php?err=especie_guardar');
    exit;
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Nova espècie</title>
    <link rel="stylesheet" href="../HTML/styles.css">
</head>
<body>
<div class="page">
    <div class="page-header">
        <h1>Registrar nova espècie</h1>
        <p class="page-subtitle">Introdueix el nom comú i el nom científic.</p>
    </div>

    <div class="panel">
        <form method="post" action="especie_nova.php">
            <label>Nom científic:</label>
            <input type="text" name="nom_cientific" required>

            <label>Nom comú:</label>
            <input type="text" name="nom_comu" required>

            <button type="submit" class="btn btn-primary btn-full mt-2">Guardar espècie</button>
            <a class="btn btn-ghost btn-full mt-2" href="consulta_cultius_varietats.php">Tornar a la consulta</a>
        </form>
    </div>
</div>
</body>
</html>
