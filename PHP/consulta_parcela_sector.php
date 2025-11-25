<?php
// Connexió a la base de dades
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "web";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Error de connexió: " . $conn->connect_error);
}

// --- LLEGIR FILTRES DEL FORMULARI (GET) ---
$municipi        = $_GET['municipi']        ?? '';
$orientacio      = $_GET['orientacio']      ?? '';
$estat_productiu = $_GET['estat_productiu'] ?? '';
$superficie_min  = $_GET['superficie_min']  ?? '';
$superficie_max  = $_GET['superficie_max']  ?? '';

// --- CONSTRUIR LA CONSULTA DINÀMICA ---
$sql = "
SELECT 
    p.id_parcela,
    p.ref_cadastral,
    p.nom        AS nom_parcela,
    p.municipi,
    p.superficie AS sup_parcela,
    p.orientacio,
    p.tipus_sol,
    s.id_sector,
    s.nom        AS nom_sector,
    s.superficie AS sup_sector,
    s.estat_productiu,
    ST_AsGeoJSON(p.geometria) AS parcela_geojson,
    ST_AsGeoJSON(s.geometria) AS sector_geojson
FROM parcela p
LEFT JOIN sector_parcela sp ON sp.id_parcela = p.id_parcela
LEFT JOIN sector s          ON s.id_sector   = sp.id_sector
";

$condicions = [];

// Municipi (LIKE, per poder escriure només part del nom)
if ($municipi !== '') {
    $municipi_esc = $conn->real_escape_string($municipi);
    $condicions[] = "p.municipi LIKE '%$municipi_esc%'";
}

// Orientació exacta
if ($orientacio !== '') {
    $orientacio_esc = $conn->real_escape_string($orientacio);
    $condicions[]   = "p.orientacio = '$orientacio_esc'";
}

// Estat productiu del sector
if ($estat_productiu !== '') {
    $estat_esc    = $conn->real_escape_string($estat_productiu);
    $condicions[] = "s.estat_productiu = '$estat_esc'";
}

// Rangs de superfície
if ($superficie_min !== '') {
    $superficie_min = floatval($superficie_min);
    $condicions[]   = "p.superficie >= $superficie_min";
}
if ($superficie_max !== '') {
    $superficie_max = floatval($superficie_max);
    $condicions[]   = "p.superficie <= $superficie_max";
}

// Afegim el WHERE si hi ha alguna condició
if (!empty($condicions)) {
    $sql .= " WHERE " . implode(" AND ", $condicions);
}

