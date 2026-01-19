<?php
$conn = new mysqli("localhost","root","","web");
$id = intval($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT * FROM parcela WHERE id_parcela=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$p = $res ? $res->fetch_assoc() : null;
$stmt->close();

if (!$p) {
    echo "<p style='color:red; font-weight:bold;'>Parcel·la no trobada.</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="ca">
<head>
<meta charset="utf-8">
<title>Editar parcel·la</title>
<link rel="stylesheet" href="../HTML/styles.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-draw/dist/leaflet.draw.css"/>
</head>
<style>
body { font-family: Arial, sans-serif; background:#f5fff5; padding:20px; }
.page-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  max-width: 500px;
  margin: 0 auto 16px;
}
.page-header h2 {
  margin: 0;
}
  .map-panel {
    max-width: 500px;
    margin: 0 auto 16px;
    background: white;
    padding: 12px;
    border-radius: 8px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
  }
  #map {
    height: 320px;
    border-radius: 6px;
  }
form { background:white; padding:20px; border-radius:8px; max-width:500px; margin:auto;
       box-shadow:0 2px 6px rgba(0,0,0,0.1); }
label { font-weight:bold; margin-top:10px; display:block; }
input, textarea, button { width:100%; padding:8px; margin-top:5px; border-radius:4px; border:1px solid #ccc; }
button { margin-top:15px; background:#2f7d2f; color:white; padding:10px; border:none;
         border-radius:5px; cursor:pointer; width:100%; }
button:hover { background:#3d9b3d; }
</style>
<body>

<div class="page-header">
  <h2>Editar parcel·la</h2>
  <a class="btn btn-primary" href="#" onclick="history.back(); return false;">&larr; Tornar</a>
</div>

<div class="map-panel">
  <strong>Mapa de la parcel·la</strong>
  <div id="map"></div>
</div>

<form method="post" action="../PHP/guardar_edicion_parcela.php" enctype="multipart/form-data">
<input type="hidden" name="id" value="<?= $p['id_parcela'] ?>">

<label>ID</label><br>
<input type="text" value="<?= htmlspecialchars($p['id_parcela']) ?>" readonly><br>

<label>Creat</label><br>
<input type="text" value="<?= htmlspecialchars($p['created_at']) ?>" readonly><br>

<label>Referència cadastral</label><br>
<input type="text" name="ref_cadastral" value="<?= htmlspecialchars($p['ref_cadastral']) ?>" required><br>

<label>Nom</label><br>
<input type="text" name="nom" value="<?= htmlspecialchars($p['nom']) ?>"><br>

<label>Superfície (ha)</label><br>
<input type="number" step="0.01" name="superficie" id="superficie" value="<?= htmlspecialchars($p['superficie']) ?>"><br>

<label>Descripció</label><br>
<textarea name="descripcio"><?= htmlspecialchars($p['descripcio']) ?></textarea><br>

<label>Municipi</label><br>
<input type="text" name="municipi" value="<?= htmlspecialchars($p['municipi']) ?>"><br>

<input type="hidden" name="geometria" id="geometria" value="<?= htmlspecialchars($p['geometria_kml']) ?>">
<input type="hidden" name="geometria_kml" id="geometria_kml" value="<?= htmlspecialchars($p['geometria_kml']) ?>">

<label>Tipus sòl</label><br>
<input type="text" name="tipus_sol" value="<?= htmlspecialchars($p['tipus_sol']) ?>"><br>

<label>Pendent</label><br>
<input type="number" step="0.01" name="pendent" value="<?= $p['pendent'] ?>"><br><br>

<label>Orientació</label><br>
<select name="orientacio">
  <option value="">--Selecciona--</option>
  <?php
  $opcions = ['N','S','E','O','NE','NO','SE','SO'];
  foreach ($opcions as $op) {
      $sel = ($op === ($p['orientacio'] ?? '')) ? 'selected' : '';
      echo "<option value=\"$op\" $sel>$op</option>";
  }
  ?>
</select><br>

<label>Edafologia</label><br>
<textarea name="edafo"><?= htmlspecialchars($p['edafo']) ?></textarea><br>

<label>Documentació</label><br>
<textarea name="documentacio"><?= htmlspecialchars($p['documentacio']) ?></textarea><br>

<label>Foto de la parcel·la</label><br>
<?php if (!empty($p['foto_url'])): ?>
  <p>Actual: <?= htmlspecialchars($p['foto_url']) ?></p>
<?php endif; ?>
<input type="file" name="foto" accept="image/*"><br>

<button type="submit">Guardar</button>
<a href="consulta_parcela_sector.php"><button type="button">Cancel·lar</button></a>
</form>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-draw/dist/leaflet.draw.js"></script>
<script src="https://unpkg.com/@turf/turf@6/turf.min.js"></script>
<script>
  var map = L.map('map').setView([41.65, 1.0], 15);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 22
  }).addTo(map);

  var drawnItems = new L.FeatureGroup();
  map.addLayer(drawnItems);

  var drawControl = new L.Control.Draw({
    draw: {
      polygon: true,
      rectangle: true,
      circle: false,
      marker: false,
      polyline: false
    },
    edit: {
      featureGroup: drawnItems
    }
  });
  map.addControl(drawControl);

  function updateGeometry(layer) {
    var geojson = layer.toGeoJSON();
    if (!geojson || !geojson.geometry) {
      return;
    }
    var geoJSONstring = JSON.stringify(geojson);
    document.getElementById('geometria').value = geoJSONstring;
    document.getElementById('geometria_kml').value = geoJSONstring;
    var area_m2 = turf.area(geojson);
    var superficie = document.getElementById("superficie");
    if (superficie && area_m2 > 0) {
      superficie.value = (area_m2 / 10000).toFixed(2);
    }
  }

  var existingGeojson = <?php echo json_encode($p['geometria_kml'] ?? ''); ?>;
  if (existingGeojson) {
    try {
      var parsed = JSON.parse(existingGeojson);
      var existingLayer = L.geoJSON(parsed);
      existingLayer.eachLayer(function(layer){
        drawnItems.addLayer(layer);
        updateGeometry(layer);
      });
      map.fitBounds(existingLayer.getBounds());
    } catch (e) {}
  }

  map.on(L.Draw.Event.CREATED, function (e) {
    drawnItems.clearLayers();
    drawnItems.addLayer(e.layer);
    updateGeometry(e.layer);
  });

  map.on(L.Draw.Event.EDITED, function (e) {
    e.layers.eachLayer(function(layer){
      updateGeometry(layer);
    });
  });
</script>
</body>
</html>
