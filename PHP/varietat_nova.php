<?php
require_once __DIR__ . '/db.php';
$conn = db_connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_especie = filter_input(INPUT_POST, 'id_especie', FILTER_VALIDATE_INT);
    $nom_cientific = trim($_POST['nom_cientific'] ?? '');
    $nom_comu = trim($_POST['nom_comu'] ?? '');
    $caracteristiques_agronomiques = trim($_POST['caracteristiques_agronomiques'] ?? '');
    $cicle_vegetatiu = trim($_POST['cicle_vegetatiu'] ?? '');
    $requisits_pollinitzacio = trim($_POST['requisits_pollinitzacio'] ?? '');
    $productivitat_mitjana = trim($_POST['productivitat_mitjana'] ?? '');
    $qualitats_organoleptiques = trim($_POST['qualitats_organoleptiques'] ?? '');
    $foto_url = trim($_POST['foto_url'] ?? '');

    if (!$id_especie || $nom_cientific === '' || $nom_comu === '') {
        $conn->close();
        header('Location: consulta_cultius_varietats.php?err=varietat_guardar');
        exit;
    }

    $productivitat_mitjana = $productivitat_mitjana === '' ? null : $productivitat_mitjana;
    $caracteristiques_agronomiques = $caracteristiques_agronomiques === '' ? null : $caracteristiques_agronomiques;
    $cicle_vegetatiu = $cicle_vegetatiu === '' ? null : $cicle_vegetatiu;
    $requisits_pollinitzacio = $requisits_pollinitzacio === '' ? null : $requisits_pollinitzacio;
    $qualitats_organoleptiques = $qualitats_organoleptiques === '' ? null : $qualitats_organoleptiques;
    $foto_url = $foto_url === '' ? null : $foto_url;

    $stmt = $conn->prepare('INSERT INTO varietat (id_especie, nom_cientific, nom_comu, caracteristiques_agronomiques, cicle_vegetatiu, requisits_pollinitzacio, productivitat_mitjana, qualitats_organoleptiques, foto_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->bind_param(
        'issssssss',
        $id_especie,
        $nom_cientific,
        $nom_comu,
        $caracteristiques_agronomiques,
        $cicle_vegetatiu,
        $requisits_pollinitzacio,
        $productivitat_mitjana,
        $qualitats_organoleptiques,
        $foto_url
    );

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header('Location: consulta_cultius_varietats.php?msg=varietat_creada');
        exit;
    }

    $stmt->close();
    $conn->close();
    header('Location: consulta_cultius_varietats.php?err=varietat_guardar');
    exit;
}

$especies = [];
$res = $conn->query('SELECT id_especie, nom_comu, nom_cientific FROM especie ORDER BY nom_comu');
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $especies[] = $row;
    }
    $res->free();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Nova varietat</title>
    <link rel="stylesheet" href="../HTML/styles.css">
</head>
<body>
<div class="page">
    <div class="page-header">
        <h1>Registrar nova varietat</h1>
        <p class="page-subtitle">Selecciona l'espècie i completa les dades.</p>
    </div>

    <div class="panel">
        <form method="post" action="varietat_nova.php">
            <label>Espècie:</label>
            <select name="id_especie" required>
                <option value="">-- Selecciona --</option>
                <?php foreach ($especies as $especie): ?>
                    <option value="<?php echo (int)$especie['id_especie']; ?>">
                        <?php echo htmlspecialchars(($especie['nom_comu'] ?? '') . ' (' . ($especie['nom_cientific'] ?? '') . ')'); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Nom científic:</label>
            <input type="text" name="nom_cientific" required>

            <label>Nom comú:</label>
            <input type="text" name="nom_comu" required>

            <label>Característiques agronòmiques:</label>
            <textarea name="caracteristiques_agronomiques"></textarea>

            <label>Cicle vegetatiu:</label>
            <textarea name="cicle_vegetatiu"></textarea>

            <label>Requisits de pol·linització:</label>
            <textarea name="requisits_pollinitzacio"></textarea>

            <label>Productivitat mitjana:</label>
            <input type="number" step="0.01" name="productivitat_mitjana">

            <label>Qualitats organolèptiques:</label>
            <textarea name="qualitats_organoleptiques"></textarea>

            <label>URL de la foto:</label>
            <input type="text" name="foto_url" placeholder="https://...">

            <button type="submit" class="btn btn-primary btn-full mt-2">Guardar varietat</button>
            <a class="btn btn-ghost btn-full mt-2" href="consulta_cultius_varietats.php">Tornar a la consulta</a>
        </form>
    </div>
</div>
</body>
</html>
