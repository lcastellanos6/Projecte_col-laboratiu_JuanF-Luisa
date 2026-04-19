<?php
require_once __DIR__ . '/db.php';

$conn = db_connect();

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

// OPERARIS (responsable/cap d'equip)
$operaris = $conn->query("SELECT id_operari, nom FROM operari ORDER BY nom");

// EQUIPS
$equips = $conn->query("SELECT id_equip, tipus FROM equip ORDER BY tipus");

// ESTATS FENOLÒGICS
$estats = $conn->query("SELECT id_estat, nom FROM estat_fenologic ORDER BY id_estat");

// CONSULTA COLLITES
$collites = $conn->query("
    SELECT c.*, s.nom AS sector_nom
    FROM collita c
    JOIN plantacio p ON c.plantacio_id = p.id_plantacio
    JOIN sector s ON p.id_sector = s.id_sector
    ORDER BY c.data_inici DESC
");

$error = $_GET['error'] ?? '';
$ok = $_GET['ok'] ?? '';
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

<h1>Gestió de la Collita</h1>

<?php if ($ok === '1'): ?>
  <div style="margin:10px 0; padding:10px 12px; border:1px solid #b9e2c1; background:#f0fff4; color:#1a5c2e; border-radius:6px;">
    Collita guardada correctament.
  </div>
<?php endif; ?>
<?php if ($error !== ''): ?>
  <div style="margin:10px 0; padding:10px 12px; border:1px solid #e3b2b2; background:#fff5f5; color:#8a2a2a; border-radius:6px;">
    <?php if ($error === 'required'): ?>
      Falten camps obligatoris (plantació i data d'inici).
    <?php else: ?>
      No s'ha pogut guardar la collita.
    <?php endif; ?>
  </div>
<?php endif; ?>

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

<label>Operari (cap d'equip / responsable)</label>
<select name="id_operari">
  <option value="">-- Sense assignar --</option>
  <?php if ($operaris): while($o = $operaris->fetch_assoc()): ?>
    <option value="<?= (int)$o['id_operari'] ?>"><?= htmlspecialchars($o['nom'] ?? '') ?></option>
  <?php endwhile; endif; ?>
</select>

<label>Equip</label>
<select name="id_equip">
  <option value="">-- Sense assignar --</option>
  <?php if ($equips): while($e = $equips->fetch_assoc()): ?>
    <option value="<?= (int)$e['id_equip'] ?>"><?= htmlspecialchars($e['tipus'] ?? '') ?></option>
  <?php endwhile; endif; ?>
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

<label>Estat fenològic</label>
<select name="id_estat">
  <option value="">-- No indicat --</option>
  <?php if ($estats): while($es = $estats->fetch_assoc()): ?>
    <option value="<?= (int)$es['id_estat'] ?>"><?= htmlspecialchars($es['nom'] ?? '') ?></option>
  <?php endwhile; endif; ?>
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

<?php
$conn->close();
?>


