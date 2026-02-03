<?php
require_once __DIR__ . '/db.php';
$conn = db_connect();

$id_varietat = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id_varietat) {
    $conn->close();
    echo "<p style='color:red; font-weight:bold;'>ID de varietat no vàlid.</p>";
    exit;
}

$stmt = $conn->prepare('SELECT v.id_varietat, v.id_especie, v.nom_cientific, v.nom_comu, v.caracteristiques_agronomiques, v.cicle_vegetatiu, v.requisits_pollinitzacio, v.productivitat_mitjana, v.qualitats_organoleptiques, v.foto_url, e.nom_comu AS especie_nom_comu, e.nom_cientific AS especie_nom_cientific FROM varietat v JOIN especie e ON e.id_especie = v.id_especie WHERE v.id_varietat = ?');
$stmt->bind_param('i', $id_varietat);
$stmt->execute();
$res = $stmt->get_result();
$varietat = $res ? $res->fetch_assoc() : null;
$stmt->close();
$conn->close();

if (!$varietat) {
    echo "<p style='color:red; font-weight:bold;'>Varietat no trobada.</p>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Detall de varietat</title>
    <link rel="stylesheet" href="../HTML/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
  <div style="padding:15px; background:#f4fff4; border-bottom:1px solid #ddd; text-align:right;">
    <a href="#" class="btn btn-primary" onclick="history.back(); return false;"><i class="fa-solid fa-arrow-left"></i> Tornar</a>
  </div>
<div class="page">
    <div class="page-header">
        <h1>Detall de varietat</h1>
    </div>

    <div class="panel">
        <p><strong>Espècie:</strong> <?php echo htmlspecialchars(($varietat['especie_nom_comu'] ?? '') . ' (' . ($varietat['especie_nom_cientific'] ?? '') . ')'); ?></p>
        <p><strong>Nom científic:</strong> <?php echo htmlspecialchars($varietat['nom_cientific'] ?? ''); ?></p>
        <p><strong>Nom comú:</strong> <?php echo htmlspecialchars($varietat['nom_comu'] ?? ''); ?></p>
        <p><strong>Característiques agronòmiques:</strong> <?php echo htmlspecialchars($varietat['caracteristiques_agronomiques'] ?? '-'); ?></p>
        <p><strong>Cicle vegetatiu:</strong> <?php echo htmlspecialchars($varietat['cicle_vegetatiu'] ?? '-'); ?></p>
        <p><strong>Requisits de pol·linització:</strong> <?php echo htmlspecialchars($varietat['requisits_pollinitzacio'] ?? '-'); ?></p>
        <p><strong>Productivitat mitjana:</strong> <?php echo htmlspecialchars($varietat['productivitat_mitjana'] ?? '-'); ?></p>
        <p><strong>Qualitats organolèptiques:</strong> <?php echo htmlspecialchars($varietat['qualitats_organoleptiques'] ?? '-'); ?></p>
        <p><strong>URL de la foto:</strong> <?php echo htmlspecialchars($varietat['foto_url'] ?? '-'); ?></p>
        <?php if (!empty($varietat['foto_url'])): ?>
            <img class="thumb" src="<?php echo htmlspecialchars($varietat['foto_url']); ?>" alt="Foto varietat" onerror="this.style.display='none';" style="width:90px; height:90px; object-fit:cover; border-radius:6px; border:1px solid #d8e3c8;">
        <?php endif; ?>

        <a class="btn btn-primary btn-full mt-2" href="varietat_editar.php?id=<?php echo (int)$varietat['id_varietat']; ?>">Editar varietat</a>
        <a class="btn btn-ghost btn-full mt-2" href="consulta_cultius_varietats.php">Tornar a la consulta</a>
    </div>
</div>
</body>
</html>
