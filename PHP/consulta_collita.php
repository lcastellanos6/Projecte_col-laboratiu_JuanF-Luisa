<?php
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "web";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Error de connexió: " . $conn->connect_error);

$varietats = [];
$resVar = $conn->query("SELECT id_varietat, nom_comu FROM varietat ORDER BY nom_comu");
if ($resVar) { while ($row = $resVar->fetch_assoc()) $varietats[] = $row; }

$clients = [];
$resCli = $conn->query("SELECT id_client, nom FROM desti_client ORDER BY nom");
if ($resCli) { while ($row = $resCli->fetch_assoc()) $clients[] = $row; }
$any          = $_GET['any'] ?? '';
$id_varietat  = $_GET['id_varietat'] ?? '';
$id_client    = $_GET['id_client'] ?? '';
$estat_lot    = $_GET['estat_lot'] ?? '';
$mode_totals  = $_GET['mode_totals'] ?? '';

$baseSql = "SELECT 
        c.collita_id, c.data_inici, c.data_fi, c.unitat,
        v.nom_comu AS varietat, s.nom AS sector, p.nom AS parcela,
        lp.lot_id, lp.codi_lot, lp.quantitat AS quantitat_lot,
        lp.estat AS estat_lot, dc.id_client, dc.nom AS client
    FROM collita c
    JOIN plantacio pl ON pl.id_plantacio = c.plantacio_id
    JOIN sector s ON s.id_sector = pl.id_sector
    JOIN sector_parcela sp ON sp.id_sector = s.id_sector
    JOIN parcela p ON p.id_parcela = sp.id_parcela
    JOIN varietat v ON v.id_varietat = pl.id_varietat
    LEFT JOIN lot_produccio lp ON lp.collita_id = c.collita_id
    LEFT JOIN desti_client dc ON dc.id_client = lp.id_client";

$sql = $baseSql;

$condicions = [];
if ($any !== '') { $any_int = intval($any); $condicions[] = "YEAR(c.data_inici) = $any_int"; }
if ($id_varietat !== '') { $id_varietat = intval($id_varietat); $condicions[] = "v.id_varietat = $id_varietat"; }
if ($id_client !== '') { $id_client = intval($id_client); $condicions[] = "dc.id_client = $id_client"; }
if ($estat_lot !== '') { $estat_esc = $conn->real_escape_string($estat_lot); $condicions[] = "lp.estat = '$estat_esc'"; }
if (!empty($condicions)) $sql .= " WHERE " . implode(" AND ", $condicions);

if ($mode_totals === '1') {
    $sql = "SELECT t.collita_id, t.data_inici, t.data_fi, t.unitat, t.varietat, t.parcela, t.sector,
            SUM(COALESCE(t.quantitat_lot,0)) AS quantitat_total_lots
            FROM ($sql) t
            GROUP BY t.collita_id, t.data_inici, t.data_fi, t.unitat, t.varietat, t.parcela, t.sector
            ORDER BY t.data_inici DESC";
} else {
    $sql .= " ORDER BY c.data_inici DESC, lp.lot_id";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Consulta de Collites - SIGA</title>
    <link rel="stylesheet" href="../HTML/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .filter-sidebar {
            background: white;
            padding: 1.5rem;
            border-radius: 0.75rem;
            border: 1px solid #e2e8f0;
            height: fit-content;
        }
        .results-content {
            flex: 1;
        }
        .main-layout {
            display: flex;
            gap: 2rem;
            align-items: flex-start;
        }
        @media (max-width: 900px) {
            .main-layout {
                flex-direction: column;
            }
            .filter-sidebar {
                width: 100%;
            }
        }
    </style>
