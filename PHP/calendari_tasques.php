<?php
session_start();
require_once __DIR__ . '/db.php';
$conn = db_connect();

$id_treballador = $_SESSION['id_treballador'] ?? 0;
$rol_usuari = $_SESSION['rol'] ?? 'usuari';

// ================== MES Y AÑO ==================
$mes = $_GET['mes'] ?? date('m');
$any = $_GET['any'] ?? date('Y');

$primer_dia = "$any-$mes-01";
$dies_mes = date('t', strtotime($primer_dia));
$dia_setmana = date('N', strtotime($primer_dia));

// ================== CARGAR TAREAS ==================
if ($rol_usuari === 'admin') {
    $sql = "SELECT id_tasca, nom_tasca, data_inici, data_final, estat,
                   data_inici_real, data_fi_real, notes, foto
            FROM tasca
            WHERE
                COALESCE(data_inici_real, data_inici) <= LAST_DAY('$primer_dia')
            AND COALESCE(data_fi_real, data_final, data_inici_real, data_inici) >= '$primer_dia'";
} else {
    $sql = "SELECT t.id_tasca, t.nom_tasca, t.data_inici, t.data_final, t.estat,
                   t.data_inici_real, t.data_fi_real, t.notes, t.foto
            FROM tasca t
            JOIN treballador_tasca tt ON t.id_tasca = tt.id_tasca
            WHERE tt.id_treballador = $id_treballador
            AND (
                COALESCE(t.data_inici_real, t.data_inici) <= LAST_DAY('$primer_dia')
                AND COALESCE(t.data_fi_real, t.data_final, t.data_inici_real, t.data_inici) >= '$primer_dia'
            )";
}

$tascas = $conn->query($sql);

if (!$tascas) die("Error SQL: ".$conn->error);

// ================== INDEXAR POR DÍA ==================
$cal = [];

while ($t = $tascas->fetch_assoc()) {

    $start = $t['data_inici_real'] ?: $t['data_inici'];
    $end   = $t['data_fi_real'] ?: ($t['data_final'] ?: $start);

    if (!$start || !$end) continue;

    for ($d = strtotime($start); $d <= strtotime($end); $d = strtotime("+1 day", $d)) {
        if (date('Y-m', $d) === "$any-$mes") {
            $dia = date('j', $d);
            $cal[$dia][] = $t;
        }
    }
}

// ================== NAVEGACIÓN ==================
$prevMes = date('m', strtotime("-1 month", strtotime($primer_dia)));
$prevAny = date('Y', strtotime("-1 month", strtotime($primer_dia)));
$nextMes = date('m', strtotime("+1 month", strtotime($primer_dia)));
$nextAny = date('Y', strtotime("+1 month", strtotime($primer_dia)));
?>
<!DOCTYPE html>
<html lang="ca">
<head>
<meta charset="UTF-8">
<title>Calendari de tasques</title>

<style>
body { font-family: Arial; padding:20px; }
.calendar { display:grid; grid-template-columns:repeat(7,1fr); gap:6px; }
.day { border:1px solid #ccc; min-height:150px; padding:5px; font-size:12px; }
.header { background:#eee; font-weight:bold; text-align:center; padding:5px; }

.planificada { background:#cce5ff; padding:3px; margin:2px 0; border-radius:3px; }
.en_curs { background:#ffe5b4; padding:3px; margin:2px 0; border-radius:3px; }
.feta { background:#d4edda; padding:3px; margin:2px 0; border-radius:3px; }
.cancel_lada { background:#f8d7da; padding:3px; margin:2px 0; border-radius:3px; }

.day img { max-width:70px; margin-top:4px; display:block; }

.nav { margin-bottom:15px; }
.nav a {
    text-decoration:none;
    padding:6px 10px;
    background:#2f7d2f;
    color:white;
    border-radius:5px;
    margin-right:5px;
}

.modal {
    display:none;
    position:fixed;
    inset:0;
    background:rgba(0,0,0,.5);
    justify-content:center;
    align-items:center;
}
.modal-content {
    background:#fff;
    padding:20px;
    border-radius:6px;
    width:360px;
}
</style>
</head>
<body>

<h1>📅 Calendari de tasques <?= "$mes / $any" ?></h1>

<div class="nav">
    <a href="?mes=<?= $prevMes ?>&any=<?= $prevAny ?>">⬅ Mes anterior</a>
    <a href="?mes=<?= date('m') ?>&any=<?= date('Y') ?>">📍 Mes actual</a>
    <a href="?mes=<?= $nextMes ?>&any=<?= $nextAny ?>">Mes següent ➡</a>
</div>

<div class="calendar">
<?php
$dias = ['Dl','Dm','Dc','Dj','Dv','Ds','Dg'];
foreach ($dias as $d) echo "<div class='header'>$d</div>";

for ($i=1; $i<$dia_setmana; $i++) echo "<div></div>";

for ($dia=1; $dia<=$dies_mes; $dia++):
?>
<div class="day">
<strong><?= $dia ?></strong>

<?php
if (!empty($cal[$dia])):
foreach ($cal[$dia] as $t):
$cls = strtolower(str_replace(' ','_',$t['estat']));
?>
<div class="<?= $cls ?>">
<?= htmlspecialchars($t['nom_tasca']) ?>

<?php if ($t['data_inici_real'] && $t['data_fi_real']): ?>
<br><small>📅 <?= $t['data_inici_real'] ?> → <?= $t['data_fi_real'] ?></small>
<?php endif; ?>

<?php if ($t['notes']): ?>
<br>📝 <?= htmlspecialchars($t['notes']) ?>
<?php endif; ?>

<?php if ($t['foto']): ?>
<br><img src="<?= htmlspecialchars($t['foto']) ?>">
<?php endif; ?>

<br>
<button onclick="openModal(<?= $t['id_tasca'] ?>)">✏️ Editar</button>
</div>
<?php endforeach; endif; ?>

</div>
<?php endfor; ?>
</div>

<!-- MODAL -->
<div id="modal" class="modal">
<div class="modal-content">
<h3>Modificar tasca</h3>

<form action="tasca_registrar.php" method="post" enctype="multipart/form-data">
<input type="hidden" name="id_tasca" id="modal_id">

<label>Data inici real</label>
<input type="datetime-local" name="data_inici_real" required>

<label>Data fi real</label>
<input type="datetime-local" name="data_fi_real" required>

<label>Notes</label>
<textarea name="notes"></textarea>

<label>Foto</label>
<input type="file" name="foto">

<br><br>
<button type="submit">Guardar</button>
<button type="button" onclick="closeModal()">Cancel·lar</button>
</form>
</div>
</div>

<script>
function openModal(id){
    document.getElementById('modal').style.display='flex';
    document.getElementById('modal_id').value=id;
}
function closeModal(){
    document.getElementById('modal').style.display='none';
}
</script>

</body>
</html>







