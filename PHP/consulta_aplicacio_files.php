<?php
require_once __DIR__ . '/db.php';

$conn = db_connect();

$id_aplicacio = trim($_GET['id_aplicacio'] ?? '');
$id_fila = trim($_GET['id_fila'] ?? '');
$estat = trim($_GET['estat'] ?? '');

$sql = "
    SELECT
        af.id_aplicacio,
        af.id_fila,
        f.id_increment AS id_plantacio,
        f.numero_fila,
        af.estat,
        af.volum_caldo_l,
        af.data_execucio,
        af.id_operari_execucio,
        o.nom AS operari_nom
    FROM aplicacio_fila af
    JOIN fila f ON f.id_fila = af.id_fila
    LEFT JOIN operari o ON o.id_operari = af.id_operari_execucio
";

$conds = [];
$params = [];
$types = '';

if ($id_aplicacio !== '' && ctype_digit($id_aplicacio)) {
    $conds[] = 'af.id_aplicacio = ?';
    $params[] = (int) $id_aplicacio;
    $types .= 'i';
}
if ($id_fila !== '' && ctype_digit($id_fila)) {
    $conds[] = 'af.id_fila = ?';
    $params[] = (int) $id_fila;
    $types .= 'i';
}
if ($estat !== '') {
    $conds[] = 'af.estat = ?';
    $params[] = $estat;
    $types .= 's';
}

if (!empty($conds)) {
    $sql .= ' WHERE ' . implode(' AND ', $conds);
}

$sql .= ' ORDER BY af.id_aplicacio DESC, af.id_fila ASC';

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
    <title>Consulta files tractades</title>
    <link rel="stylesheet" href="../HTML/styles.css">
</head>
<body>
<div class="page">
    <div class="page-header">
        <div class="panel-header">
            <div>
                <h1>Consulta de files tractades</h1>
                <p class="page-subtitle">Llistat de `aplicacio_fila` amb filtre per aplicació.</p>
            </div>
            <div class="page-header-actions">
                <a class="btn btn-primary" href="../HTML/aplicacio_fila.html<?php echo ($id_aplicacio !== '' ? '?id_aplicacio=' . urlencode($id_aplicacio) : ''); ?>">Afegir fila tractada</a>
            </div>
        </div>
    </div>

    <div class="panel">
        <form method="get" class="form-grid-2">
            <label>ID Aplicació</label>
            <input type="number" name="id_aplicacio" value="<?php echo htmlspecialchars($id_aplicacio); ?>">

            <label>ID Fila</label>
            <input type="number" name="id_fila" value="<?php echo htmlspecialchars($id_fila); ?>">

            <label>Estat</label>
            <select name="estat">
                <option value="">(Qualsevol)</option>
                <?php foreach (['Pendent', 'Fet', 'Parcial'] as $e): ?>
                    <option value="<?php echo $e; ?>" <?php echo ($estat === $e) ? 'selected' : ''; ?>><?php echo $e; ?></option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="btn btn-primary mt-2">Filtrar</button>
        </form>
    </div>

    <div class="panel">
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID Aplicació</th>
                        <th>ID Fila</th>
                        <th>ID Plantació</th>
                        <th>Núm. fila</th>
                        <th>Estat</th>
                        <th>Volum caldo (L)</th>
                        <th>Data execució</th>
                        <th>ID Operari</th>
                        <th>Operari</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($rows)): ?>
                    <tr><td colspan="9">No s'han trobat files tractades.</td></tr>
                <?php else: ?>
                    <?php foreach ($rows as $row): ?>
                    <tr>
                        <td><?php echo (int) $row['id_aplicacio']; ?></td>
                        <td><?php echo (int) $row['id_fila']; ?></td>
                        <td><?php echo htmlspecialchars($row['id_plantacio'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['numero_fila'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['estat'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['volum_caldo_l'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['data_execucio'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['id_operari_execucio'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['operari_nom'] ?? ''); ?></td>
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
