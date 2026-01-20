<?php
session_start();
$old = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);

function old($key, $default = '') {
    global $old;
    return htmlspecialchars($old[$key] ?? $default, ENT_QUOTES, 'UTF-8');
}

function selected($key, $value) {
    global $old;
    return (isset($old[$key]) && $old[$key] === $value) ? 'selected' : '';
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
  <meta charset="utf-8" />
  <title>Registrar nova parcel·la</title>
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
      <h1>Registrar nova parcel·la</h1>
      <p class="page-subtitle">Dibuixa la parcel·la al mapa i completa les dades bàsiques.</p>
    </div>

    <div id="form-error" style="display:none; padding:10px 12px; margin:12px 0; border:1px solid #e3b2b2; background:#fff5f5; color:#8a2a2a; border-radius:6px;"></div>

    <div class="panel">
      <h2 class="panel-title">Mapa de la parcel·la</h2>
      <div id="map" style="height:400px;"></div>
    </div>

    <div class="panel">
      <h2 class="panel-title">Dades de la parcel·la</h2>
      <form id="form" action="../PHP/guardar_parcela.php" method="post" enctype="multipart/form-data">
        <label>Referència cadastral:</label>
        <input type="text" name="ref_cadastral" required value="<?= old('ref_cadastral') ?>">

        <label>Nom descriptiu:</label>
        <input type="text" name="nom" required value="<?= old('nom') ?>">

        <label>Descripció:</label>
        <textarea name="descripcio"><?= old('descripcio') ?></textarea>

        <label>Municipi:</label>
        <input type="text" name="municipi" value="<?= old('municipi') ?>">

        <label>Superfície (ha):</label>
        <input type="number" step="0.01" name="superficie" id="superficie" value="<?= old('superficie') ?>">

        <input type="hidden" name="geometria" id="geometria" value="<?= old('geometria') ?>">
        <input type="hidden" name="geometria_kml" id="geometria_kml" value="<?= old('geometria_kml') ?>">

        <label>Tipus de sòl:</label>
        <input type="text" name="tipus_sol" value="<?= old('tipus_sol') ?>">

        <label>Pendent (%):</label>
        <input type="number" step="0.01" name="pendent" value="<?= old('pendent') ?>">

        <label>Orientació:</label>
        <select name="orientacio">
          <option value="">--Selecciona--</option>
          <option value="N" <?= selected('orientacio', 'N') ?>>Nord</option>
          <option value="S" <?= selected('orientacio', 'S') ?>>Sud</option>
          <option value="E" <?= selected('orientacio', 'E') ?>>Est</option>
          <option value="O" <?= selected('orientacio', 'O') ?>>Oest</option>
          <option value="NE" <?= selected('orientacio', 'NE') ?>>Nord-est</option>
          <option value="NO" <?= selected('orientacio', 'NO') ?>>Nord-oest</option>
          <option value="SE" <?= selected('orientacio', 'SE') ?>>Sud-est</option>
          <option value="SO" <?= selected('orientacio', 'SO') ?>>Sud-oest</option>
        </select>

        <label>Edafologia:</label>
        <textarea name="edafo"><?= old('edafo') ?></textarea>

        <label>Documentació:</label>
        <textarea name="documentacio"><?= old('documentacio') ?></textarea>

        <label>Foto de la parcel·la:</label>
        <input type="file" name="foto" accept="image/*">

        <button type="submit" class="btn btn-primary btn-full mt-2">Guardar parcel·la</button>
        <a class="btn btn-ghost btn-full mt-2" href="../PHP/consulta_parcela_sector.php">Tornar a la consulta</a>
      </form>
    </div>
  </div>

  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script src="https://unpkg.com/leaflet-draw/dist/leaflet.draw.js"></script>
  <script src="https://unpkg.com/@turf/turf@6/turf.min.js"></script>

  <script>
    function openPage(url) {
      if (window.parent && window.parent !== window && typeof window.parent.openPage === 'function') {
        window.parent.openPage(url);
        return;
      }
      window.location.href = url;
    }
  </script>

  <script>
    (function () {
      var params = new URLSearchParams(window.location.search);
      var error = params.get('error');
      var box = document.getElementById('form-error');
      if (!box || !error) return;
      if (error === 'ref_cadastral_dup') {
        box.textContent = 'Ya existe una parcela con esa referencia catastral. Revisa el dato o usa otra referencia.';
      } else if (error === 'required') {
        box.textContent = 'Completa los campos obligatorios: referencia catastral y nombre descriptivo.';
      } else if (error === 'save') {
        box.textContent = 'No se ha podido guardar la parcela. Intentalo de nuevo.';
      } else if (error === 'geometry') {
        box.textContent = 'No hi ha geometria. Dibuixa la parcel·la al mapa per continuar.';
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