</head>
<body class="page">

    <div class="mb-2">
        <a href="javascript:history.back()" class="btn btn-ghost">
            <i class="fa-solid fa-arrow-left"></i> Tornar
        </a>
    </div>

    <div class="page-header">
        <div class="flex justify-between items-center">
            <div>
                <h1><i class="fa-solid fa-basket-shopping"></i> Consulta de Collites</h1>
                <p class="page-subtitle">Historial detallat de producció i traçabilitat de lots.</p>
            </div>
            <a href="collita_nova.php" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Nova Collita
            </a>
        </div>
    </div>

    <div class="main-layout">
        <aside class="filter-sidebar">
            <h3 class="panel-title" style="margin-bottom: 1rem;"><i class="fa-solid fa-filter"></i> Filtres</h3>
            <form method="get">
                <div class="form-group">
                    <label>Any de campanya</label>
                    <input type="number" name="any" value="<?= htmlspecialchars($any) ?>" placeholder="Ex: 2025">
                </div>
                <div class="form-group">
                    <label>Varietat</label>
                    <select name="id_varietat">
                        <option value="">(Qualsevol)</option>
                        <?php foreach($varietats as $v): ?>
                            <option value="<?= $v['id_varietat'] ?>" <?= ($v['id_varietat']==$id_varietat?'selected':'') ?>>
                                <?= htmlspecialchars($v['nom_comu']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Client / destí</label>
                    <select name="id_client">
                        <option value="">(Qualsevol)</option>
                        <?php foreach($clients as $c): ?>
                            <option value="<?= $c['id_client'] ?>" <?= ($c['id_client']==$id_client?'selected':'') ?>>
                                <?= htmlspecialchars($c['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Estat del lot</label>
                    <select name="estat_lot">
                        <option value="">(Qualsevol)</option>
                        <?php foreach(['Emmagatzemat','En transport','Venut'] as $e): ?>
                            <option value="<?= $e ?>" <?= ($estat_lot==$e?'selected':'') ?>><?= $e ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Mode de visualització</label>
                    <select name="mode_totals">
                        <option value="0" <?= $mode_totals!=='1'?'selected':'' ?>>Detall per lot</option>
                        <option value="1" <?= $mode_totals==='1'?'selected':'' ?>>Totals per collita</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-full mt-1">
                    <i class="fa-solid fa-magnifying-glass"></i> Filtrar
                </button>
                <a href="consulta_collita.php" class="btn btn-ghost btn-full mt-1">Netejar</a>
            </form>
        </aside>

        <main class="results-content">
            <div class="panel">
                <?php if ($result && $result->num_rows>0): ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Varietat</th>
                                <th>Ubicació</th>
                                <?php if($mode_totals==='1'): ?>
                                    <th>Quantitat Total</th>
                                <?php else: ?>
                                    <th>Codi Lot</th>
                                    <th>Quantitat</th>
                                    <th>Estat</th>
                                    <th>Client</th>
                                <?php endif; ?>
                                <th>Accions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <div class="font-bold"><?= date('d/m/Y', strtotime($row['data_inici'])) ?></div>
                                    <div class="text-muted" style="font-size: 0.75rem;"><?= $row['data_fi'] ? date('H:i', strtotime($row['data_fi'])) : '' ?></div>
                                </td>
                                <td><strong><?= htmlspecialchars($row['varietat']) ?></strong></td>
                                <td>
                                    <div style="font-size: 0.85rem;"><?= htmlspecialchars($row['parcela']) ?></div>
                                    <div class="badge badge-info" style="font-size: 0.7rem;"><?= htmlspecialchars($row['sector']) ?></div>
                                </td>
                                
                                <?php if($mode_totals==='1'): ?>
                                    <td>
                                        <span class="font-bold"><?= number_format($row['quantitat_total_lots'], 2, ',', '.') ?></span> 
                                        <small><?= htmlspecialchars($row['unitat']) ?></small>
                                    </td>
                                <?php else: ?>
                                    <td>
                                        <?php if($row['codi_lot']): ?>
                                            <code style="background: #f1f5f9; padding: 2px 4px; border-radius: 4px; font-size: 0.8rem;"><?= htmlspecialchars($row['codi_lot']) ?></code>
                                        <?php else: ?>
                                            <span class="text-muted">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="font-bold"><?= number_format($row['quantitat_lot'], 2, ',', '.') ?></span> 
                                        <small><?= htmlspecialchars($row['unitat']) ?></small>
                                    </td>
                                    <td>
                                        <?php if($row['estat_lot']): ?>
                                            <?php
                                            $b = 'badge-info';
                                            if($row['estat_lot']=='Venut') $b = 'badge-success';
                                            if($row['estat_lot']=='En transport') $b = 'badge-warning';
                                            ?>
                                            <span class="badge <?= $b ?>"><?= $row['estat_lot'] ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($row['client'] ?? '—') ?></td>
                                <?php endif; ?>

                                <td>
                                    <div class="flex gap-1">
                                        <?php if(!$mode_totals && $row['lot_id']): ?>
                                            <a href="etiqueta_lot.php?id=<?= $row['lot_id'] ?>" class="btn btn-ghost btn-sm" title="Etiqueta QR">
                                                <i class="fa-solid fa-qrcode"></i>
                                            </a>
                                        <?php endif; ?>
                                        <a href="collita_detall.php?id=<?= $row['collita_id'] ?>" class="btn btn-ghost btn-sm" title="Veure detall">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div style="padding: 3rem; text-align: center; color: #64748b;">
                        <i class="fa-solid fa-inbox" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.2;"></i>
                        <p>No s'han trobat collites amb els filtres seleccionats.</p>
                        <a href="consulta_collita.php" class="text-primary">Netejar filtres</a>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

</body>
</html>
<?php $conn->close(); ?>