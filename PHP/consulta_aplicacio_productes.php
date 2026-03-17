<?php
require_once __DIR__ . '/db.php';

$conn = db_connect();

$id_aplicacio = trim($_GET['id_aplicacio'] ?? '');

$sql = "
    SELECT
        ap.id_aplicacio,
        a.data AS data_aplicacio,
        a.metode,
        ap.id_producte,
        p.nom_comercial,
        ap.quantitat,
        ap.unitat,
        ap.concentracio,
        ap.lot_referencia
    FROM aplicacio_producte ap
    JOIN aplicacio a ON a.id_aplicacio = ap.id_aplicacio
    JOIN producte p ON p.id_producte = ap.id_producte
";

$params = [];
$types = '';
if ($id_aplicacio !== '' && ctype_digit($id_aplicacio)) {
    $sql .= " WHERE ap.id_aplicacio = ?";
    $params[] = (int) $id_aplicacio;
    $types .= 'i';
}

$sql .= " ORDER BY ap.id_aplicacio DESC, ap.id_producte ASC";

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
    <title>Consulta productes aplicats</title>
    <link rel="stylesheet" href="../HTML/styles.css">
</head>
<body>
<div class="page">
    <div class="page-header">
        <div class="panel-header">
            <div>
                <h1>Consulta de productes aplicats</h1>
                <p class="page-subtitle">Llistat de registres d'aplicacio_producte, amb filtre per ID d'aplicació.</p>
            </div>
            <div class="page-header-actions">
                <a class="btn btn-primary" href="../HTML/aplicacio_productes.html<?php echo ($id_aplicacio !== '' ? '?id_aplicacio=' . urlencode($id_aplicacio) : ''); ?>">Afegir producte aplicat</a>
            </div>
        </div>
    </div>

    <div class="panel">
        <form method="get" class="form-grid-2">
            <label>ID Aplicació</label>
            <input type="number" name="id_aplicacio" value="<?php echo htmlspecialchars($id_aplicacio); ?>">

            <button type="submit" class="btn btn-primary mt-2">Filtrar</button>
        </form>
    </div>

    <div class="panel">
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID Aplicació</th>
                        <th>Data aplicació</th>
                        <th>Mètode</th>
                        <th>ID Producte</th>
                        <th>Nom comercial</th>
                        <th>Quantitat</th>
                        <th>Unitat</th>
                        <th>Concentració</th>
                        <th>Lot referència</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($rows)): ?>
                    <tr><td colspan="9">No s'han trobat productes aplicats.</td></tr>
                <?php else: ?>
                    <?php foreach ($rows as $row): ?>
                    <tr>
                        <td><?php echo (int) $row['id_aplicacio']; ?></td>
                        <td><?php echo htmlspecialchars($row['data_aplicacio'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['metode'] ?? ''); ?></td>
                        <td><?php echo (int) $row['id_producte']; ?></td>
                        <td><?php echo htmlspecialchars($row['nom_comercial'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['quantitat'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['unitat'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['concentracio'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['lot_referencia'] ?? ''); ?></td>
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
