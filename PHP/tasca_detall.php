<?php
require_once __DIR__ . '/auth.php';
require_login();

$conn = db_connect();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$res = $conn->query("SELECT t.*, s.nom as nom_sector 
                     FROM tasca t 
                     LEFT JOIN sector s ON t.id_sector = s.id_sector 
                     WHERE t.id_tasca = $id");
$t = $res->fetch_assoc();

if (!$t) {
    die("Tasca no trobada.");
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Detall de la Tasca</title>
    <link rel="stylesheet" href="../HTML/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<div class="page">
    <div class="mb-2">
        <a href="tasca.php" class="btn btn-ghost">
            <i class="fa-solid fa-arrow-left"></i> Tornar al llistat
        </a>
    </div>

    <div class="page-header">
        <div class="flex justify-between items-center">
            <div>
                <h2><i class="fa-solid fa-list-check"></i> Detall de la Tasca: <?= htmlspecialchars($t['nom_tasca']) ?></h2>
                <p class="page-subtitle">Informació completa de la tasca programada.</p>
            </div>
            <div class="flex gap-1">
                <a href="tasca_editar.php?id=<?= $t['id_tasca'] ?>" class="btn btn-primary">
                    <i class="fa-solid fa-pen-to-square"></i> Editar Tasca
                </a>
            </div>
        </div>
    </div>

    <div class="grid-2">
        <div class="panel">
            <h3 class="panel-title"><i class="fa-solid fa-info-circle"></i> Informació General</h3>
            <div class="info-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.75rem; color: #64748b; text-transform: uppercase;">Tipus</label>
                    <span style="font-weight: 600;"><?= htmlspecialchars($t['tipus_tasca'] ?? 'Sense definir') ?></span>
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; color: #64748b; text-transform: uppercase;">Estat</label>
                    <span class="badge badge-info"><?= htmlspecialchars($t['estat']) ?></span>
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; color: #64748b; text-transform: uppercase;">Sector</label>
                    <span style="font-weight: 600;"><?= htmlspecialchars($t['nom_sector'] ?? 'N/A') ?></span>
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; color: #64748b; text-transform: uppercase;">Durada Estimada</label>
                    <span style="font-weight: 600;"><?= htmlspecialchars($t['durada_estimada'] ?? '—') ?></span>
                </div>
            </div>
        </div>

        <div class="panel">
            <h3 class="panel-title"><i class="fa-solid fa-calendar"></i> Planificació</h3>
            <div class="info-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.75rem; color: #64748b; text-transform: uppercase;">Data Inici</label>
                    <span style="font-weight: 600;"><?= $t['data_inici'] ? date('d/m/Y', strtotime($t['data_inici'])) : '—' ?></span>
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; color: #64748b; text-transform: uppercase;">Data Final</label>
                    <span style="font-weight: 600;"><?= $t['data_final'] ? date('d/m/Y', strtotime($t['data_final'])) : '—' ?></span>
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; color: #64748b; text-transform: uppercase;">Inici Real</label>
                    <span style="font-weight: 600;"><?= $t['data_inici_real'] ? date('d/m/Y H:i', strtotime($t['data_inici_real'])) : 'Pendent' ?></span>
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; color: #64748b; text-transform: uppercase;">Final Real</label>
                    <span style="font-weight: 600;"><?= $t['data_fi_real'] ? date('d/m/Y H:i', strtotime($t['data_fi_real'])) : 'Pendent' ?></span>
                </div>
            </div>
        </div>
    </div>

    <div class="panel mt-3">
        <h3 class="panel-title"><i class="fa-solid fa-align-left"></i> Instruccions i Notes</h3>
        <div style="margin-top: 1rem;">
            <label style="display: block; font-size: 0.75rem; color: #64748b; text-transform: uppercase;">Instruccions</label>
            <p style="background: #f8fafc; padding: 1rem; border-radius: 0.5rem; margin-top: 0.5rem;">
                <?= nl2br(htmlspecialchars($t['instruccions'] ?? 'No hi ha instruccions detallades.')) ?>
            </p>
        </div>
        <div style="margin-top: 1rem;">
            <label style="display: block; font-size: 0.75rem; color: #64748b; text-transform: uppercase;">Notes d'execució</label>
            <p style="background: #f8fafc; padding: 1rem; border-radius: 0.5rem; margin-top: 0.5rem;">
                <?= nl2br(htmlspecialchars($t['notes'] ?? 'Sense notes.')) ?>
            </p>
        </div>
    </div>
</div>
</body>
</html>
<?php $conn->close(); ?>
