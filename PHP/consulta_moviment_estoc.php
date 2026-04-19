<?php
require_once __DIR__ . '/db.php';

$conn = db_connect();

$id_lot = trim($_GET['id_lot'] ?? '');
$id_aplicacio = trim($_GET['id_aplicacio'] ?? '');
$motiu = trim($_GET['motiu'] ?? '');
$data_des = trim($_GET['data_des'] ?? '');
$data_fins = trim($_GET['data_fins'] ?? '');

$sql = "
    SELECT
        me.id_mov,
        me.id_lot,
        pl.numero_lot,
        p.nom_comercial,
        me.data,
        me.quantitat,
        me.motiu,
        me.id_aplicacio,
        me.observacions
    FROM moviment_estoc me
    JOIN producte_lot pl ON pl.id_lot = me.id_lot
    JOIN producte p ON p.id_producte = pl.id_producte
";

$conds = [];
$params = [];
$types = '';

if ($id_lot !== '' && ctype_digit($id_lot)) {
    $conds[] = 'me.id_lot = ?';
    $params[] = (int) $id_lot;
    $types .= 'i';
}
if ($id_aplicacio !== '' && ctype_digit($id_aplicacio)) {
    $conds[] = 'me.id_aplicacio = ?';
    $params[] = (int) $id_aplicacio;
    $types .= 'i';
}
if ($motiu !== '') {
    $conds[] = 'me.motiu = ?';
    $params[] = $motiu;
    $types .= 's';
}
if ($data_des !== '') {
    $conds[] = 'DATE(me.data) >= ?';
    $params[] = $data_des;
    $types .= 's';
}
if ($data_fins !== '') {
    $conds[] = 'DATE(me.data) <= ?';
    $params[] = $data_fins;
    $types .= 's';
}

if (!empty($conds)) {
    $sql .= ' WHERE ' . implode(' AND ', $conds);
}

$sql .= ' ORDER BY me.data DESC, me.id_mov DESC';

$rows = [];
$stmt = $conn->prepare($sql);
if ($stmt) {
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    if ($stmt->execute()) {
        $res = $stmt->get_result();
        while ($r = $res->fetch_assoc()) {
            $rows[] = $r;
        }
    }
    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Consulta moviments d'estoc</title>
    <link rel="stylesheet" href="../HTML/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<div class="page">
    <div class="page-header">
        <div class="panel-header">
            <div>
                <h1>Consulta moviments d'estoc</h1>
                <p class="page-subtitle">Llistat de moviments registrats per lot i aplicació.</p>
            </div>
            <div class="page-header-actions">
                <a class="btn btn-primary" href="../HTML/moviment_lot.html">Nou moviment</a>
            </div>
        </div>
    </div>

    <div class="panel">
        <form method="get" class="form-grid-2">
            <label>ID Lot</label>
            <input type="number" name="id_lot" value="<?php echo htmlspecialchars($id_lot); ?>">

            <label>ID Aplicació</label>
            <input type="number" name="id_aplicacio" value="<?php echo htmlspecialchars($id_aplicacio); ?>">

            <label>Motiu</label>
            <select name="motiu">
                <option value="">(Qualsevol)</option>
                <?php foreach (['Compra', 'Ajust', 'Aplicacio'] as $m): ?>
                    <option value="<?php echo $m; ?>" <?php echo ($motiu === $m) ? 'selected' : ''; ?>><?php echo $m; ?></option>
                <?php endforeach; ?>
            </select>

            <label>Data des</label>
            <input type="date" name="data_des" value="<?php echo htmlspecialchars($data_des); ?>">

            <label>Data fins</label>
            <input type="date" name="data_fins" value="<?php echo htmlspecialchars($data_fins); ?>">

            <button type="submit" class="btn btn-primary mt-2">Filtrar</button>
        </form>
    </div>

    <div class="panel">
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID Mov</th>
                        <th>Data</th>
                        <th>ID Lot</th>
                        <th>Núm. lot</th>
                        <th>Producte</th>
                        <th>Quantitat</th>
                        <th>Motiu</th>
                        <th>ID Aplicació</th>
                        <th>Observacions</th>
                        <th>Accions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($rows)): ?>
                    <tr><td colspan="10">No s'han trobat moviments.</td></tr>
                <?php else: ?>
                    <?php foreach ($rows as $row): ?>
                    <tr>
                        <td><?php echo (int) $row['id_mov']; ?></td>
                        <td><?php echo htmlspecialchars($row['data'] ?? ''); ?></td>
                        <td><?php echo (int) $row['id_lot']; ?></td>
                        <td><?php echo htmlspecialchars($row['numero_lot'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['nom_comercial'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['quantitat'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['motiu'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['id_aplicacio'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['observacions'] ?? ''); ?></td>
                        <td>
                            <div class="table-actions">
                                <a href="moviment_estoc_detall.php?id_mov=<?php echo urlencode((string) ($row['id_mov'] ?? '')); ?>" title="Detall">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="../HTML/moviment_lot.html?id_lot=<?php echo urlencode((string) ($row['id_lot'] ?? '')); ?>" title="Nou moviment per aquest lot">
                                    <i class="fa-solid fa-plus"></i>
                                </a>
                                <a href="consulta_productes.php?numero_lot=<?php echo urlencode((string) ($row['numero_lot'] ?? '')); ?>" title="Veure lot a consulta productes">
                                    <i class="fa-solid fa-box"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
