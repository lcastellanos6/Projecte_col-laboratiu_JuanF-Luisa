<?php
$conn = new mysqli("localhost","root","","web");
if($conn->connect_error) die("Error BD");

$id = $_GET['id'] ?? 1;
$mes = $_GET['mes'] ?? date('m');
$any = $_GET['any'] ?? date('Y');

$res = $conn->query("
SELECT 
  DATE(data_hora_inici) AS dia,
  COUNT(*) AS jornades
FROM jornada
WHERE id_treballador=$id
AND MONTH(data_hora_inici)=$mes
AND YEAR(data_hora_inici)=$any
GROUP BY dia
");

$dias = [];
while($r = $res->fetch_assoc()){
  $dias[$r['dia']] = $r['jornades'];
}

$primer = strtotime("$any-$mes-01");
$dies_mes = date('t',$primer);
$dia_setmana = date('N',$primer);
?>

<!DOCTYPE html>
<html lang="ca">
<head>
<meta charset="utf-8">
<title>Calendari jornades</title>
<style>
body{font-family:Arial;background:#f4fff4;padding:20px}
.calendar{display:grid;grid-template-columns:repeat(7,1fr);gap:5px}
.day{background:#fff;border:1px solid #ccc;padding:10px;height:80px}
.work{background:#c9f7c9}
.header{font-weight:bold;text-align:center}
</style>
</head>
<body>

<h2>Calendari jornades – <?= "$mes/$any" ?></h2>

<div class="calendar">
<?php
$dies = ['Dl','Dt','Dc','Dj','Dv','Ds','Dg'];
foreach($dies as $d) echo "<div class='header'>$d</div>";

for($i=1;$i<$dia_setmana;$i++) echo "<div></div>";

for($d=1;$d<=$dies_mes;$d++){
  $date = "$any-$mes-".str_pad($d,2,'0',STR_PAD_LEFT);
  $class = isset($dias[$date]) ? 'day work' : 'day';
  echo "<div class='$class'><b>$d</b>";
  if(isset($dias[$date])) echo "<br>{$dias[$date]} jornada/es";
  echo "</div>";
}
?>
</div>

</body>
</html>
