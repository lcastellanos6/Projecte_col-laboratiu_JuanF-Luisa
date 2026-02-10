<?php
$conn = new mysqli("localhost","root","","web");
if ($conn->connect_error) die("Error BD");
$conn->set_charset("utf8");

// Mes y año
$mes = $_GET['mes'] ?? date('m');
$any = $_GET['any'] ?? date('Y');

$primer_dia = "$any-$mes-01";
$dies_mes = date('t', strtotime($primer_dia));
$dia_setmana = date('N', strtotime($primer_dia)); // 1 = lunes

// Obtener tratamientos del mes
$tractaments = $conn->query("
    SELECT t.*, f.numero_fila
    FROM tractament t
    JOIN fila f ON f.id_fila = t.id_fila
    WHERE MONTH(t.data) = $mes AND YEAR(t.data) = $any
");

// Indexar por día y preparar alertas
$cal = [];
$avui = date('Y-m-d');
$alertas = []; // Para el mensaje emergente

while ($t = $tractaments->fetch_assoc()) {
    $dia = date('j', strtotime($t['data']));
    $tipus = strtolower($t['tipus']);
    if (!in_array($tipus, ['fertilització','riego','poda'])) $tipus = 'altres';

    $dataDia = $t['data'];
    $clase_alerta = '';
    $diff = (strtotime($dataDia) - strtotime($avui))/86400;

    if ($diff == 0) {
        $clase_alerta = 'today';
        $alertas[] = "Hoy: ".$t['tipus']." en fila ".$t['numero_fila'];
    }
    elseif ($diff > 0 && $diff <= 2) {
        $clase_alerta = 'upcoming';
        $alertas[] = "Próximo: ".$t['tipus']." en fila ".$t['numero_fila']." (".$t['data'].")";
    }

    $cal[$dia][] = [
        'tipus' => $tipus,
        'text'  => $t['tipus'],
        'producte' => $t['producte'],
        'dosi' => $t['dosi_ml'],
        'fila' => $t['numero_fila'],
        'alerta' => $clase_alerta
    ];
}

// Navegación meses
$prevMes = date('m', strtotime("-1 month", strtotime($primer_dia)));
$prevAny = date('Y', strtotime("-1 month", strtotime($primer_dia)));
$nextMes = date('m', strtotime("+1 month", strtotime($primer_dia)));
$nextAny = date('Y', strtotime("+1 month", strtotime($primer_dia)));
?>
<!DOCTYPE html>
<html lang="ca">
<head>
<meta charset="UTF-8">
<title>Calendari de Tractaments amb Alertes</title>
<style>
body { font-family: Arial; padding: 20px; background:#f4fff4; }
h1 { text-align:center; color:#2e7d32; margin-bottom:15px; }

.nav { margin-bottom: 15px; text-align:center; }
.nav a {
    text-decoration: none;
    padding: 6px 10px;
    background: #2f7d2f;
    color: white;
    border-radius: 5px;
    margin: 0 5px;
}

.calendar {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 6px;
}

.header {
    background: #eee;
    font-weight: bold;
    text-align: center;
    padding: 5px;
    border-radius:3px;
}

.day {
    border: 1px solid #ccc;
    min-height: 120px;
    padding: 5px;
    font-size: 12px;
    background:white;
    border-radius:5px;
    position:relative;
}

.day strong { display:block; margin-bottom:5px; }

.today { border: 2px solid #2e7d32; }
.upcoming { background: #fff3cd; }

/* Colores y iconos por tipo */
.fertilització { background: #FF9800; color: white; padding: 3px; margin:2px 0; border-radius:3px; cursor:pointer; }
.fertilització::before { content:"🌿 "; }
.riego        { background: #2196F3; color: white; padding: 3px; margin:2px 0; border-radius:3px; cursor:pointer; }
.riego::before { content:"💧 "; }
.poda         { background: #9C27B0; color: white; padding: 3px; margin:2px 0; border-radius:3px; cursor:pointer; }
.poda::before { content:"✂ "; }
.altres       { background: #4CAF50; color: white; padding: 3px; margin:2px 0; border-radius:3px; cursor:pointer; }
.altres::before { content:"🔹 "; }

/* Tooltip flotante */
.tooltip {
    position: absolute;
    background: #333;
    color: #fff;
    padding: 6px 10px;
    border-radius: 5px;
    font-size: 12px;
    display: none;
    z-index: 100;
    max-width: 200px;
}
</style>
</head>
<body>

<!-- Mantener el encabezado y navegación -->
<h1>📅 Calendari de Tractaments <?= "$mes / $any" ?></h1>

<div class="nav">
    <a href="?mes=<?= $prevMes ?>&any=<?= $prevAny ?>">⬅ Mes anterior</a>
    <a href="?mes=<?= date('m') ?>&any=<?= date('Y') ?>">📍 Avui</a>
    <a href="?mes=<?= $nextMes ?>&any=<?= $nextAny ?>">Mes següent ➡</a>
</div>

<!-- Calendario -->
<div class="calendar">
<?php
$dias = ['Dl','Dm','Dc','Dj','Dv','Ds','Dg'];
foreach ($dias as $d) echo "<div class='header'>$d</div>";

for ($i = 1; $i < $dia_setmana; $i++) echo "<div></div>";

for ($dia = 1; $dia <= $dies_mes; $dia++):
?>
<div class="day">
<strong><?= $dia ?></strong>

<?php
if (!empty($cal[$dia])):
    foreach ($cal[$dia] as $e):
        $icon_alerta = ($e['alerta'] == 'today' || $e['alerta'] == 'upcoming') ? " 🔔" : "";
?>
<div class="<?= $e['tipus'] ?> <?= $e['alerta'] ?>"
     data-fila="<?= htmlspecialchars($e['fila']) ?>"
     data-producte="<?= htmlspecialchars($e['producte']) ?>"
     data-dosi="<?= htmlspecialchars($e['dosi']) ?>">
<?= htmlspecialchars($e['text']) ?><?= $icon_alerta ?>
</div>
<?php
    endforeach;
endif;
?>
</div>
<?php endfor; ?>
</div>

<!-- Tooltip flotante -->
<div class="tooltip" id="tooltip"></div>

<script>
// Tooltip JS
const tooltip = document.getElementById('tooltip');
document.querySelectorAll('.day div').forEach(el => {
    el.addEventListener('mouseenter', e => {
        tooltip.innerHTML = `
        Fila: ${el.dataset.fila}<br>
        Producte: ${el.dataset.producte}<br>
        Dosi: ${el.dataset.dosi} ml
        `;
        tooltip.style.display = 'block';
        const rect = el.getBoundingClientRect();
        tooltip.style.top = (rect.top + window.scrollY + el.offsetHeight) + 'px';
        tooltip.style.left = (rect.left + window.scrollX) + 'px';
    });
    el.addEventListener('mouseleave', e => {
        tooltip.style.display = 'none';
    });
});

// Alertas emergentes
let alertas = <?= json_encode($alertas) ?>;
if (alertas.length > 0) {
    let msg = alertas.join("\n");
    alert("⚠️ Alertes:\n\n" + msg);
}
</script>

</body>
</html>







