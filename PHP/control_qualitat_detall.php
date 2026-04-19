<?php
require_once __DIR__ . '/auth.php';
require_login();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    die("ID de control no vàlid.");
}

$conn = db_connect();
$sql = "SELECT cq.*, lp.codi_lot, lp.lot_id
        FROM control_qualitat cq
        JOIN lot_produccio lp ON cq.lot_id = lp.lot_id
        WHERE cq.control_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$control = $stmt->get_result()->fetch_assoc();

if (!$control) {
    die("Control no trobat.");
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Detall Control de Qualitat</title>
    <link rel="stylesheet" href="../HTML/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        .qr-section {
            text-align: center;
            padding: 1.5rem;
            background: #f8fafc;
            border-radius: 0.75rem;
            border: 1px dashed #cbd5e1;
        }
        .info-card {
            background: white;
            padding: 1.5rem;
            border-radius: 0.75rem;
            border: 1px solid #e2e8f0;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body class="page">
    <div class="mb-2">
        <a href="consulta_qualitat.php" class="btn btn-ghost">
            <i class="fa-solid fa-arrow-left"></i> Tornar al llistat
        </a>
    </div>

    <div class="page-header">
        <h1>Control de Qualitat: Lot <?= htmlspecialchars($control['codi_lot']) ?></h1>
        <p class="page-subtitle">Realitzat el <?= date('d/m/Y', strtotime($control['data_control'])) ?></p>
    </div>

    <div class="detail-grid">
        <div class="panel">
            <h3 class="panel-title"><i class="fa-solid fa-vial"></i> Paràmetres Mesurats</h3>
            <div class="data-row">
                <span class="data-label">Calibre</span>
                <span class="data-value"><?= htmlspecialchars($control['calibre'] ?? '—') ?></span>
            </div>
            <div class="data-row">
                <span class="data-label">Color</span>
                <span class="data-value"><?= htmlspecialchars($control['color'] ?? '—') ?></span>
            </div>
            <div class="data-row">
                <span class="data-label">Fermesa</span>
                <span class="data-value"><?= htmlspecialchars($control['fermesa'] ?? '—') ?></span>
            </div>
            <div class="data-row">
                <span class="data-label">Sucres (Brix)</span>
                <span class="data-value"><?= htmlspecialchars($control['brix'] ?? '—') ?>°</span>
            </div>
            <div class="data-row">
                <span class="data-label">Defectes</span>
                <span class="data-value"><?= htmlspecialchars($control['defectes'] ?? 'Cap defecte detectat') ?></span>
            </div>
        </div>

        <div class="panel">
            <h3 class="panel-title"><i class="fa-solid fa-star"></i> Valoració Final</h3>
            <div class="flex items-center gap-2 mb-2">
                <span class="qualificacio-badge" style="width: 40px; height: 40px; line-height: 40px; font-size: 1.2rem;">
                    <?= strtoupper(substr($control['qualificacio_final'] ?? 'P', 0, 1)) ?>
                </span>
                <div>
                    <strong>Qualificació: <?= htmlspecialchars($control['qualificacio_final'] ?? 'Pendent') ?></strong><br>
                    <small>Estat del lot per a comercialització</small>
                </div>
            </div>
            
            <div class="qr-section mt-2">
                <p><strong>Codi de Traçabilitat (QR)</strong></p>
                <?php 
                $qr_data = "https://trace/lot/" . $control['codi_lot'];
                $qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($qr_data);
                ?>
                <img src="<?= $qr_url ?>" alt="QR Lot">
                <div class="mt-1">
                    <a href="etiqueta_lot.php?id=<?= $control['lot_id'] ?>" class="btn btn-primary btn-sm">
                        <i class="fa-solid fa-print"></i> Imprimir Etiqueta
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>
