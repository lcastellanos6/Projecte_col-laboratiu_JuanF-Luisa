<?php
$conn = new mysqli("localhost", "root", "", "web");
if ($conn->connect_error) die("Error BD: " . $conn->connect_error);
$conn->set_charset("utf8");

// CONSULTA SEGURA: KPIs por parcela
$datosCollites = $conn->query("
    SELECT 
        p.id_plantacio,
        s.nom AS sector_nom,
        p.num_arbres_total,
        IFNULL(SUM(c.quantitat_total),0) AS total_collita,
        COUNT(c.collita_id) AS num_collites
    FROM plantacio p
    LEFT JOIN collita c ON c.plantacio_id = p.id_plantacio
    JOIN sector s ON p.id_sector = s.id_sector
    GROUP BY p.id_plantacio, s.nom, p.num_arbres_total
    ORDER BY s.nom, p.id_plantacio
");

if(!$datosCollites){
    die("Error en consulta SQL: " . $conn->error);
}

// Preparar datos para mostrar y gráficas
$parcelas = [];
while($row = $datosCollites->fetch_assoc()) {
    $parcelas[] = [
        'id' => $row['id_plantacio'],
        'sector' => $row['sector_nom'],
        'arboles' => $row['num_arbres_total'],
        'total' => (float)$row['total_collita'],
        'num_collites' => (int)$row['num_collites'],
        'prod_por_arbol' => $row['num_arbres_total'] ? round($row['total_collita'] / $row['num_arbres_total'], 2) : 0
    ];
}

// Datos para Chart.js
$labels = array_map(fn($p) => "Parcela ".$p['id']." (".$p['sector'].")", $parcelas);
$totales = array_map(fn($p) => $p['total'], $parcelas);
$prod_arbol = array_map(fn($p) => $p['prod_por_arbol'], $parcelas);
?>
<!DOCTYPE html>
<html lang="ca">
<head>
<meta charset="UTF-8">
<title>Anàlisi de Rendiments</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="../HTML/styles.css">
<style>
/* Cuerpo y tipografía */
body {
    font-family: Arial, sans-serif;
    padding: 20px;
    background: #fdfdfd;
    color: #333;
}

/* Formularios */
form {
    background: #f5f5f5;
    padding: 20px;
    border-radius: 8px;
    max-width: 900px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}
label {
    display: block;
    margin-top: 12px;
    font-weight: bold;
}
input, select, textarea {
    width: 100%;
    padding: 8px;
    margin-top: 4px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
}

/* Botones */
button {
    margin-top: 15px;
    padding: 10px 16px;
    border: none;
    cursor: pointer;
    border-radius: 6px;
    font-weight: bold;
    transition: background 0.3s;
}
.btn {
    background-color: #2f7d2f;
    color: #fff;
}
.btn:hover {
    background-color: #256b25;
}
.btn-sec {
    background-color: #555;
    color: #fff;
}
.btn-sec:hover {
    background-color: #333;
}

/* Tablas */
table {
    border-collapse: collapse;
    width: 100%;
    margin-top: 20px;
    background: #fff;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
}
th, td {
    border: 1px solid #ccc;
    padding: 10px;
    text-align: left;
}
th {
    background: #eee;
}

/* Ocultar/mostrar */
.hidden {
    display: none;
}

/* Gráficas */
canvas {
    margin-top: 20px;
    background: #fff;
    padding: 10px;
    border-radius: 6px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
}
</style>

</head>
<body>

</style>
</head>
<body>

<h1>📊 Anàlisi de Rendiments i Comparatives</h1>

<h2>KPIs per Parcela</h2>
<div class="table-scroll">
<table class="table">
<tr>
    <th>Parcela</th>
    <th>Sector</th>
    <th>Total Collita</th>
    <th>Número de collites</th>
    <th>Arbres</th>
    <th>Producció per arbre</th>
</tr>
<?php foreach($parcelas as $p): ?>
<tr>
    <td><?= $p['id'] ?></td>
    <td><?= htmlspecialchars($p['sector']) ?></td>
    <td><?= $p['total'] ?></td>
    <td><?= $p['num_collites'] ?></td>
    <td><?= $p['arboles'] ?></td>
    <td><?= $p['prod_por_arbol'] ?></td>
</tr>
<?php endforeach; ?>
</table>
</div>

<h2>Comparativa de Producció per Parcela</h2>
<canvas id="graficaTotal" style="max-width:800px;"></canvas>

<h2>Producció per Arbre</h2>
<canvas id="graficaPorArbol" style="max-width:800px;"></canvas>

<script>
const labels = <?= json_encode($labels) ?>;
const totales = <?= json_encode($totales) ?>;
const prodArbol = <?= json_encode($prod_arbol) ?>;

new Chart(document.getElementById('graficaTotal'), {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: 'Total Collita',
            data: totales,
            backgroundColor: '#7bbc31'
        }]
    },
    options: {
        responsive:true,
        plugins:{legend:{display:false}},
        scales:{y:{beginAtZero:true,title:{display:true,text:'Kg / unitats'}}}
    }
});

new Chart(document.getElementById('graficaPorArbol'), {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: 'Producció per arbre',
            data: prodArbol,
            backgroundColor: '#208b5b'
        }]
    },
    options: {
        responsive:true,
        plugins:{legend:{display:false}},
        scales:{y:{beginAtZero:true,title:{display:true,text:'Kg / arbre'}}}
    }
});
</script>

</body>
</html>

