<?php
$conn = new mysqli("localhost","root","","web");
if ($conn->connect_error) die("Error BD: " . $conn->connect_error);
$conn->set_charset("utf8");

// Consulta: horas totales por trabajador, por semana y por mes
$sql = "
SELECT 
  t.id_treballador,
  t.nom_complet AS nom,
  YEAR(j.data_hora_inici) AS any,
  MONTH(j.data_hora_inici) AS mes,
  WEEK(j.data_hora_inici, 1) AS setmana,
  SUM((TIMESTAMPDIFF(MINUTE, j.data_hora_inici, j.data_hora_fi) - COALESCE(j.minuts_pausa,0))/60) AS hores_totals
FROM jornada j
JOIN treballador t ON t.id_treballador = j.id_treballador
GROUP BY t.id_treballador, any, mes, setmana
ORDER BY t.nom_complet, any, mes, setmana
";

$res = $conn->query($sql);
if (!$res) die("Error consulta: " . $conn->error);

// Organizar datos
$totals_mes = [];
$colors = [];
$worker_index = 0;
$color_palette = ["#2d742f","#2196f3","#ff9800","#9c27b0","#f44336","#00bcd4","#ffc107","#8bc34a","#e91e63"]; // Colores por trabajador

while($row = $res->fetch_assoc()) {
    $totals_mes[$row['nom']][$row['any']][$row['mes']][] = $row;

    // Asignar color fijo por trabajador
    if(!isset($colors[$row['nom']])){
        $colors[$row['nom']] = $color_palette[$worker_index % count($color_palette)];
        $worker_index++;
    }
}

?>
<!DOCTYPE html>
<html lang="ca">
<head>
<meta charset="UTF-8">
<title>Dashboard hores treballades per treballador</title>
<style>
body { font-family: Arial,sans-serif; padding:20px; background:#f0f9f0; }
h2 { text-align:center; color:#2f7d2f; }
table { border-collapse:collapse; width:100%; margin-top:20px; }
th, td { border:1px solid #ccc; padding:8px; text-align:center; }
th { background:#eee; }
tr.total { font-weight:bold; background:#dff0d8; }
</style>
</head>
<body>

<h2>📊 Dashboard hores treballades per treballador</h2>

<?php foreach($totals_mes as $nom => $anys): ?>
    <?php foreach($anys as $any => $mesos): ?>
        <h3 style="color:<?= $colors[$nom] ?>;"><?= htmlspecialchars($nom) ?> - Any <?= $any ?></h3>
        <table>
            <tr>
                <th>Mes</th>
                <th>Setmana</th>
                <th>Hores totals</th>
            </tr>
            <?php foreach($mesos as $mes => $setmanes): 
                $total_mes = 0;
            ?>
                <?php foreach($setmanes as $s): 
                    $total_mes += $s['hores_totals'];
                ?>
                <tr style="background:<?= $colors[$nom] ?>20;"> <!-- Transparencia para no saturar -->
                    <td><?= $mes ?></td>
                    <td><?= $s['setmana'] ?></td>
                    <td><?= round($s['hores_totals'],2) ?></td>
                </tr>
                <?php endforeach; ?>
                <tr class="total">
                    <td colspan="2">Total Mes <?= $mes ?></td>
                    <td><?= round($total_mes,2) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endforeach; ?>
<?php endforeach; ?>

</body>
</html>
