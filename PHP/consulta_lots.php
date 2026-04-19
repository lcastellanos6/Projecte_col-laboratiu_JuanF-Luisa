<?php
require_once __DIR__ . '/db.php';
$conn = db_connect();

$sql = "SELECT lp.*, v.nom_comu as varietat, s.nom as sector_nom
        FROM lot_produccio lp
        JOIN collita c ON lp.collita_id = c.collita_id
        JOIN plantacio pl ON c.plantacio_id = pl.id_plantacio
        JOIN varietat v ON pl.id_varietat = v.id_varietat
        JOIN sector s ON pl.id_sector = s.id_sector
        ORDER BY lp.data_creacio DESC";

$res = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Consulta de Lots</title>
    <link rel="stylesheet" href="../HTML/styles.css">
</head>
<body>
<div class="page">
    <div class="mb-2">
        <a href="javascript:history.back()" class="btn btn-ghost">
            <i class="fa-solid fa-arrow-left"></i> Tornar
        </a>
    </div>
    <div class="page-header">
        <h2><i class="fa-solid fa-barcode"></i> Consulta de Lots de Producció</h2>
        <p class="page-subtitle">Gestió de la traçabilitat, etiquetatge i estat de l'estoc.</p>
    </div>

    <div class="panel">
        <h3 class="panel-title"><i class="fa-solid fa-list-ul"></i> Lots actius i venuts</h3>
        <table>
            <thead>
                <tr>
                    <th>Codi Lot</th>
                    <th>Data Creació</th>
                    <th>Producte / Varietat</th>
                    <th>Sector d'Origen</th>
                    <th>Quantitat</th>
                    <th>Estat</th>
                    <th style="text-align: center;">Accions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($res->num_rows == 0): ?>
                    <tr><td colspan="7" style="text-align:center;">No s'han trobat lots de producció.</td></tr>
                <?php endif; ?>
                
                <?php while ($lot = $res->fetch_assoc()): 
                    $estatEstat = $lot['estat'] == 'Venut' ? 'badge-danger' : 'badge-success';
                ?>
                <tr>
                    <td><strong><?= htmlspecialchars($lot['codi_lot']) ?></strong></td>
                    <td><?= date('d/m/Y', strtotime($lot['data_creacio'])) ?></td>
                    <td><?= htmlspecialchars($lot['varietat']) ?></td>
                    <td><?= htmlspecialchars($lot['sector_nom']) ?></td>
                    <td><strong><?= number_format($lot['quantitat'], 2) ?></strong> <?= htmlspecialchars($lot['unitat']) ?></td>
                    <td>
                        <span class="badge <?= $estatEstat ?>">
                            <?= htmlspecialchars($lot['estat']) ?>
                        </span>
                    </td>
                    <td style="text-align: center;">
                        <a href="etiqueta_lot.php?id=<?= $lot['lot_id'] ?>" class="btn btn-ghost" title="Veure etiqueta">
                            <i class="fa-solid fa-print"></i> Etiqueta
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
