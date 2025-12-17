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
<title>Consulta de collites</title>
<style>
* { margin:0; padding:0; box-sizing:border-box; font-family:Arial, sans-serif; }
body { background:#f4fff4; color:#333; }

.container { display:flex; height:100vh; font-size:0.85rem; }
.sidebar { width:280px; background:#fff; padding:15px; border-right:1px solid #ddd; overflow-y:auto; }
.content { flex:1; padding:15px; overflow:auto; }

h1 { color:#2f7d2f; margin-bottom:10px; font-size:1.2rem; }
h2 { margin-bottom:10px; color:#2f7d2f; font-size:1rem; }

.form-group { margin-bottom:10px; display:flex; flex-direction:column; }
label { font-weight:bold; margin-bottom:3px; font-size:0.85rem; }
input, select { padding:5px; border:1px solid #ccc; border-radius:4px; font-size:0.85rem; }
.btn { padding:7px 10px; background:#2f7d2f; color:white; border:none; border-radius:5px; cursor:pointer; font-weight:bold; font-size:0.85rem; }
.btn:hover { background:#3d9b3d; }

.table-wrapper { max-height:calc(100vh - 60px); overflow:auto; }
.table { width:100%; border-collapse:collapse; margin-top:10px; font-size:0.8rem; }
.table th, .table td { padding:4px 6px; border-bottom:1px solid #ddd; text-align:left; }
.table th { background:#e6f4e6; position:sticky; top:0; font-weight:bold; font-size:0.8rem; }
.table tr:hover { background:#f0fff0; }
</style>
</head>
<body>

<div class="container">
    <div class="sidebar">
        <h2>Filtres de collites</h2>
        <form method="get">
            <div class="form-group">
                <label>Any de campanya</label>
                <input type="number" name="any" value="<?= htmlspecialchars($any) ?>">
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
                <label>Mode</label>
                <select name="mode_totals">
                    <option value="0" <?= $mode_totals!=='1'?'selected':'' ?>>Detall per lot</option>
                    <option value="1" <?= $mode_totals==='1'?'selected':'' ?>>Totals per collita</option>
                </select>
            </div>
            <button type="submit" class="btn">Filtrar</button>
        </form>
    </div>

    <div class="content">
        <h2>Resultats</h2>
        <?php if ($result && $result->num_rows>0): ?>
        <div class="table-wrapper">
            <table class="table">
                <tr>
                    <th>ID</th>
                    <th>Inici</th>
                    <th>Fi</th>
                    <th>Varietat</th>
                    <th>Parcel·la</th>
                    <th>Sector</th>
                    <?php if($mode_totals==='1'): ?>
                        <th>Quantitat</th>
                        <th>Unitat</th>
                    <?php else: ?>
                        <th>Lot</th>
                        <th>Unitat</th>
                        <th>Codi</th>
                        <th>Estat</th>
                        <th>Client</th>
                    <?php endif; ?>
                </tr>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['collita_id'] ?></td>
                    <td><?= $row['data_inici'] ?></td>
                    <td><?= $row['data_fi'] ?></td>
                    <td><?= htmlspecialchars($row['varietat']) ?></td>
                    <td><?= htmlspecialchars($row['parcela']) ?></td>
                    <td><?= htmlspecialchars($row['sector']) ?></td>
                    <?php if($mode_totals==='1'): ?>
                        <td><?= htmlspecialchars($row['quantitat_total_lots']) ?></td>
                        <td><?= htmlspecialchars($row['unitat']) ?></td>
                    <?php else: ?>
                        <td><?= htmlspecialchars($row['quantitat_lot']) ?></td>
                        <td><?= htmlspecialchars($row['unitat']) ?></td>
                        <td><?= htmlspecialchars($row['codi_lot']) ?></td>
                        <td><?= htmlspecialchars($row['estat_lot']) ?></td>
                        <td><?= htmlspecialchars($row['client']) ?></td>
                    <?php endif; ?>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
        <?php else: ?>
            <p>No s'han trobat collites amb aquests filtres.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
<?php $conn->close(); ?>