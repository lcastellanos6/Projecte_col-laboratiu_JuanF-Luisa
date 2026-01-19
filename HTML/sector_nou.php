<?php
session_start();
$old = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);

function old($key, $default = '') {
    global $old;
    return htmlspecialchars($old[$key] ?? $default, ENT_QUOTES, 'UTF-8');
}

function selected($key, $value, $fallback = '') {
    global $old;
    $current = $old[$key] ?? $fallback;
    return ($current === $value) ? 'selected' : '';
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
  <meta charset="utf-8" />
  <title>Registrar nou sector</title>
  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet-draw/dist/leaflet.draw.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>
  <div style="padding:15px; background:#f4fff4; border-bottom:1px solid #ddd; text-align:right;">
    <a href="#" class="btn btn-primary" onclick="history.back(); return false;"><i class="fa-solid fa-arrow-left"></i> Tornar</a>
  </div>
  <div class="page">
    <div class="page-header">
      <h1>Registrar nou sector</h1>
      <p class="page-subtitle">Dibuixa el sector al mapa i completa les dades bàsiques.</p>
    </div>

    <div id="form-error" style="display:none; padding:10px 12px; margin:12px 0; border:1px solid #e3b2b2; background:#fff5f5; color:#8a2a2a; border-radius:6px;"></div>

    <div class="panel">
      <h2 class="panel-title">Mapa del sector</h2>
      <div id="map" style="height:400px;"></div>
    </div>

    <div class="panel">
      <h2 class="panel-title">Dades del sector</h2>
      <form id="form" action="../PHP/guardar_sector.php" method="post">
        <label>Nom del sector:</label>
        <input type="text" name="nom" required value="<?= old('nom') ?>">

        <label>Superfície (ha):</label>
        <input type="number" step="0.01" name="superficie" id="superficie" value="<?= old('superficie') ?>">

        <input type="hidden" name="geometria" id="geometria" value="<?= old('geometria') ?>">
        <input type="hidden" name="geometria_kml" id="geometria_kml" value="<?= old('geometria_kml') ?>">

        <label>URL de la foto:</label>
        <input type="text" name="foto_url" placeholder="https://..." value="<?= old('foto_url') ?>">

        <label>Estat productiu:</label>
        <select name="estat_productiu">
          <option value="Plantat" <?= selected('estat_productiu', 'Plantat', 'Plantat') ?>>Plantat</option>
          <option value="Repos" <?= selected('estat_productiu', 'Repos') ?>>Repos</option>
          <option value="Productiu" <?= selected('estat_productiu', 'Productiu') ?>>Productiu</option>
          <option value="Reconvertit" <?= selected('estat_productiu', 'Reconvertit') ?>>Reconvertit</option>
          <option value="Abandonat" <?= selected('estat_productiu', 'Abandonat') ?>>Abandonat</option>
        </select>

        <button type="submit" class="btn btn-primary btn-full mt-2">Guardar sector</button>
        <a class="btn btn-ghost btn-full mt-2" href="../PHP/consulta_parcela_sector.php">Tornar a la consulta</a>
      </form>
    </div>
  </div>

  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script src="https://unpkg.com/leaflet-draw/dist/leaflet.draw.js"></script>
  <script src="https://unpkg.com/@turf/turf@6/turf.min.js"></script>

  <script>
    (function () {
      var params = new URLSearchParams(window.location.search);
      var error = params.get('error');
      var box = document.getElementById('form-error');
      if (!box || !error) return;
      if (error === 'required') {
        box.textContent = 'Completa el camp obligatori: nom del sector.';
      } else if (error === 'geometry') {
        box.textContent = 'Dibuixa el sector al mapa per continuar.';
      } else if (error === 'save') {
        box.textContent = 'No s\'ha pogut guardar el sector. Torna-ho a provar.';
      } else {
        return;
      }
      box.style.display = 'block';
    })();
  </script>

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
      var geoJSONstring = JSON.stringify(geojson);
      document.getElementById('geometria').value = geoJSONstring;
      document.getElementById('geometria_kml').value = geoJSONstring;
      var area_m2 = turf.area(geojson);
      document.getElementById("superficie").value = (area_m2/10000).toFixed(2);
    }

    function restoreGeometry() {
      var raw = document.getElementById('geometria').value;
      if (!raw) return;
      try {
        var geojson = JSON.parse(raw);
        var restored = L.geoJSON(geojson);
        drawnItems.clearLayers();
        restored.eachLayer(function(layer){
          drawnItems.addLayer(layer);
        });
        if (drawnItems.getLayers().length) {
          map.fitBounds(drawnItems.getBounds());
          updateGeometry(drawnItems.getLayers()[0]);
        }
      } catch (e) {
      }
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

    restoreGeometry();
  </script>

</body>
</html>
