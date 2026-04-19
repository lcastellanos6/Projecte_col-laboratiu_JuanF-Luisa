<?php
require_once __DIR__ . '/auth.php';
require_login();

$conn = db_connect();

$sql = "SELECT cq.*, lp.codi_lot, lp.lot_id
        FROM control_qualitat cq
        JOIN lot_produccio lp ON cq.lot_id = lp.lot_id
        ORDER BY cq.data_control DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Controls de Qualitat</title>
    <link rel="stylesheet" href="../HTML/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /* Estils moguts a styles.css */
    </style>
</head>
<body>
<div class="page">
    <div class="mb-2">
        <a href="javascript:history.back()" class="btn btn-ghost">
            <i class="fa-solid fa-arrow-left"></i> Tornar
        </a>
    </div>

    <div class="page-header">
        <div class="flex justify-between items-center">
            <div>
                <h2><i class="fa-solid fa-clipboard-check"></i> Controls de Qualitat</h2>
                <p class="page-subtitle">Seguiment dels estàndards de qualitat dels lots de producció.</p>
            </div>
            <a href="../HTML/control_qualitat.php" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Nou Control
            </a>
        </div>
    </div>

    <div class="panel">
        <table class="table">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Lot</th>
                    <th>Calibre</th>
                    <th>Color</th>
                    <th>Fermesa</th>
                    <th>Qualificació</th>
                    <th>Accions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($row['data_control'])) ?></td>
                        <td><strong><?= htmlspecialchars($row['codi_lot']) ?></strong></td>
                        <td><?= htmlspecialchars($row['calibre'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($row['color'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($row['fermesa'] ?? '—') ?></td>
                        <td>
                            <span class="qualificacio-badge">
                                <?= strtoupper(substr($row['qualificacio_final'] ?? 'P', 0, 1)) ?>
                            </span>
                        </td>
                        <td>
                            <div class="flex gap-2">
                                <a href="control_qualitat_detall.php?id=<?= $row['control_id'] ?>" class="btn-eye" title="Veure detall">
                                    <i class="fa-solid fa-circle-info"></i>
                                </a>
                                <a href="etiqueta_lot.php?id=<?= $row['lot_id'] ?>" class="btn-qr" title="Veure QR i Etiqueta">
                                    <i class="fa-solid fa-qrcode"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="7" style="text-align:center;">No hi ha controls registrats.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
<?php $conn->close(); ?>
