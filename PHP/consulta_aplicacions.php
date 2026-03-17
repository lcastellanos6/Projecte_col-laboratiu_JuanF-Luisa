<?php
require_once __DIR__ . '/db.php';

$conn = db_connect();

$id_pla = trim($_GET['id_pla'] ?? '');
$id_operari = trim($_GET['id_operari'] ?? '');
$id_equip = trim($_GET['id_equip'] ?? '');
$metode = trim($_GET['metode'] ?? '');
$data_des = trim($_GET['data_des'] ?? '');
$data_fins = trim($_GET['data_fins'] ?? '');

$sql = "
    SELECT
        a.id_aplicacio,
        a.id_pla,
        a.data,
        a.hora_inici,
        a.hora_fi,
        a.metode,
        a.condicions_ambientals,
        a.id_operari,
        o.nom AS operari_nom,
        a.id_equip,
        a.observacions
    FROM aplicacio a
    LEFT JOIN operari o ON o.id_operari = a.id_operari
";

$conds = [];
$params = [];
$types = '';

if ($id_pla !== '' && ctype_digit($id_pla)) {
    $conds[] = 'a.id_pla = ?';
    $params[] = (int) $id_pla;
    $types .= 'i';
}
if ($id_operari !== '' && ctype_digit($id_operari)) {
    $conds[] = 'a.id_operari = ?';
    $params[] = (int) $id_operari;
    $types .= 'i';
}
if ($id_equip !== '' && ctype_digit($id_equip)) {
    $conds[] = 'a.id_equip = ?';
    $params[] = (int) $id_equip;
    $types .= 'i';
}
if ($metode !== '') {
    $conds[] = 'a.metode = ?';
    $params[] = $metode;
    $types .= 's';
}
if ($data_des !== '') {
    $conds[] = 'a.data >= ?';
    $params[] = $data_des;
    $types .= 's';
}
if ($data_fins !== '') {
    $conds[] = 'a.data <= ?';
    $params[] = $data_fins;
    $types .= 's';
}

if (!empty($conds)) {
    $sql .= ' WHERE ' . implode(' AND ', $conds);
}

$sql .= ' ORDER BY a.data DESC, a.id_aplicacio DESC';

$rows = [];
$stmt = $conn->prepare($sql);
if ($stmt) {
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
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
    <title>Consulta aplicacions</title>
    <link rel="stylesheet" href="../HTML/styles.css">
</head>
<body>
<div class="page">
    <div class="page-header">
        <div class="panel-header">
            <div>
                <h1>Consulta d'aplicacions</h1>
                <p class="page-subtitle">Llistat d'aplicacions registrades amb filtres bàsics.</p>
            </div>
            <div class="page-header-actions">
                <a class="btn btn-primary" href="../HTML/aplicacio.html">Nova aplicació</a>
            </div>
        </div>
    </div>

    <div class="panel">
        <form method="get" class="form-grid-2">
            <label>ID Pla</label>
            <input type="number" name="id_pla" value="<?php echo htmlspecialchars($id_pla); ?>">

            <label>ID Operari</label>
            <input type="number" name="id_operari" value="<?php echo htmlspecialchars($id_operari); ?>">

            <label>ID Equip</label>
            <input type="number" name="id_equip" value="<?php echo htmlspecialchars($id_equip); ?>">

            <label>Mètode</label>
            <select name="metode">
                <option value="">(Qualsevol)</option>
                <?php foreach (['Fertirrigacio', 'Foliar', 'Sòl', 'Altres'] as $m): ?>
                    <option value="<?php echo $m; ?>" <?php echo ($metode === $m) ? 'selected' : ''; ?>><?php echo $m; ?></option>
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
                        <th>ID Aplicació</th>
                        <th>Data</th>
                        <th>ID Pla</th>
                        <th>Mètode</th>
                        <th>Hora inici</th>
                        <th>Hora fi</th>
                        <th>ID Operari</th>
                        <th>Operari</th>
                        <th>ID Equip</th>
                        <th>Condicions</th>
                        <th>Observacions</th>
                        <th>Accions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($rows)): ?>
                    <tr><td colspan="12">No s'han trobat aplicacions.</td></tr>
                <?php else: ?>
                    <?php foreach ($rows as $row): ?>
                    <tr>
                        <td><?php echo (int) $row['id_aplicacio']; ?></td>
                        <td><?php echo htmlspecialchars($row['data'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['id_pla'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['metode'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['hora_inici'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['hora_fi'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['id_operari'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['operari_nom'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['id_equip'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['condicions_ambientals'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['observacions'] ?? ''); ?></td>
                        <td>
                            <a href="aplicacio_detall.php?id_aplicacio=<?php echo urlencode((string) ($row['id_aplicacio'] ?? '')); ?>">Detall</a>
                            |
                            <a href="consulta_aplicacio_productes.php?id_aplicacio=<?php echo urlencode((string) ($row['id_aplicacio'] ?? '')); ?>">Veure productes aplicats</a>
                            |
                            <a href="../HTML/aplicacio_productes.html?id_aplicacio=<?php echo urlencode((string) ($row['id_aplicacio'] ?? '')); ?>">Afegir productes</a>
                            |
                            <a href="consulta_aplicacio_files.php?id_aplicacio=<?php echo urlencode((string) ($row['id_aplicacio'] ?? '')); ?>">Veure files tractades</a>
                            |
                            <a href="consulta_moviment_estoc.php?id_aplicacio=<?php echo urlencode((string) ($row['id_aplicacio'] ?? '')); ?>">Veure consum d'estoc</a>
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
