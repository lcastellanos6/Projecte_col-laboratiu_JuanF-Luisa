<?php
require_once __DIR__ . '/db.php';

$conn = db_connect();

$id_pla = trim($_GET['id_pla'] ?? '');
$id_operari = trim($_GET['id_operari'] ?? '');
$id_equip = trim($_GET['id_equip'] ?? '');
$metode = trim($_GET['metode'] ?? '');
$data_des = trim($_GET['data_des'] ?? '');
$data_fins = trim($_GET['data_fins'] ?? '');

$plans = [];
$operaris = [];
$equips = [];

$resPlans = $conn->query("SELECT id_pla, nom FROM pla_tractament ORDER BY nom ASC");
if ($resPlans) {
    while ($row = $resPlans->fetch_assoc()) {
        $plans[] = $row;
    }
    $resPlans->free();
}

$resOperaris = $conn->query("SELECT id_operari, nom FROM operari ORDER BY nom ASC");
if ($resOperaris) {
    while ($row = $resOperaris->fetch_assoc()) {
        $operaris[] = $row;
    }
    $resOperaris->free();
}

$resEquips = $conn->query("SELECT id_equip, tipus FROM equip ORDER BY tipus ASC");
if ($resEquips) {
    while ($row = $resEquips->fetch_assoc()) {
        $equips[] = $row;
    }
    $resEquips->free();
}

$sql = "
    SELECT
        a.id_aplicacio,
        a.id_pla,
        pt.nom AS pla_nom,
        a.data,
        a.hora_inici,
        a.hora_fi,
        a.metode,
        a.condicions_ambientals,
        a.id_operari,
        o.nom AS operari_nom,
        a.id_equip,
        e.tipus AS equip_tipus,
        a.observacions
    FROM aplicacio a
    LEFT JOIN pla_tractament pt ON pt.id_pla = a.id_pla
    LEFT JOIN operari o ON o.id_operari = a.id_operari
    LEFT JOIN equip e ON e.id_equip = a.id_equip
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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
                <a class="btn btn-primary" href="../HTML/aplicacio.php">Nova aplicació</a>
            </div>
        </div>
    </div>

    <div class="panel">
        <form method="get" class="form-grid-2">
            <label>Pla</label>
            <select name="id_pla">
                <option value="">(Qualsevol)</option>
                <?php foreach ($plans as $pla): ?>
                    <option value="<?php echo (int) $pla['id_pla']; ?>" <?php echo ($id_pla !== '' && (int) $id_pla === (int) $pla['id_pla']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($pla['nom']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Operari</label>
            <select name="id_operari">
                <option value="">(Qualsevol)</option>
                <?php foreach ($operaris as $operari): ?>
                    <option value="<?php echo (int) $operari['id_operari']; ?>" <?php echo ($id_operari !== '' && (int) $id_operari === (int) $operari['id_operari']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($operari['nom']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Equip</label>
            <select name="id_equip">
                <option value="">(Qualsevol)</option>
                <?php foreach ($equips as $equip): ?>
                    <option value="<?php echo (int) $equip['id_equip']; ?>" <?php echo ($id_equip !== '' && (int) $id_equip === (int) $equip['id_equip']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($equip['tipus']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

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
                        <th>Pla</th>
                        <th>Mètode</th>
                        <th>Hora inici</th>
                        <th>Hora fi</th>
                        <th>Operari</th>
                        <th>Equip</th>
                        <th>Condicions</th>
                        <th>Observacions</th>
                        <th>Accions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($rows)): ?>
                    <tr><td colspan="11">No s'han trobat aplicacions.</td></tr>
                <?php else: ?>
                    <?php foreach ($rows as $row): ?>
                    <tr>
                        <td><?php echo (int) $row['id_aplicacio']; ?></td>
                        <td><?php echo htmlspecialchars($row['data'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['pla_nom'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['metode'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['hora_inici'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['hora_fi'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['operari_nom'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['equip_tipus'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['condicions_ambientals'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['observacions'] ?? ''); ?></td>
                        <td>
                            <div class="table-actions">
                                <a href="aplicacio_detall.php?id_aplicacio=<?php echo urlencode((string) ($row['id_aplicacio'] ?? '')); ?>" title="Detall">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="consulta_aplicacio_productes.php?id_aplicacio=<?php echo urlencode((string) ($row['id_aplicacio'] ?? '')); ?>" title="Veure productes aplicats">
                                    <i class="fa-solid fa-flask-vial"></i>
                                </a>
                                <a href="../HTML/aplicacio_productes.php?id_aplicacio=<?php echo urlencode((string) ($row['id_aplicacio'] ?? '')); ?>" title="Afegir productes">
                                    <i class="fa-solid fa-plus"></i>
                                </a>
                                <a href="consulta_aplicacio_files.php?id_aplicacio=<?php echo urlencode((string) ($row['id_aplicacio'] ?? '')); ?>" title="Veure files tractades">
                                    <i class="fa-solid fa-list-check"></i>
                                </a>
                                <a href="consulta_moviment_estoc.php?id_aplicacio=<?php echo urlencode((string) ($row['id_aplicacio'] ?? '')); ?>" title="Veure consum d'estoc">
                                    <i class="fa-solid fa-truck-ramp-box"></i>
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
