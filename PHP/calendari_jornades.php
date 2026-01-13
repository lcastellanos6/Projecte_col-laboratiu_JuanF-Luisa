<?php
$conn = new mysqli("localhost","root","","web");
if ($conn->connect_error) die("Error BD: " . $conn->connect_error);

// Consultar jornadas con info de trabajador y modelo de horario
$res = $conn->query("
SELECT j.*, t.nom_complet AS treballador, t.id_calendari_model,
       ta.nom_tasca,
       m.nom AS model_nom, m.dies_laborables, m.dies_festius
FROM jornada j
JOIN treballador t ON t.id_treballador = j.id_treballador
LEFT JOIN tasca ta ON ta.id_tasca = j.id_tasca
LEFT JOIN calendari_model m ON m.id_calendari_model = t.id_calendari_model
ORDER BY data_hora_inici
");
if (!$res) die("Error consulta: " . $conn->error);

// Agrupar jornadas por día
$calendari = [];
while ($r = $res->fetch_assoc()) {
    $dia = date('Y-m-d', strtotime($r['data_hora_inici']));
    $calendari[$dia][] = $r;
}

// Colores por trabajador
$colors = [];
$color_palette = ['#2f7d2f','#d9534f','#f0ad4e','#5bc0de','#563d7c','#f7b731','#20bf6b','#eb3b5a'];
$idx = 0;
foreach ($res as $r) {
    if (!isset($colors[$r['treballador']])) {
        $colors[$r['treballador']] = $color_palette[$idx % count($color_palette)];
        $idx++;
    }
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
<meta charset="UTF-8">
<title>Calendari amb model d'horari</title>
<style>
body { font-family: Arial; padding: 20px; }
.day { margin-bottom: 30px; }
.day h3 { margin-bottom: 10px; }
.worker-row { display: flex; align-items: center; margin-bottom: 5px; position: relative; height: 30px; }
.worker-name { width: 120px; font-weight: bold; }
.time-line { flex: 1; position: relative; background: #eaeaea; height: 100%; border: 1px solid #ddd; }
.jornada-block {
    position: absolute;
    height: 100%;
    color: #fff;
    text-align: center;
    font-size: 12px;
    border-radius: 4px;
    overflow: hidden;
    white-space: nowrap;
    cursor: pointer;
    transition: transform 0.2s;
}
.jornada-block:hover { transform: scale(1.05); z-index: 2; }
.time-labels { display: flex; margin-bottom: 5px; font-size: 12px; }
.time-labels div { flex: 1; text-align: center; border-right: 1px solid #ccc; }
.time-labels div:last-child { border-right: none; }
.tooltip {
    position: absolute;
    background: rgba(0,0,0,0.8);
    color: #fff;
    padding: 5px 8px;
    border-radius: 4px;
    font-size: 12px;
    display: none;
    pointer-events: none;
    white-space: nowrap;
}
</style>
</head>
<body>

<h2>📅 Calendari amb model d'horari</h2>

<?php
$dies_setmana_cat = [
    'Monday'=>'Dilluns','Tuesday'=>'Dimarts','Wednesday'=>'Dimecres',
    'Thursday'=>'Dijous','Friday'=>'Divendres','Saturday'=>'Dissabte','Sunday'=>'Diumenge'
];

foreach ($calendari as $dia => $jornades):
    $treballadors = [];
    foreach ($jornades as $j) $treballadors[$j['treballador']] = [];
    foreach ($jornades as $j) $treballadors[$j['treballador']][] = $j;

    echo "<div class='day'>";
    echo "<h3>$dia</h3>";
    echo "<div class='time-labels'>";
    for ($h=8;$h<=20;$h++) echo "<div>$h:00</div>";
    echo "</div>";

    foreach ($treballadors as $nom => $jornades_trab):
        echo "<div class='worker-row'>";
        echo "<div class='worker-name'>" . htmlspecialchars($nom) . "</div>";
        echo "<div class='time-line'>";

        foreach ($jornades_trab as $j):
            $inici = strtotime($j['data_hora_inici']);
            $fi = strtotime($j['data_hora_fi']);
            $minuts_pausa = $j['minuts_pausa'];

            // --- Días laborables y festivos ---
            $dies_laborables = array_map('trim', explode(',', $j['dies_laborables'] ?: ''));
            $dies_festius = array_map('trim', explode(',', $j['dies_festius'] ?: ''));

            $dia_setmana = $dies_setmana_cat[date('l', $inici)];

            $festiu = !empty($dies_festius) && in_array(date('Y-m-d',$inici), $dies_festius);
            $laborable = in_array($dia_setmana, $dies_laborables) && !$festiu;

            // Depuración: ver por qué está gris
            echo "<!-- Jornada: ".$j['treballador']." | Día: $dia_setmana | Laborable: ".($laborable?'Sí':'No')." | Festiu: ".($festiu?'Sí':'No')." -->\n";

            $color = $laborable ? $colors[$nom] : '#999'; // gris si no laborable

            // --- Horas extra ---
            $hora_model_inici = 8;
            $hora_model_fi = 16;
            $duracion_modelo = $hora_model_fi - $hora_model_inici;
            $hora_inici = max(8, (int)date('H',$inici) + date('i',$inici)/60);
            $hora_fi = min(20, (int)date('H',$fi) + date('i',$fi)/60);
            $duracion_real = $hora_fi - $hora_inici;
            $horas_extra = max(0, $duracion_real - $duracion_modelo);

            $tooltip = htmlspecialchars($j['nom_tasca'] ?: 'Cap tasca') . "<br>" .
                       "Incidències: " . htmlspecialchars($j['incidencies'] ?: 'Cap') . "<br>" .
                       "Model: " . htmlspecialchars($j['model_nom']) . "<br>" .
                       "Hores extra: {$horas_extra}";

            $total_hores = 20-8;
            $left = (($hora_inici-8)/$total_hores)*100;
            $width = (($hora_fi-$hora_inici)/$total_hores)*100;

            echo "<div class='jornada-block' style='left:{$left}%; width:{$width}%; background-color:{$color};'
                  data-tooltip='{$tooltip}'>
                  ".date('H:i',$inici)."→".date('H:i',$fi)."
                  </div>";

        endforeach;

        echo "</div></div>";
    endforeach;

    echo "</div>";
endforeach;
?>

<div class="tooltip" id="tooltip"></div>

<script>
// Tooltip dinámico
const tooltip = document.getElementById('tooltip');
document.querySelectorAll('.jornada-block').forEach(block => {
    block.addEventListener('mouseenter', e => {
        tooltip.innerHTML = block.getAttribute('data-tooltip');
        tooltip.style.display = 'block';
    });
    block.addEventListener('mousemove', e => {
        tooltip.style.left = e.pageX + 10 + 'px';
        tooltip.style.top = e.pageY + 10 + 'px';
    });
    block.addEventListener('mouseleave', e => {
        tooltip.style.display = 'none';
    });
});
</script>

</body>
</html>