// Ordre per tenir-ho bonic
$sql .= " ORDER BY p.municipi, p.nom, s.nom";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Consulta de parcel·les i sectors</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 16px; }
        form { margin-bottom: 20px; }
        label { display: block; margin-top: 8px; font-weight: bold; }
        input, select { width: 100%; padding: 6px; }
        button { margin-top: 10px; padding: 8px 12px; }
        #layout { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        #map { height: 520px; border: 1px solid #ccc; }
        table { border-collapse: collapse; width: 100%; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        th { background: #eee; }
        tr.clickable { cursor: pointer; }
        tr.highlight { background: #fff7c2; }
    </style>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    </head>
<body>

<h2>Consulta de parcel·les i sectors</h2>

<form method="get">
    <label>Municipi</label>
    <input type="text" name="municipi" value="<?php echo htmlspecialchars($municipi); ?>">

    <label>Orientació</label>
    <select name="orientacio">
        <option value="">(Qualsevol)</option>
        <?php
        $opcions = ['N','S','E','O','NE','NO','SE','SO'];
        foreach ($opcions as $op) {
            $sel = ($op === $orientacio) ? 'selected' : '';
            echo "<option value=\"$op\" $sel>$op</option>";
        }
        ?>
    </select>

    <label>Estat productiu del sector</label>
    <select name="estat_productiu">
        <option value="">(Qualsevol)</option>
        <?php
        $estats = ['Repos','Plantat','Productiu','Reconvertit','Abandonat'];
        foreach ($estats as $e) {
            $sel = ($e === $estat_productiu) ? 'selected' : '';
            echo "<option value=\"$e\" $sel>$e</option>";
        }
        ?>
    </select>

    <label>Superfície mínima (ha)</label>
    <input type="number" step="0.01" name="superficie_min" value="<?php echo htmlspecialchars($superficie_min); ?>">

    <label>Superfície màxima (ha)</label>
    <input type="number" step="0.01" name="superficie_max" value="<?php echo htmlspecialchars($superficie_max); ?>">

    <button type="submit">Filtrar</button>
</form>

<div id="layout">
  <div id="map"></div>
  <div>
<?php
if ($result && $result->num_rows > 0): ?>
    <table id="resultTable">
        <tr>
            <th>Parcel·la</th>
            <th>Ref. cadastral</th>
            <th>Municipi</th>
            <th>Sup. parcel·la</th>
            <th>Orientació</th>
            <th>Tipus de sòl</th>
            <th>Sector</th>
            <th>Sup. sector</th>
            <th>Estat productiu</th>
        </tr>
        <?php 
        $featuresParceles = [];
        $featuresSectors  = [];
        while($row = $result->fetch_assoc()): 
            $pid = $row['id_parcela'];
            $sid = $row['id_sector'];
            $pgeo = $row['parcela_geojson'] ?? null;
            $sgeo = $row['sector_geojson'] ?? null;
            if ($pid && $pgeo) {
                $featuresParceles[$pid] = [ 'id'=>$pid, 'nom'=>$row['nom_parcela'] ?? '', 'geojson'=> json_decode($pgeo, true) ];
            }
            if ($sid && $sgeo) {
                $featuresSectors[$sid] = [ 'id'=>$sid, 'nom'=>$row['nom_sector'] ?? '', 'geojson'=> json_decode($sgeo, true) ];
            }
        ?>
        <tr class="clickable" data-parcela-id="<?php echo htmlspecialchars($pid ?? ''); ?>" data-sector-id="<?php echo htmlspecialchars($sid ?? ''); ?>">
            <td><?php echo htmlspecialchars($row['nom_parcela']); ?></td>
            <td><?php echo htmlspecialchars($row['ref_cadastral']); ?></td>
            <td><?php echo htmlspecialchars($row['municipi']); ?></td>
            <td><?php echo htmlspecialchars($row['sup_parcela']); ?></td>
            <td><?php echo htmlspecialchars($row['orientacio']); ?></td>
            <td><?php echo htmlspecialchars($row['tipus_sol']); ?></td>
            <td><?php echo htmlspecialchars($row['nom_sector']); ?></td>
            <td><?php echo htmlspecialchars($row['sup_sector']); ?></td>
            <td><?php echo htmlspecialchars($row['estat_productiu']); ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p>No hi ha resultats amb aquests filtres.</p>
<?php endif; ?>
  </div>
</div>

<?php 
// Prepare JS data
$parceles_js = json_encode(array_values($featuresParceles ?? []));
$sectors_js  = json_encode(array_values($featuresSectors  ?? []));

$conn->close();
?>

<script>
  // Basic Leaflet map
  const map = L.map('map');
  const osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; OpenStreetMap'
  }).addTo(map);

  // Data from PHP
  const parceles = <?php echo $parceles_js ?: '[]'; ?>;
  const sectors  = <?php echo $sectors_js  ?: '[]'; ?>;

  // Layers and index by id
  const parcelaLayer = L.geoJSON(null, {
    style: { color: '#1f78b4', weight: 2, fillOpacity: 0.15 }
  }).addTo(map);
  const sectorLayer = L.geoJSON(null, {
    style: { color: '#33a02c', weight: 2, fillOpacity: 0.15 }
  }).addTo(map);
  const byParcelaId = new Map();
  const bySectorId = new Map();

  // Helpers
  function fitIfPossible(group) {
    try {
      const b = group.getBounds();
      if (b.isValid()) map.fitBounds(b.pad(0.2));
    } catch(e) {}
  }
  function highlightLayer(layer, on) {
    if (!layer) return;
    layer.setStyle(on ? { weight: 4, fillOpacity: 0.25 } : { weight: 2, fillOpacity: 0.15 });
  }

  // Add features
  parceles.forEach(p => {
    if (!p.geojson) return;
    const layer = L.geoJSON(p.geojson);
    layer.eachLayer(l => {
      l.options.pId = p.id;
      l.bindTooltip((p.nom || ('Parcela '+p.id)), {sticky:true});
      l.on('click', () => selectParcelaInTable(p.id));
    });
    parcelaLayer.addLayer(layer);
    byParcelaId.set(p.id, layer);
  });

  sectors.forEach(s => {
    if (!s.geojson) return;
    const layer = L.geoJSON(s.geojson);
    layer.eachLayer(l => {
      l.options.sId = s.id;
      l.bindTooltip((s.nom || ('Sector '+s.id)), {sticky:true});
      l.on('click', () => selectSectorInTable(s.id));
    });
    sectorLayer.addLayer(layer);
    bySectorId.set(s.id, layer);
  });

  // Initial view
  const group = L.featureGroup([parcelaLayer, sectorLayer]);
  fitIfPossible(group);

  // Table interactions
  const table = document.getElementById('resultTable');
  if (table) {
    table.addEventListener('click', (ev) => {
      const tr = ev.target.closest('tr.clickable');
      if (!tr) return;
      const pid = tr.getAttribute('data-parcela-id');
      const sid = tr.getAttribute('data-sector-id');
      setHighlightedRow(tr);
      if (pid) zoomParcela(parseInt(pid,10));
      if (sid) zoomSector(parseInt(sid,10));
    });
  }

  function setHighlightedRow(tr) {
    document.querySelectorAll('#resultTable tr.highlight').forEach(r => r.classList.remove('highlight'));
    if (tr) tr.classList.add('highlight');
  }

  function selectParcelaInTable(pid) {
    const tr = document.querySelector(`#resultTable tr[data-parcela-id="${pid}"]`);
    if (tr) {
      tr.scrollIntoView({behavior:'smooth', block:'center'});
      setHighlightedRow(tr);
    }
    zoomParcela(pid);
  }

  function selectSectorInTable(sid) {
    const tr = document.querySelector(`#resultTable tr[data-sector-id="${sid}"]`);
    if (tr) {
      tr.scrollIntoView({behavior:'smooth', block:'center'});
      setHighlightedRow(tr);
    }
    zoomSector(sid);
  }

  let lastHighlighted = null;
  function zoomParcela(pid) {
    const layer = byParcelaId.get(pid);
    if (!layer) return;
    if (lastHighlighted) highlightLayer(lastHighlighted, false);
    lastHighlighted = layer;
    highlightLayer(layer, true);
    fitIfPossible(layer);
  }
  function zoomSector(sid) {
    const layer = bySectorId.get(sid);
    if (!layer) return;
    if (lastHighlighted) highlightLayer(lastHighlighted, false);
    lastHighlighted = layer;
    highlightLayer(layer, true);
    fitIfPossible(layer);
  }
</script>

</body>
</html>
