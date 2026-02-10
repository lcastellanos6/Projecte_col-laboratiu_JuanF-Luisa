<?php
$conn = new mysqli("localhost", "root", "", "web");
if ($conn->connect_error) die("Error BD: " . $conn->connect_error);
$conn->set_charset("utf8");

$sql = "
SELECT
    p.id_plantacio,
    s.nom AS sector,
    p.num_arbres_total,
    IFNULL(SUM(c.quantitat_total),0) AS total_collita,
    IFNULL(SUM(c.quantitat_total) / NULLIF(p.num_arbres_total,0),0) AS collita_per_arbre
FROM plantacio p
JOIN sector s ON p.id_sector = s.id_sector
LEFT JOIN collita c ON c.plantacio_id = p.id_plantacio
GROUP BY p.id_plantacio, s.nom, p.num_arbres_total
ORDER BY s.nom, p.id_plantacio
";

$result = $conn->query($sql);

// Clasificación
function colorParcel($v){
    if ($v >= 50) return "green";
    if ($v >= 20) return "yellow";
    return "red";
}

// Agrupar por sector
$sectors = [];
while($r = $result->fetch_assoc()){
    $sectors[$r['sector']][] = $r;
}
?>

<!DOCTYPE html>
<html lang="ca">
<head>
<meta charset="UTF-8">
<title>Mapa de Calor de Parcel·les</title>
<link rel="stylesheet" href="../HTML/styles.css">
<style>
body{font-family:Arial;padding:20px;background:#f4f6f5}
h1{color:#2f7d2f}
h2{margin-top:30px;color:#333}

.mapa{
    display:grid;
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    gap:15px;
    margin-top:15px;
}

.parcela{
    border-radius:10px;
    padding:15px;
    color:#333;
    font-weight:bold;
    box-shadow:0 3px 8px rgba(0,0,0,0.15);
    transition:transform 0.2s;
}
.parcela:hover{transform:scale(1.05)}

.green{background:#c8e6c9}
.yellow{background:#fff3cd}
.red{background:#f8d7da}

.parcela span{
    display:block;
    font-size:13px;
    font-weight:normal;
    margin-top:4px;
}

.leyenda{
    margin-top:20px;
}
.leyenda span{
    display:inline-block;
    padding:6px 12px;
    margin-right:10px;
    border-radius:6px;
    font-weight:bold;
}
</style>
</head>
<body>

<h1>🗺️ Mapa de Calor de Rendiment Agronòmic</h1>

<div class="leyenda">
    <span class="green">🟢 Alt</span>
    <span class="yellow">🟡 Mitjà</span>
    <span class="red">🔴 Baix</span>
</div>

<?php foreach($sectors as $sector => $parceles): ?>
    <h2>Sector: <?= htmlspecialchars($sector) ?></h2>
    <div class="mapa">
        <?php foreach($parceles as $p): ?>
            <div class="parcela <?= colorParcel($p['collita_per_arbre']) ?>">
                Parcela <?= $p['id_plantacio'] ?>
                <span>🌳 Arbres: <?= $p['num_arbres_total'] ?></span>
                <span>🌾 Total: <?= round($p['total_collita'],2) ?></span>
                <span>📊 / arbre: <?= round($p['collita_per_arbre'],2) ?></span>
            </div>
        <?php endforeach; ?>
    </div>
<?php endforeach; ?>

</body>
</html>

