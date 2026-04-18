<?php
require_once __DIR__ . '/db.php';

$conn = db_connect();

$campanya = filter_input(INPUT_GET, 'campanya', FILTER_VALIDATE_INT);
if (!$campanya) {
    $campanya = (int) date('Y');
}

$sql = "
SELECT
    p.id_parcela,
    p.nom,
    p.municipi,
    p.superficie,
    ST_AsGeoJSON(p.geometria) AS geojson,
    COALESCE(prev.previsio_kg, 0) AS previsio_kg,
    COALESCE(seg.incidencies_actives, 0) AS incidencies_actives,
    COALESCE(task.tasques_pendents, 0) AS tasques_pendents,
    COALESCE(inf.infraestructures, 0) AS infraestructures
FROM parcela p
LEFT JOIN (
    SELECT
        id_parcela,
        SUM(CASE WHEN unitat = 'Tn' THEN estimacio_produccio * 1000 ELSE estimacio_produccio END) AS previsio_kg
    FROM previsio_collita
    WHERE campanya_any = ?
    GROUP BY id_parcela
) prev ON prev.id_parcela = p.id_parcela
LEFT JOIN (
    SELECT
        sp.id_parcela,
        SUM(
            CASE
                WHEN ult.incidencies_detectades IS NOT NULL
                     AND TRIM(ult.incidencies_detectades) <> ''
                     AND LOWER(TRIM(ult.incidencies_detectades)) NOT LIKE 'sense%'
                THEN 1 ELSE 0
            END
        ) AS incidencies_actives
    FROM sector_parcela sp
    LEFT JOIN (
        SELECT s1.id_sector, s1.incidencies_detectades
        FROM seguiment s1
        INNER JOIN (
            SELECT id_sector, MAX(data_registre) AS max_data
            FROM seguiment
            GROUP BY id_sector
        ) s2 ON s2.id_sector = s1.id_sector AND s2.max_data = s1.data_registre
    ) ult ON ult.id_sector = sp.id_sector
    GROUP BY sp.id_parcela
) seg ON seg.id_parcela = p.id_parcela
LEFT JOIN (
    SELECT
        sp.id_parcela,
        COUNT(*) AS tasques_pendents
    FROM sector_parcela sp
    JOIN tasca t ON t.id_sector = sp.id_sector
    WHERE t.estat IN ('Planificada', 'En curs')
    GROUP BY sp.id_parcela
) task ON task.id_parcela = p.id_parcela
LEFT JOIN (
    SELECT id_parcela, COUNT(*) AS infraestructures
    FROM infraestructura
    GROUP BY id_parcela
) inf ON inf.id_parcela = p.id_parcela
ORDER BY p.nom
";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $campanya);
$stmt->execute();
$result = $stmt->get_result();

$features = [];
while ($row = $result->fetch_assoc()) {
    $geo = $row['geojson'] ? json_decode($row['geojson'], true) : null;
    if (!$geo) {
        continue;
    }

    $indexRisc = ((int) $row['incidencies_actives'] * 3) + ((int) $row['tasques_pendents']) - ((int) $row['infraestructures']);
    if ($indexRisc < 0) {
        $indexRisc = 0;
    }

    $features[] = [
        'type' => 'Feature',
        'geometry' => $geo,
        'properties' => [
            'id_parcela' => (int) $row['id_parcela'],
            'nom' => $row['nom'],
            'municipi' => $row['municipi'],
            'superficie' => (float) $row['superficie'],
            'previsio_kg' => (float) $row['previsio_kg'],
            'incidencies_actives' => (int) $row['incidencies_actives'],
            'tasques_pendents' => (int) $row['tasques_pendents'],
            'infraestructures' => (int) $row['infraestructures'],
            'index_risc' => $indexRisc,
        ],
    ];
}

$stmt->close();
$conn->close();

$geojson = json_encode(['type' => 'FeatureCollection', 'features' => $features], JSON_UNESCAPED_UNICODE);
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Mapa temàtic de parcel·les</title>
    <link rel="stylesheet" href="../HTML/styles.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <style>
      #map-tematic { height: 620px; border-radius: 0.8rem; border: 1px solid #d1d5db; }
      .legend-box { display: flex; gap: 0.6rem; flex-wrap: wrap; margin-top: 0.8rem; }
      .legend-item { display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.85rem; }
      .dot { width: 12px; height: 12px; border-radius: 999px; display: inline-block; }
      .toolbar { display: flex; gap: 1rem; flex-wrap: wrap; align-items: center; }
      .toolbar label { margin: 0; font-weight: 600; }
      .toolbar select, .toolbar input { width: auto; min-width: 120px; }
      .kpi-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 0.7rem; margin-top: 0.9rem; }
      .kpi { border: 1px solid #d7e7d7; border-radius: 0.7rem; padding: 0.7rem; background: #f7fbf7; }
      .kpi strong { display: block; font-size: 1.2rem; }
    </style>
</head>
<body>
<div class="page">
  <div class="page-header">
    <h1>Mapa temàtic de l'estat actual de parcel·les</h1>
    <p class="page-subtitle">Visualització per capes de previsió, incidències, tasques pendents i nivell de risc.</p>
  </div>

  <div class="panel">
    <form method="get" class="toolbar">
      <label for="campanya">Campanya:</label>
      <input id="campanya" type="number" name="campanya" min="2000" max="2100" value="<?php echo htmlspecialchars((string) $campanya); ?>">

      <label for="tipus-capa">Capa:</label>
      <select id="tipus-capa">
        <option value="risc">Risc global</option>
        <option value="previsio">Previsió de producció</option>
        <option value="incidencies">Incidències actives</option>
        <option value="tasques">Tasques pendents</option>
      </select>

      <button type="submit" class="btn btn-primary">Actualitzar campanya</button>
    </form>
    <div id="map-tematic" class="mt-2"></div>
    <div class="legend-box">
      <span class="legend-item"><span class="dot" style="background:#2f7d2f"></span>Baix</span>
      <span class="legend-item"><span class="dot" style="background:#f59e0b"></span>Mitjà</span>
      <span class="legend-item"><span class="dot" style="background:#dc2626"></span>Alt</span>
    </div>
    <div class="kpi-row" id="kpis"></div>
  </div>
</div>

<script>
const data = <?php echo $geojson ?: '{"type":"FeatureCollection","features":[]}'; ?>;
const map = L.map('map-tematic');
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  maxZoom: 19,
  attribution: '&copy; OpenStreetMap'
}).addTo(map);

