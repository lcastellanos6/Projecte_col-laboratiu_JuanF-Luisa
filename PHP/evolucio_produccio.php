<?php
$conn = new mysqli("localhost", "root", "", "web");
if ($conn->connect_error) {
    die("Error BD: " . $conn->connect_error);
}
$conn->set_charset("utf8");

// Filtro por parcela (opcional)
$plantacio_id = isset($_GET['plantacio']) && $_GET['plantacio'] !== ''
    ? intval($_GET['plantacio'])
    : null;

$where = $plantacio_id ? "WHERE p.id_plantacio = $plantacio_id" : "";

// Parcelas para selector
$parceles = $conn->query("
    SELECT p.id_plantacio, s.nom AS sector
    FROM plantacio p
    JOIN sector s ON p.id_sector = s.id_sector
    ORDER BY s.nom
");

// Consulta evolución anual (ARREGLADA)
$sql = "
SELECT
    YEAR(c.data_inici) AS any,
    v.nom_comu AS varietat,
    SUM(c.quantitat_total) AS total
FROM collita c
JOIN plantacio p ON c.plantacio_id = p.id_plantacio
JOIN varietat v ON p.id_varietat = v.id_varietat
$where
GROUP BY any, varietat
ORDER BY any
";

$result = $conn->query($sql);
if (!$result) {
    die("Error SQL: " . $conn->error);
}

// Preparar datos
$anys = [];
$varietats = [];
$data = [];

while ($row = $result->fetch_assoc()) {
    $any = $row['any'];
    $var = $row['varietat'];
    $total = (float)$row['total'];

    if (!in_array($any, $anys)) $anys[] = $any;
    if (!in_array($var, $varietats)) $varietats[] = $var;

    $data[$var][$any] = $total;
}

// Datasets para Chart.js
$colors = ["#2f7d2f", "#4caf50", "#81c784", "#a5d6a7", "#66bb6a"];
$datasets = [];

foreach ($varietats as $i => $var) {
    $valors = [];
    foreach ($anys as $a) {
        $valors[] = $data[$var][$a] ?? 0;
    }

    $datasets[] = [
        "label" => $var,
        "data" => $valors,
        "borderColor" => $colors[$i % count($colors)],
        "backgroundColor" => $colors[$i % count($colors)],
        "fill" => false,
        "tension" => 0.3
    ];
}
?>

<!DOCTYPE html>
<html lang="ca">
<head>
<meta charset="UTF-8">
<title>Evolució de la Producció</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="../HTML/styles.css">
<style>
body{font-family:Arial;padding:20px;background:#fdfdfd}
h1,h2{color:#2f7d2f}
form{margin-bottom:20px}
select,button{padding:6px;border-radius:5px}
button{background:#2f7d2f;color:#fff;border:none;margin-left:10px;cursor:pointer}
table{border-collapse:collapse;width:100%;margin-top:20px;background:#fff}
th,td{border:1px solid #ccc;padding:8px}
th{background:#eee}
canvas{background:#fff;padding:15px;border-radius:8px;box-shadow:0 2px 6px rgba(0,0,0,0.1)}
</style>
</head>
<body>

<h1>📈 Evolució Històrica de la Producció</h1>

<form method="get">
    <label>Parcel·la:</label>
    <select name="plantacio">
        <option value="">Totes</option>
        <?php while ($p = $parceles->fetch_assoc()): ?>
            <option value="<?= $p['id_plantacio'] ?>"
                <?= ($plantacio_id == $p['id_plantacio']) ? 'selected' : '' ?>>
                Parcela <?= $p['id_plantacio'] ?> (<?= htmlspecialchars($p['sector']) ?>)
            </option>
        <?php endwhile; ?>
    </select>
    <button>Filtrar</button>
</form>

<canvas id="graficaEvolucio" style="max-width:900px;"></canvas>

<h2>📋 Producció per Any</h2>
<table>
<tr>
    <th>Varietat</th>
    <?php foreach ($anys as $a): ?>
        <th><?= $a ?></th>
    <?php endforeach; ?>
</tr>

<?php foreach ($varietats as $v): ?>
<tr>
    <td><?= htmlspecialchars($v) ?></td>
    <?php foreach ($anys as $a): ?>
        <td><?= $data[$v][$a] ?? 0 ?></td>
    <?php endforeach; ?>
</tr>
<?php endforeach; ?>
</table>

<script>
new Chart(document.getElementById('graficaEvolucio'), {
    type: 'line',
    data: {
        labels: <?= json_encode($anys) ?>,
        datasets: <?= json_encode($datasets) ?>
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top' } },
        scales: { y: { beginAtZero: true } }
    }
});
</script>

</body>
</html>
