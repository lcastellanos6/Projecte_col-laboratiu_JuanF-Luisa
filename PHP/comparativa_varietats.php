<?php
$conn = new mysqli("localhost", "root", "", "web");
if ($conn->connect_error) die("Error BD: " . $conn->connect_error);
$conn->set_charset("utf8");

// FILTRO POR AÑO (opcional)
$year_filter = isset($_GET['any']) ? intval($_GET['any']) : null;
$where = $year_filter ? "WHERE YEAR(c.data_inici) = $year_filter" : "";

// CONSULTA: producción por parcela y variedad
$sql = "
SELECT 
    p.id_plantacio,
    s.nom AS sector_nom,
    v.nom_comu AS varietat_nom,
    p.num_arbres_total,
    IFNULL(SUM(c.quantitat_total),0) AS total_collita
FROM plantacio p
JOIN sector s ON p.id_sector = s.id_sector
JOIN varietat v ON p.id_varietat = v.id_varietat
LEFT JOIN collita c ON c.plantacio_id = p.id_plantacio
$where
GROUP BY p.id_plantacio, v.nom_comu, s.nom, p.num_arbres_total
ORDER BY s.nom, p.id_plantacio, v.nom_comu
";

$result = $conn->query($sql);
if(!$result) die("Error SQL: ".$conn->error);

// Preparar datos
$parcelas = [];
$varietats = [];
while($row = $result->fetch_assoc()){
    $parcelas[$row['id_plantacio']] = [
        'sector' => $row['sector_nom'],
        'arboles' => $row['num_arbres_total']
    ];
    $varietats[$row['id_plantacio']][$row['varietat_nom']] = (float)$row['total_collita'];
}

// Etiquetas y datasets para Chart.js
$labels = [];
$datasets = [];
$colores = ["#2f7d2f","#55a455","#7fc97f","#a8d5a2","#cce5cc"];
$varietat_names = [];

foreach($varietats as $id_plantacio => $data){
    $labels[] = "Parcela $id_plantacio";
    foreach($data as $varietat => $total){
        if(!in_array($varietat, $varietat_names)) $varietat_names[] = $varietat;
    }
}

// Crear dataset por variedad
$datasets = [];
foreach($varietat_names as $index => $varietat){
    $data = [];
    foreach($varietats as $id_plantacio => $v_data){
        $data[] = isset($v_data[$varietat]) ? $v_data[$varietat] : 0;
    }
    $datasets[] = [
        "label" => $varietat,
        "data" => $data,
        "backgroundColor" => $colores[$index % count($colores)]
    ];
}
?>

<!DOCTYPE html>
<html lang="ca">
<head>
<meta charset="UTF-8">
<title>Comparativa Parcel·les i Varietats</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="../HTML/styles.css">
<style>
body{font-family:Arial,sans-serif;padding:20px;background:#fdfdfd;color:#333}
h1,h2{color:#2f7d2f;margin-bottom:15px}
table{border-collapse:collapse;width:100%;margin-top:20px;background:#fff;box-shadow:0 2px 6px rgba(0,0,0,0.05)}
th,td{border:1px solid #ccc;padding:10px;text-align:left}
th{background:#eee}
canvas{margin-top:20px;background:#fff;padding:10px;border-radius:6px;box-shadow:0 2px 6px rgba(0,0,0,0.05)}
form{margin-bottom:20px}
select,input{padding:6px;margin-top:3px;border-radius:4px;border:1px solid #ccc}
button{padding:8px 14px;border:none;border-radius:6px;background:#2f7d2f;color:#fff;cursor:pointer;font-weight:bold;margin-left:10px}
button:hover{background:#256b25}
</style>
</head>
<body>

<h1>📊 Comparativa Parcel·les i Varietats</h1>

<!-- FILTRO AÑO -->
<form method="get">
    <label>Filtrar per any:</label>
    <input type="number" name="any" value="<?= $year_filter ?? '' ?>" placeholder="2026">
    <button>Filtrar</button>
</form>

<!-- GRÁFICA -->
<h2>Producció per Parcela i Varietat</h2>
<canvas id="graficaVarietats" style="max-width:900px;"></canvas>

<!-- TABLA RESUMEN -->
<h2>Taula Resum</h2>
<table>
<tr>
    <th>Parcela</th>
    <th>Sector</th>
    <th>Arbres</th>
    <?php foreach($varietat_names as $v): ?>
    <th><?= htmlspecialchars($v) ?></th>
    <?php endforeach; ?>
</tr>
<?php foreach($varietats as $id_plantacio => $v_data): ?>
<tr>
    <td><?= $id_plantacio ?></td>
    <td><?= htmlspecialchars($parcelas[$id_plantacio]['sector']) ?></td>
    <td><?= $parcelas[$id_plantacio]['arboles'] ?></td>
    <?php foreach($varietat_names as $v): ?>
    <td><?= isset($v_data[$v]) ? $v_data[$v] : '0' ?></td>
    <?php endforeach; ?>
</tr>
<?php endforeach; ?>
</table>

<script>
const ctx = document.getElementById('graficaVarietats').getContext('2d');
const data = {
    labels: <?= json_encode($labels) ?>,
    datasets: <?= json_encode($datasets) ?>
};
new Chart(ctx,{
    type:'bar',
    data:data,
    options:{
        responsive:true,
        plugins:{legend:{position:'top'}},
        scales:{y:{beginAtZero:true,title:{display:true,text:'Kg / unitats'}}}
    }
});
</script>

</body>
</html>
