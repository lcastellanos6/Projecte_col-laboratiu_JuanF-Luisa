<?php
session_start();
require_once __DIR__ . '/../PHP/db.php';
$conn = db_connect();

$sectores = [];
$sql = "SELECT id_sector, nom FROM sector ORDER BY nom";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $sectores[] = $row;
    }
    $result->free();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Monitoratge Ambiental</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<div class="page">
    <div class="mb-2">
        <a href="javascript:history.back()" class="btn btn-ghost">
            <i class="fa-solid fa-arrow-left"></i> Tornar
        </a>
    </div>
    <div class="page-header">
        <h2><i class="fa-solid fa-tower-broadcast"></i> Registrar Lectura de Sensors</h2>
        <p class="page-subtitle">Monitoratge ambiental i de reg.</p>
    </div>

    <div class="panel">
        <form action="../PHP/guardar_sensor.php" method="post">
            <label>Sector *</label>
            <select name="id_sector" required class="mb-2">
                <option value="">Selecciona un sector...</option>
                <?php foreach ($sectores as $sector): ?>
                    <option value="<?= $sector['id_sector'] ?>"><?= htmlspecialchars($sector['nom']) ?></option>
                <?php endforeach; ?>
            </select>

            <div class="grid-2 mb-2">
                <div>
                    <label><i class="fa-solid fa-temperature-half"></i> Temperatura Aire (°C)</label>
                    <input type="number" step="0.1" name="temperatura_aire" placeholder="0.0">
                </div>
                <div>
                    <label><i class="fa-solid fa-droplet"></i> Humitat Aire (%)</label>
                    <input type="number" step="0.1" name="humitat_aire" placeholder="0.0">
                </div>
            </div>

            <div class="grid-2 mb-2">
                <div>
                    <label><i class="fa-solid fa-faucet-drip"></i> Humitat Sòl (%)</label>
                    <input type="number" step="0.1" name="humitat_sol" placeholder="0.0">
                </div>
                <div>
                    <label><i class="fa-solid fa-cloud-rain"></i> Pluja (mm)</label>
                    <input type="number" step="0.1" name="pluja" placeholder="0.0">
                </div>
            </div>

            <label><i class="fa-solid fa-sun"></i> Evaporació (mm)</label>
            <input type="number" step="0.1" name="evaporacio" placeholder="0.0" class="mb-2">

            <button type="submit" class="btn btn-primary btn-full mt-3">
                <i class="fa-solid fa-save"></i> Guardar Lectura
            </button>
        </form>
    </div>
</div>
</body>
</html>
