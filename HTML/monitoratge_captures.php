<?php
session_start();
require_once __DIR__ . '/../PHP/db.php';
$conn = db_connect();

$trampes = [];
$sql = "SELECT t.id_trampa, t.model, s.nom as sector_nom 
        FROM trampa t 
        JOIN sector s ON t.id_sector = s.id_sector 
        ORDER BY s.nom, t.model";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $trampes[] = $row;
    }
    $result->free();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Registrar Captures</title>
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
        <h2><i class="fa-solid fa-clipboard-check"></i> Registrar Captures de Plaga</h2>
        <p class="page-subtitle">Anota les captures trobades a les trampes.</p>
    </div>

    <div class="panel">
        <form action="../PHP/guardar_captura.php" method="post">
            <div class="grid-2 mb-2">
                <div>
                    <label>Trampa *</label>
                    <select name="id_trampa" required>
                        <option value="">Selecciona una trampa...</option>
                        <?php foreach ($trampes as $trampa): ?>
                            <option value="<?= $trampa['id_trampa'] ?>">
                                <?= htmlspecialchars($trampa['sector_nom']) ?> - <?= htmlspecialchars($trampa['model']) ?> (ID: <?= $trampa['id_trampa'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label>Data de Registre *</label>
                    <input type="date" name="data_registre" required value="<?= date('Y-m-d') ?>">
                </div>
            </div>

            <div class="grid-2 mb-2">
                <div>
                    <label>Plaga Objectiu *</label>
                    <input type="text" name="plaga_objectiu" required placeholder="Ex: Mosca de la fruita, Pugó...">
                </div>
                <div>
                    <label>Quantitat Capturada *</label>
                    <input type="number" name="quantitat_capturada" required min="0" placeholder="0">
                </div>
            </div>

            <label>Observacions</label>
            <textarea name="observacions" rows="3" placeholder="Notes de la inspecció..."></textarea>

            <button type="submit" class="btn btn-primary btn-full mt-3">
                <i class="fa-solid fa-save"></i> Guardar Captura
            </button>
        </form>
    </div>
</div>
</body>
</html>
