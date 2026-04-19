<?php
require_once __DIR__ . '/db.php';
$conn = db_connect();

$id_lot = $_GET['id'] ?? 0;
$collita_id = $_GET['collita_id'] ?? 0;

$lots = [];

if ($id_lot > 0) {
    $sql = "SELECT lp.*, c.data_inici, v.nom_comu as varietat, s.nom as sector_nom
            FROM lot_produccio lp
            JOIN collita c ON lp.collita_id = c.collita_id
            JOIN plantacio pl ON c.plantacio_id = pl.id_plantacio
            JOIN varietat v ON pl.id_varietat = v.id_varietat
            JOIN sector s ON pl.id_sector = s.id_sector
            WHERE lp.lot_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_lot);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $lots[] = $row;
    }
} elseif ($collita_id > 0) {
    $sql = "SELECT lp.*, c.data_inici, v.nom_comu as varietat, s.nom as sector_nom
            FROM lot_produccio lp
            JOIN collita c ON lp.collita_id = c.collita_id
            JOIN plantacio pl ON c.plantacio_id = pl.id_plantacio
            JOIN varietat v ON pl.id_varietat = v.id_varietat
            JOIN sector s ON pl.id_sector = s.id_sector
            WHERE lp.collita_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $collita_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $lots[] = $row;
    }
}

// Si no hay lotes, mostrar mensaje y botón atrás
if (empty($lots)) {
    ?>
    <!DOCTYPE html>
    <html lang="ca">
    <head>
        <meta charset="UTF-8">
        <title>Lot no trobat</title>
        <link rel="stylesheet" href="../HTML/styles.css">
        <style>
            body { font-family: sans-serif; text-align: center; padding: 50px; }
            .error-box { background: #fee2e2; border: 1px solid #ef4444; padding: 20px; border-radius: 8px; display: inline-block; }
            .btn { display: inline-block; margin-top: 20px; padding: 10px 20px; background: #2f7d2f; color: white; text-decoration: none; border-radius: 4px; }
        </style>
    </head>
    <body>
        <div class="error-box">
            <h2>Lot no trobat</h2>
            <p>No s'han trobat lots de producció per a aquesta collita.</p>
            <a href="javascript:void(0)" onclick="window.history.length > 1 ? history.back() : window.location.href='consulta_lots.php'" class="btn">Tornar enrere</a>
        </div>
    </body>
    </html>
    <?php
    exit;
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Etiquetes de Traçabilitat</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        .etiqueta-container { display: flex; flex-wrap: wrap; gap: 20px; justify-content: center; }
        .etiqueta { border: 2px solid #333; padding: 20px; width: 400px; border-radius: 10px; background: white; page-break-inside: avoid; }
        .header { text-align: center; border-bottom: 1px solid #ccc; padding-bottom: 10px; margin-bottom: 15px; }
        .codi-lot { font-size: 1.5em; font-weight: bold; color: #2e7d32; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 15px; }
        .label { font-size: 0.8em; color: #666; text-transform: uppercase; }
        .valor { font-weight: bold; }
        .qr-section { text-align: center; margin-top: 15px; padding: 10px; background: #fff; border: 1px solid #eee; border-radius: 8px; }
        .qr-img { width: 120px; height: 120px; margin-bottom: 5px; }
        .qr-text { font-size: 0.7em; color: #888; text-transform: uppercase; letter-spacing: 1px; }
        .no-print { margin-bottom: 20px; text-align: center; }
        .btn { display: inline-block; padding: 10px 20px; background: #2f7d2f; color: white; text-decoration: none; border-radius: 4px; border: none; cursor: pointer; }
        .btn-secondary { background: #64748b; }
        @media print { .no-print { display: none; } body { padding: 0; } }
    </style>
</head>
<body>
    <div class="no-print">
        <a href="javascript:void(0)" onclick="window.history.length > 1 ? history.back() : window.location.href='consulta_lots.php'" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Tornar enrere
        </a>
        <button onclick="window.print()" class="btn">
            <i class="fa-solid fa-print"></i> Imprimir Totes les Etiquetes
        </button>
    </div>

    <div class="etiqueta-container">
        <?php foreach ($lots as $lot): ?>
        <div class="etiqueta">
            <div class="header">
                <div class="label">Producte de l'Explotació</div>
                <div class="codi-lot"><?= htmlspecialchars($lot['codi_lot']) ?></div>
            </div>

            <div class="grid">
                <div>
                    <div class="label">Varietat</div>
                    <div class="valor"><?= htmlspecialchars($lot['varietat']) ?></div>
                </div>
                <div>
                    <div class="label">Data Collita</div>
                    <div class="valor"><?= date('d/m/Y', strtotime($lot['data_inici'])) ?></div>
                </div>
                <div>
                    <div class="label">Origen</div>
                    <div class="valor"><?= htmlspecialchars($lot['sector_nom']) ?></div>
                </div>
                <div>
                    <div class="label">Quantitat</div>
                    <div class="valor"><?= $lot['quantitat'] ?> <?= $lot['unitat'] ?></div>
                </div>
                <div>
                    <div class="label">Qualitat</div>
                    <div class="valor"><?= htmlspecialchars($lot['qualitat'] ?? 'Estàndard') ?></div>
                </div>
            </div>

            <div class="qr-section">
                <?php 
                $qr_data = $lot['qr_url'] ?? "https://trace/lot/" . $lot['codi_lot'];
                $qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=" . urlencode($qr_data);
                ?>
                <img src="<?= $qr_url ?>" alt="Codi QR Traçabilitat" class="qr-img">
                <div class="qr-text">Traçabilitat Garantida</div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
