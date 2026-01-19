<?php
$conn = new mysqli("localhost","root","","web");
if ($conn->connect_error) die("Error BD");
$conn->set_charset("utf8");

// Mes y año
$mes = $_GET['mes'] ?? date('m');
$any = $_GET['any'] ?? date('Y');
$primer_dia = "$any-$mes-01";
$dies_mes = date('t', strtotime($primer_dia));
$dia_setmana = date('N', strtotime($primer_dia));

// ================== CARGAR TAREAS ==================
$tascas = $conn->query("
    SELECT id_tasca, nom_tasca, data_inici, data_final, estat, data_inici_real, data_fi_real, notes, foto
    FROM tasca
    WHERE (MONTH(data_inici) = $mes OR MONTH(data_final) = $mes OR
           (data_inici_real IS NOT NULL AND MONTH(data_inici_real) = $mes) OR
           (data_fi_real IS NOT NULL AND MONTH(data_fi_real) = $mes))
      AND YEAR(data_inici) <= $any
      AND YEAR(data_final) >= $any
");
if (!$tascas) die("Error en la consulta SQL: ".$conn->error);

// ================== INDEXAR POR DÍA ==================
$cal = [];
while ($t = $tascas->fetch_assoc()) {
    // Usar fechas reales si existen, si no usar planificadas
    $start = !empty($t['data_inici_real']) ? strtotime($t['data_inici_real']) : strtotime($t['data_inici']);
    $end   = !empty($t['data_fi_real'])    ? strtotime($t['data_fi_real'])    : strtotime($t['data_final']);

    // Asegurarse de que hay fechas válidas
    if (!$start) $start = strtotime($t['data_inici']);
    if (!$end)   $end = strtotime($t['data_final']);

    for ($d = $start; $d <= $end; $d = strtotime("+1 day", $d)) {
        if (date('m', $d) == $mes && date('Y', $d) == $any) {
            $dia = date('j', $d);
            $cal[$dia][] = $t;
        }
    }
}

// ================== NAVEGACIÓN MES ==================
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
body { font-family: Arial; padding: 20px; }
.calendar { display: grid; grid-template-columns: repeat(7, 1fr); gap: 6px; }
.day { border:1px solid #ccc; min-height:150px; padding:5px; font-size:12px; position: relative; }
.header { background:#eee; font-weight:bold; text-align:center; padding:5px; }
.planificada { background:#cce5ff; margin:2px 0; padding:3px; border-radius:3px; }
.en_curs { background:#ffe5b4; margin:2px 0; padding:3px; border-radius:3px; }
.feta { background:#d4edda; margin:2px 0; padding:3px; border-radius:3px; }
.cancel_lada { background:#f8d7da; margin:2px 0; padding:3px; border-radius:3px; }
.nav { margin-bottom:15px; }
.nav a { text-decoration:none; padding:6px 10px; background:#2f7d2f; color:white; border-radius:5px; margin-right:5px; }
.modal { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.5); justify-content:center; align-items:center; }
.modal-content { background:white; padding:20px; border-radius:5px; max-width:400px; width:90%; }
.modal-content label { display:block; margin-top:10px; }
.modal-content input, .modal-content textarea { width:100%; padding:6px; margin-top:3px; }
.modal-content button { margin-top:10px; padding:8px; cursor:pointer; }
.day img { max-width:70px; margin-top:2px; display:block; }
</style>
</head>
<body>

<h1>📅 Calendari de tasques <?= "$mes / $any" ?></h1>

<div class="nav">
    <a href="?mes=<?= $prevMes ?>&any=<?= $prevAny ?>">⬅ Mes anterior</a>
    <a href="?mes=<?= date('m') ?>&any=<?= date('Y') ?>">📍 Avui</a>
    <a href="?mes=<?= $nextMes ?>&any=<?= $nextAny ?>">Mes següent ➡</a>
</div>

<div class="calendar">
<?php
$dias = ['Dl','Dm','Dc','Dj','Dv','Ds','Dg'];
foreach ($dias as $d) echo "<div class='header'>$d</div>";

// Espacios vacíos antes del primer día
for ($i=1; $i < $dia_setmana; $i++) echo "<div></div>";

// Mostrar los días
for ($dia=1; $dia<=$dies_mes; $dia++):
?>
<div class="day">
<strong><?= $dia ?></strong>
<?php
if (!empty($cal[$dia])):
    foreach ($cal[$dia] as $t):
        $cls = strtolower(str_replace(' ','_',$t['estat']));
        echo "<div class='$cls'>";
        echo htmlspecialchars($t['nom_tasca']);

        // Mostrar fechas reales si existen
        if (!empty($t['data_inici_real']) && !empty($t['data_fi_real'])) {
            echo "<br><small>📅 ".htmlspecialchars($t['data_inici_real'])." → ".htmlspecialchars($t['data_fi_real'])."</small>";
        }

        // Mostrar notas si existen
        if (!empty($t['notes'])) {
            echo "<br>📝 ".htmlspecialchars($t['notes']);
        }

        // Mostrar foto si existe
        if (!empty($t['foto'])) {
            echo "<br><img src='".htmlspecialchars($t['foto'])."' alt='foto'>";
        }

        // Botón para abrir modal
        echo "<br><button onclick=\"openModal({$t['id_tasca']})\">Editar 📝</button>";
        echo "</div>";
    endforeach;
endif;
?>
</div>
<?php endfor; ?>
</div>

<!-- MODAL PARA REGISTRAR -->
<div id="modal" class="modal">
<div class="modal-content">
<h3>Registrar tasca</h3>
<form id="modalForm" action="tasca_registrar.php" method="post" enctype="multipart/form-data">
<input type="hidden" name="id_tasca" id="modal_id">
<label>Data inici real</label>
<input type="datetime-local" name="data_inici_real" required>
<label>Data fi real</label>
<input type="datetime-local" name="data_fi_real" required>
<label>Notes</label>
<textarea name="notes"></textarea>
<label>Foto</label>
<input type="file" name="foto" accept="image/*">
<br>
<button type="submit">Guardar</button>
<button type="button" onclick="closeModal()">Tancar</button>
</form>
</div>
</div>

<script>
function openModal(id){
    document.getElementById('modal').style.display = 'flex';
    document.getElementById('modal_id').value = id;
}
function closeModal(){
    document.getElementById('modal').style.display = 'none';
}
</script>

</body>
</html>



