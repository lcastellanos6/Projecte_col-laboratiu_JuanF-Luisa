<?php
$conn = new mysqli("localhost","root","","web");
if ($conn->connect_error) die("Error BD");

$res = $conn->query("SELECT * FROM tasca ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="ca">
<head>
<meta charset="UTF-8">
<title>Gestió de tasques</title>
<link rel="stylesheet" href="../HTML/styles.css">
<style>
body{font-family:Arial;padding:20px}
form{background:#f5f5f5;padding:15px;border-radius:5px}
label{display:block;margin-top:10px}
input,select,textarea{width:100%;padding:6px}
button{margin-top:15px;padding:10px;border:none;cursor:pointer}
.btn{background:#2f7d2f;color:#fff}
.btn-sec{background:#555;color:#fff}
table{border-collapse:collapse;width:100%;margin-top:20px}
th,td{border:1px solid #ccc;padding:8px}
th{background:#eee}
a{text-decoration:none;margin-right:8px}
.hidden{display:none}
</style>
</head>
<body>

<h1>🛠️ Gestió de tasques</h1>

<!-- FORMULARIO NUEVA TAREA -->
<h2>➕ Nova tasca</h2>
<form action="tasca_guardar.php" method="post">
<label>Nom de la tasca *</label>
<input type="text" name="nom_tasca" required>

<label>Tipus de tasca</label>
<input type="text" name="tipus_tasca">

<label>Data inici</label>
<input type="date" name="data_inici">

<label>Data final</label>
<input type="date" name="data_final">

<label>Durada estimada</label>
<input type="text" name="durada_estimada">

<label>Personal requerit</label>
<input type="number" name="personal_requerit">

<label>Equipament necessari</label>
<textarea name="equipament_necessari"></textarea>

<label>Instruccions</label>
<textarea name="instruccions"></textarea>

<label>Dependències</label>
<textarea name="dependencies"></textarea>

<label>Estat</label>
<select name="estat">
  <option value="Planificada">Planificada</option>
  <option value="En curs">En curs</option>
  <option value="Feta">Feta</option>
  <option value="Cancel·lada">Cancel·lada</option>
</select>

<button class="btn">Guardar tasca</button>
</form>

<!-- BOTÓN -->
<h2>
<button class="btn-sec" onclick="toggleTasques()">📋 Tasques existents</button>
</h2>

<!-- TABLA OCULTA -->
<div id="tasques" class="hidden">
<table>
<tr>
<th>Nom</th>
<th>Tipus</th>
<th>Dates</th>
<th>Estat</th>
<th>Accions</th>
</tr>

<?php while($t = $res->fetch_assoc()): ?>
<tr>
<td><?= htmlspecialchars($t['nom_tasca']) ?></td>
<td><?= htmlspecialchars($t['tipus_tasca']) ?></td>
<td><?= $t['data_inici'] ?> → <?= $t['data_final'] ?></td>
<td><?= $t['estat'] ?></td>
<td>
<a href="tasca_editar.php?id=<?= $t['id_tasca'] ?>">✏️ Editar</a>
<a href="tasca_eliminar.php?id=<?= $t['id_tasca'] ?>"
   onclick="return confirm('Eliminar aquesta tasca?')">🗑️</a>
</td>
</tr>
<?php endwhile; ?>
</table>
</div>

<script>
function toggleTasques(){
  document.getElementById('tasques').classList.toggle('hidden');
}
</script>

</body>
</html>
