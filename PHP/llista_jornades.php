<?php
$conn = new mysqli("localhost","root","","web");
if ($conn->connect_error) die("Error BD: " . $conn->connect_error);

// Consulta per hores totals, per setmana i per mes
$res = $conn->query("
SELECT 
  t.nom_complet AS nom,
  YEAR(j.data_hora_inici) AS any,
  MONTH(j.data_hora_inici) AS mes,
  WEEK(j.data_hora_inici, 1) AS setmana,
  SUM((TIMESTAMPDIFF(MINUTE, j.data_hora_inici, j.data_hora_fi) - COALESCE(j.minuts_pausa,0))/60) AS hores_totals
FROM jornada j
JOIN treballador t ON t.id_treballador = j.id_treballador
GROUP BY t.id_treballador, any, mes, setmana
ORDER BY t.nom_complet, any, mes, setmana
");

if (!$res) die("Error consulta: " . $conn->error);
?>
<!DOCTYPE html>
<html lang="ca">
<head>
<meta charset="UTF-8">
<title>Resum jornades</title>
<style>
body { font-family: Arial, sans-serif; background:#f5fff5; padding:20px; }
h2 { text-align:center; color:#2f7d2f; }
table { border-collapse:collapse; width:100%; margin-top:20px; }
th, td { border:1px solid #ccc; padding:8px; text-align:center; }
th { background:#eee; }
tr:nth-child(even) { background:#f9f9f9; }
</style>
</head>
<body>

<h2>📊 Resum hores treballades</h2>

<table>
<tr>
<th>Treballador</th>
<th>Any</th>
<th>Mes</th>
<th>Setmana</th>
<th>Hores totals</th>
</tr>

<?php while($r=$res->fetch_assoc()): ?>
<tr>
<td><?= htmlspecialchars($r['nom']) ?></td>
<td><?= $r['any'] ?></td>
<td><?= $r['mes'] ?></td>
<td><?= $r['setmana'] ?></td>
<td><?= round($r['hores_totals'],2) ?></td>
</tr>
<?php endwhile; ?>

</table>

</body>
</html>


