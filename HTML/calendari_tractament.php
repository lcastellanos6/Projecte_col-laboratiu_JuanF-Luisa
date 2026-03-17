<?php

$conn = new mysqli("localhost","root","","web");

if ($conn->connect_error) {
    die("Error BD");
}

$conn->set_charset("utf8");

/* MES I ANY */

$mes = $_GET['mes'] ?? date('m');
$any = $_GET['any'] ?? date('Y');

$primer_dia = "$any-$mes-01";
$dies_mes = date('t', strtotime($primer_dia));
$dia_setmana = date('N', strtotime($primer_dia));

/* CONSULTA PLANS */

$plans = $conn->query("

SELECT
nom,
tipus,
plaga_malaltia_objectiu,
finestra_data_inici,
finestra_data_fi

FROM pla_tractament

");

/* INDEXAR PER DIA */

$cal = [];

while ($p = $plans->fetch_assoc()) {

$start = strtotime($p['finestra_data_inici']);
$end   = strtotime($p['finestra_data_fi']);

for ($d=$start; $d <= $end; $d = strtotime("+1 day",$d)){

if(date('m',$d)==$mes){

$dia = date('j',$d);

$cal[$dia][] = [

'tipus'=>strtolower($p['tipus']),
'nom'=>$p['nom'],

'info'=>"
Tipus: ".$p['tipus']."
Plaga: ".$p['plaga_malaltia_objectiu']."
".$p['finestra_data_inici']." → ".$p['finestra_data_fi']."

"

];

}

}

}

/* NAVEGACIÓ */

$prevMes = date('m', strtotime("-1 month", strtotime($primer_dia)));
$prevAny = date('Y', strtotime("-1 month", strtotime($primer_dia)));

$nextMes = date('m', strtotime("+1 month", strtotime($primer_dia)));
$nextAny = date('Y', strtotime("+1 month", strtotime($primer_dia)));

?>

<!DOCTYPE html>
<html lang="ca">

<head>

<meta charset="UTF-8">

<title>Calendari Tractaments</title>

<style>

body{
font-family:Arial;
background:#eef7ee;
padding:20px;
}

h1{
text-align:center;
color:#2e7d32;
}

.nav{
text-align:center;
margin-bottom:20px;
}

.nav a{
background:#2e7d32;
color:white;
padding:8px 14px;
text-decoration:none;
border-radius:6px;
margin:5px;
}

.calendar{

display:grid;

grid-template-columns:repeat(7,1fr);

gap:8px;

}

.header{

text-align:center;

font-weight:bold;

background:#dcedc8;

padding:8px;

border-radius:5px;

}

.day{

background:white;

border-radius:8px;

padding:6px;

min-height:130px;

box-shadow:0 2px 5px rgba(0,0,0,0.1);

position:relative;

}

.day-number{

font-weight:bold;

color:#444;

margin-bottom:5px;

}

.event{

font-size:12px;

padding:4px;

margin-top:3px;

border-radius:4px;

cursor:pointer;

}

.preventiu{

background:#c8e6c9;

border-left:4px solid #2e7d32;

}

.curatiu{

background:#ffcdd2;

border-left:4px solid #c62828;

}

.event:hover{

transform:scale(1.03);

}

</style>

</head>

<body>

<h1>🌱 Calendari de Plans de Tractament <?= "$mes / $any" ?></h1>

<div class="nav">

<a href="?mes=<?= $prevMes ?>&any=<?= $prevAny ?>">⬅ Mes anterior</a>

<a href="?mes=<?= date('m') ?>&any=<?= date('Y') ?>">📍 Avui</a>

<a href="?mes=<?= $nextMes ?>&any=<?= $nextAny ?>">Mes següent ➡</a>

</div>


<div class="calendar">

<?php

$dias=['Dl','Dm','Dc','Dj','Dv','Ds','Dg'];

foreach($dias as $d){

echo "<div class='header'>$d</div>";

}

for($i=1;$i<$dia_setmana;$i++){

echo "<div></div>";

}

for($dia=1;$dia<=$dies_mes;$dia++):

?>

<div class="day">

<div class="day-number"><?= $dia ?></div>

<?php

if(!empty($cal[$dia])){

foreach($cal[$dia] as $e){

?>

<div class="event <?= $e['tipus'] ?>" title="<?= htmlspecialchars($e['info']) ?>">

🌱 <?= htmlspecialchars($e['nom']) ?>

</div>

<?php

}

}

?>

</div>

<?php endfor; ?>

</div>

</body>

</html>