// External SIG layers (WMS)
const wmsCatastro = L.tileLayer.wms('https://ovc.catastro.meh.es/Cartografia/WMS/ServidorWMS.aspx', {
  layers: 'catastro',
  format: 'image/png',
  transparent: true,
  opacity: 0.6,
  attribution: 'Dirección General del Catastro'
});

const wmsNatura2000 = L.tileLayer.wms('https://wms.mapama.gob.es/sig/Biodiversidad/RedNatura/wms.aspx', {
  layers: 'PS.ProtectedSite',
  format: 'image/png',
  transparent: true,
  version: '1.3.0',
  opacity: 0.55,
  attribution: 'MITECO (Red Natura 2000)'
});

L.control.layers(null, {
  'Catastro (WMS)': wmsCatastro,
  'Red Natura 2000 (WMS)': wmsNatura2000
}, { collapsed: true }).addTo(map);

let layer = null;
const layerSelector = document.getElementById('tipus-capa');
const kpiBox = document.getElementById('kpis');

function colorByMetric(props, mode) {
  if (mode === 'previsio') {
    const v = Number(props.previsio_kg || 0);
    if (v >= 1500) return '#2f7d2f';
    if (v >= 700) return '#f59e0b';
    return '#dc2626';
  }
  if (mode === 'incidencies') {
    const v = Number(props.incidencies_actives || 0);
    if (v === 0) return '#2f7d2f';
    if (v <= 2) return '#f59e0b';
    return '#dc2626';
  }
  if (mode === 'tasques') {
    const v = Number(props.tasques_pendents || 0);
    if (v <= 1) return '#2f7d2f';
    if (v <= 3) return '#f59e0b';
    return '#dc2626';
  }
  const r = Number(props.index_risc || 0);
  if (r <= 2) return '#2f7d2f';
  if (r <= 6) return '#f59e0b';
  return '#dc2626';
}

function tooltipFor(props) {
  return `
    <strong>${props.nom || 'Parcel·la'}</strong><br>
    Municipi: ${props.municipi || '-'}<br>
    Superfície: ${Number(props.superficie || 0).toFixed(2)} ha<br>
    Previsió: ${Number(props.previsio_kg || 0).toLocaleString('ca-ES')} kg<br>
    Incidències: ${props.incidencies_actives || 0}<br>
    Tasques pendents: ${props.tasques_pendents || 0}<br>
    Infraestructures: ${props.infraestructures || 0}<br>
    Índex risc: ${props.index_risc || 0}
  `;
}

function renderKpis(features) {
  const total = features.length;
  let previsio = 0;
  let incidencies = 0;
  let tasques = 0;
  features.forEach((f) => {
    const p = f.properties || {};
    previsio += Number(p.previsio_kg || 0);
    incidencies += Number(p.incidencies_actives || 0);
    tasques += Number(p.tasques_pendents || 0);
  });
  kpiBox.innerHTML = `
    <div class="kpi"><span>Parcel·les al mapa</span><strong>${total}</strong></div>
    <div class="kpi"><span>Previsió total</span><strong>${previsio.toLocaleString('ca-ES')} kg</strong></div>
    <div class="kpi"><span>Incidències actives</span><strong>${incidencies}</strong></div>
    <div class="kpi"><span>Tasques pendents</span><strong>${tasques}</strong></div>
  `;
}

function renderLayer(mode) {
  if (layer) {
    map.removeLayer(layer);
  }
  layer = L.geoJSON(data, {
    style: (feature) => ({
      color: colorByMetric(feature.properties || {}, mode),
      weight: 2,
      fillOpacity: 0.25
    }),
    onEachFeature: (feature, l) => {
      l.bindTooltip(tooltipFor(feature.properties || {}), {sticky: true});
    }
  }).addTo(map);

  try {
    const bounds = layer.getBounds();
    if (bounds.isValid()) {
      map.fitBounds(bounds.pad(0.15));
    } else {
      map.setView([41.61, 0.87], 12);
    }
  } catch (error) {
    map.setView([41.61, 0.87], 12);
  }

  renderKpis(data.features || []);
}

layerSelector.addEventListener('change', () => {
  renderLayer(layerSelector.value);
});

renderLayer(layerSelector.value);
</script>
</body>
</html>
