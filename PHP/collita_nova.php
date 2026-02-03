<?php
// Conexión a la base de datos
$conn = new mysqli("localhost", "root", "", "web");
if ($conn->connect_error) die("Error BD: " . $conn->connect_error);
$conn->set_charset("utf8");

// CONSULTA PLANTACIONS / PARCEL·LES
$plantacions = $conn->query("
    SELECT 
        p.id_plantacio, 
        s.nom AS sector_nom, 
        p.data_plantacio, 
        p.num_arbres_total
    FROM plantacio p
    JOIN sector s ON p.id_sector = s.id_sector
    ORDER BY s.nom, p.id_plantacio
");

// CONSULTA COLLITES
$collites = $conn->query("
    SELECT c.*, s.nom AS sector_nom
    FROM collita c
    JOIN plantacio p ON c.plantacio_id = p.id_plantacio
    JOIN sector s ON p.id_sector = s.id_sector
    ORDER BY c.data_inici DESC
");
?>
<!DOCTYPE html>
<html lang="ca">
<head>
<meta charset="UTF-8">
<title>Gestió de Collita</title>
<link rel="stylesheet" href="../HTML/styles.css">
<style>
body{font-family:Arial;padding:20px}
form{background:#f5f5f5;padding:15px;border-radius:6px;max-width:900px}
label{display:block;margin-top:10px;font-weight:bold}
input,select,textarea{width:100%;padding:6px;margin-top:3px}
button{margin-top:15px;padding:10px;border:none;cursor:pointer;border-radius:4px}
.btn{background:#2f7d2f;color:#fff}
.btn-sec{background:#555;color:#fff}
table{border-collapse:collapse;width:100%;margin-top:20px}
th,td{border:1px solid #ccc;padding:8px;text-align:left}
th{background:#eee}
.hidden{display:none}
</style>
</head>
<body>

<h1>🍎 Gestió de la Collita</h1>

<!-- FORMULARI -->
<form action="collita_guardar.php" method="post">

<label>Parcel·la / Sector *</label>
<select name="plantacio_id" required>
  <option value="">-- Selecciona --</option>
  <?php while($p = $plantacions->fetch_assoc()): ?>
    <option value="<?= $p['id_plantacio'] ?>">
      <?= "ID: ".$p['id_plantacio']." | Sector: ".htmlspecialchars($p['sector_nom'])." | Plantació: ".$p['data_plantacio']." | Arbres: ".$p['num_arbres_total'] ?>
    </option>
  <?php endwhile; ?>
</select>

<label>Data inici *</label>
<input type="datetime-local" name="data_inici" required>

<label>Data fi</label>
<input type="datetime-local" name="data_fi">

<label>Quantitat total</label>
<input type="number" step="0.01" name="quantitat_total">

<label>Unitat</label>
<select name="unitat">
  <option value="kg">Kg</option>
  <option value="caixa">Caixes</option>
  <option value="bin">Bins</option>
</select>

<label>Condicions ambientals</label>
<textarea name="condicions_ambientals"></textarea>

<label>Maduresa del fruit</label>
<input type="text" name="maduresa">

<label>Incidències</label>
<textarea name="incidencies"></textarea>

<button class="btn">Guardar collita</button>
</form>

<!-- TAULA DE COLLITES REGISTRADES -->
<div style="margin-top:30px">
<button class="btn-sec" onclick="toggle()">📋 Collites registrades</button>
</div>

<div id="taula" class="hidden">
<table>
<tr>
  <th>Sector</th>
  <th>Inici</th>
  <th>Fi</th>
  <th>Quantitat</th>
  <th>Unitat</th>
  <th>Maduresa</th>
  <th>Incidències</th>
  <th>Condicions Ambientals</th>
</tr>

<?php while($c = $collites->fetch_assoc()): ?>
<tr>
  <td><?= htmlspecialchars($c['sector_nom']) ?></td>
  <td><?= $c['data_inici'] ?></td>
  <td><?= $c['data_fi'] ?: '—' ?></td>
  <td><?= $c['quantitat_total'] ?: '—' ?></td>
  <td><?= $c['unitat'] ?></td>
  <td><?= htmlspecialchars($c['maduresa']) ?></td>
  <td><?= htmlspecialchars($c['incidencies']) ?: '—' ?></td>
  <td><?= htmlspecialchars($c['condicions_ambientals']) ?: '—' ?></td>
</tr>
<?php endwhile; ?>
</table>
</div>

<script>
function toggle(){
  document.getElementById('taula').classList.toggle('hidden');
}
</script>

</body>
</html>


