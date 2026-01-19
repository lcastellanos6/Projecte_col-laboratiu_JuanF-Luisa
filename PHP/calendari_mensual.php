<?php
$conn = new mysqli("localhost", "root", "", "web");
if ($conn->connect_error) die("Error BD");

// Mes y año
$mes = $_GET['mes'] ?? date('m');
$any = $_GET['any'] ?? date('Y');

$primer_dia = "$any-$mes-01";
$dies_mes = date('t', strtotime($primer_dia));
$dia_setmana = date('N', strtotime($primer_dia)); // 1 = lunes

// ================= FESTIVOS ESPAÑA + CATALUNYA =================
$festius = [

    // ESPAÑA
    "$any-01-01" => "Any Nou",
    "$any-01-06" => "Reis",
    "$any-05-01" => "Dia del Treball",
    "$any-08-15" => "Assumpció",
    "$any-10-12" => "Festa Nacional d'Espanya",
    "$any-11-01" => "Tots Sants",
    "$any-12-06" => "Constitució",
    "$any-12-08" => "Immaculada",
    "$any-12-25" => "Nadal",

    // CATALUNYA
    "$any-06-24" => "Sant Joan",
    "$any-09-11" => "Diada Nacional de Catalunya",
    "$any-12-26" => "Sant Esteve"
];

// ================= JORNADAS =================
$jornades = $conn->query("
    SELECT j.data_hora_inici, j.data_hora_fi, t.nom_complet
    FROM jornada j
    JOIN treballador t ON t.id_treballador = j.id_treballador
    WHERE MONTH(j.data_hora_inici) = $mes
      AND YEAR(j.data_hora_inici) = $any
");

// ================= AUSENCIAS =================
$absencies = $conn->query("
    SELECT a.*, t.nom_complet
    FROM absencia a
    JOIN treballador t ON t.id_treballador = a.id_treballador
    WHERE (
        MONTH(a.data_inici) = $mes OR
        MONTH(a.data_fi) = $mes
    )
    AND YEAR(a.data_inici) = $any
");

// ================= INDEXAR POR DÍA =================
$cal = [];

// Jornadas
while ($j = $jornades->fetch_assoc()) {
    $dia = date('j', strtotime($j['data_hora_inici']));
    $cal[$dia][] = [
        'tipus' => 'jornada',
        'text' => $j['nom_complet']." (".
            date('H:i', strtotime($j['data_hora_inici'])) . "-" .
            date('H:i', strtotime($j['data_hora_fi'])) . ")"
    ];
}

// Ausencias
while ($a = $absencies->fetch_assoc()) {
    $start = strtotime($a['data_inici']);
    $end   = strtotime($a['data_fi']);

    for ($d = $start; $d <= $end; $d = strtotime("+1 day", $d)) {
        if (date('m', $d) == $mes) {
            $dia = date('j', $d);
            $cal[$dia][] = [
                'tipus' => strtolower($a['tipus']),
                'text'  => $a['nom_complet']." (".$a['tipus'].")"
            ];
        }
    }
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
<title>Calendari de jornades</title>
<style>
body { font-family: Arial; padding: 20px; }

.calendar {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 6px;
}

.day {
    border: 1px solid #ccc;
    min-height: 120px;
    padding: 5px;
    font-size: 12px;
}

.header {
    background: #eee;
    font-weight: bold;
    text-align: center;
    padding: 5px;
}

.jornada { background: #d9ecff; margin: 2px 0; padding: 3px; border-radius: 3px; }
.vacances { background: #d4edda; margin: 2px 0; padding: 3px; border-radius: 3px; }
.baixa { background: #f8d7da; margin: 2px 0; padding: 3px; border-radius: 3px; }
.permis, .permís { background: #fff3cd; margin: 2px 0; padding: 3px; border-radius: 3px; }

.festiu {
    background: #ffe0e0;
    border: 1px solid #ff9999;
    color: #900;
    font-weight: bold;
    padding: 3px;
    margin-top: 4px;
    border-radius: 4px;
}

.nav {
    margin-bottom: 15px;
}
.nav a {
    text-decoration: none;
    padding: 6px 10px;
    background: #2f7d2f;
    color: white;
    border-radius: 5px;
    margin-right: 5px;
}
</style>
</head>

<body>

<h1>📅 Calendari <?= "$mes / $any" ?></h1>

<div class="nav">
    <a href="?mes=<?= $prevMes ?>&any=<?= $prevAny ?>">⬅ Mes anterior</a>
    <a href="?mes=<?= date('m') ?>&any=<?= date('Y') ?>">📍 Avui</a>
    <a href="?mes=<?= $nextMes ?>&any=<?= $nextAny ?>">Mes següent ➡</a>
</div>

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
$dataActual = sprintf('%04d-%02d-%02d', $any, $mes, $dia);
if (isset($festius[$dataActual])) {
    echo "<div class='festiu'>🎉 ".$festius[$dataActual]."</div>";
}

if (!empty($cal[$dia])):
    foreach ($cal[$dia] as $e):
?>
<div class="<?= $e['tipus'] ?>">
<?= htmlspecialchars($e['text']) ?>
</div>
<?php
    endforeach;
endif;
?>
</div>
<?php endfor; ?>
</div>

</body>
</html>


