<?php
$conn = new mysqli("localhost","root","","web");
if ($conn->connect_error) die("Error BD: " . $conn->connect_error);

// Consulta corregida: usamos nom_complet y COALESCE para pausas nulas
$res = $conn->query("
SELECT 
  t.nom_complet AS nom,
  SUM(
    (TIMESTAMPDIFF(MINUTE, j.data_hora_inici, j.data_hora_fi) - COALESCE(j.minuts_pausa,0))/60
  ) AS hores
FROM jornada j
JOIN treballador t ON t.id_treballador = j.id_treballador
GROUP BY t.id_treballador, t.nom_complet
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
form {
    background:white; padding:20px; border-radius:8px;
    max-width:500px; margin:auto;
    box-shadow:0 2px 6px rgba(0,0,0,0.1);
}
label { font-weight:bold; margin-top:10px; display:block; }
input, select, textarea, button {
    width:100%; padding:8px; margin-top:5px;
    border-radius:4px; border:1px solid #ccc;
}
textarea { height:80px; resize:vertical; }
button {
    margin-top:15px; background:#2f7d2f; color:white;
    padding:10px; border:none; border-radius:5px;
    cursor:pointer;
}
button:hover { background:#3d9b3d; }
table{border-collapse:collapse;width:100%}
th,td{border:1px solid #ccc;padding:8px}
th{background:#eee}
</style>
</head>
<body>

<h2>📊 Resum hores treballades</h2>

<table>
<tr>
<th>Treballador</th>
<th>Hores totals</th>
</tr>
<?php while($r=$res->fetch_assoc()): ?>
<tr>
<td><?= htmlspecialchars($r['nom']) ?></td>
<td><?= round($r['hores'],2) ?></td>
</tr>
<?php endwhile; ?>
</table>

</body>
</html>


