<?php
$conn = new mysqli("localhost","root","","web");
$id = intval($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT p.*, ST_AsGeoJSON(p.geometria) AS geometria_geojson FROM parcela p WHERE p.id_parcela=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$p = $res ? $res->fetch_assoc() : null;
$stmt->close();

if (!$p) {
    echo "<p style='color:red; font-weight:bold;'>Parcel·la no trobada.</p>";
    exit;
}

$sols = [];
$stmt = $conn->prepare("SELECT id_sol, tipus FROM sol ORDER BY tipus");
$stmt->execute();
$res = $stmt->get_result();
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $sols[] = $row;
    }
}
$stmt->close();

$parcela_sols = [];
$stmt = $conn->prepare("SELECT id_sol FROM parcela_sol WHERE id_parcela=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $parcela_sols[] = (int)$row['id_sol'];
    }
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="ca">
<head>
<meta charset="utf-8">
<title>Editar parcel·la</title>
<link rel="stylesheet" href="../HTML/styles.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-draw/dist/leaflet.draw.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
  #map {
    width: 100%;
    min-height: 420px;
    border-radius: 0.75rem;
    border: 1px solid #aacbaa;
  }
</style>
</head>
<body>
  <div style="padding:15px; background:#f4fff4; border-bottom:1px solid #ddd; text-align:right;">
    <a href="#" class="btn btn-primary" onclick="history.back(); return false;"><i class="fa-solid fa-arrow-left"></i> Tornar</a>
  </div>

  <div class="page">
    <div class="page-header">
      <h1>Editar parcel·la</h1>
      <p class="page-subtitle">Actualitza les dades i ajusta la geometria si cal.</p>
    </div>

    <div class="panel">
      <h2 class="panel-title">Mapa de la parcel·la</h2>
      <div id="map-error" style="display:none; padding:10px 12px; margin-bottom:12px; border:1px solid #e3b2b2; background:#fff5f5; color:#8a2a2a; border-radius:6px;"></div>
      <div id="map"></div>
    </div>

    <div class="panel">
      <h2 class="panel-title">Dades de la parcel·la</h2>
      <form method="post" action="../PHP/guardar_edicion_parcela.php" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $p['id_parcela'] ?>">

        <label>Creat</label>
        <input type="text" value="<?= htmlspecialchars($p['created_at']) ?>" readonly>

        <label>Referència cadastral</label>
        <input type="text" name="ref_cadastral" value="<?= htmlspecialchars($p['ref_cadastral']) ?>" required>

        <label>Nom</label>
        <input type="text" name="nom" value="<?= htmlspecialchars($p['nom']) ?>">

        <label>Superfície (ha)</label>
        <input type="number" step="0.01" name="superficie" id="superficie" value="<?= htmlspecialchars($p['superficie']) ?>">

        <label>Descripció</label>
        <textarea name="descripcio"><?= htmlspecialchars($p['descripcio']) ?></textarea>

        <label>Municipi</label>
        <input type="text" name="municipi" value="<?= htmlspecialchars($p['municipi']) ?>">

        <input type="hidden" name="geometria" id="geometria" value="<?= htmlspecialchars($p['geometria_kml']) ?>">
        <input type="hidden" name="geometria_kml" id="geometria_kml" value="<?= htmlspecialchars($p['geometria_kml']) ?>">

        <label>Tipus de sòl</label>
        <?php if (!empty($sols)): ?>
          <p style="margin-top:6px; color:#4c6b4c;">Pots marcar més d'un sòl.</p>
          <div class="multiselect" data-multiselect>
            <button type="button" class="multiselect-toggle">Selecciona sòls</button>
            <div class="multiselect-panel">
              <div class="checkbox-list">
                <?php foreach ($sols as $sol): ?>
                  <?php
                    $id_sol = (int)$sol['id_sol'];
                    $is_checked = in_array($id_sol, $parcela_sols, true) ? 'checked' : '';
                  ?>
                  <label class="checkbox-item">
                    <input type="checkbox" name="id_sol[]" value="<?= $id_sol ?>" <?= $is_checked ?>>
                    <span><?= htmlspecialchars($sol['tipus'], ENT_QUOTES, 'UTF-8') ?></span>
                  </label>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        <?php else: ?>
          <p style="margin-top:8px; color:#8a2a2a;">No hi ha sòls disponibles. Crea un sòl abans d'assignar-lo.</p>
        <?php endif; ?>

        <label>Pendent</label>
        <input type="number" step="0.01" name="pendent" value="<?= $p['pendent'] ?>">

        <label>Orientació</label>
        <select name="orientacio">
          <option value="">--Selecciona--</option>
          <?php
          $opcions = ['N','S','E','O','NE','NO','SE','SO'];
          foreach ($opcions as $op) {
              $sel = ($op === ($p['orientacio'] ?? '')) ? 'selected' : '';
              echo "<option value=\"$op\" $sel>$op</option>";
          }
          ?>
        </select>

        <label>Edafologia</label>
        <textarea name="edafo"><?= htmlspecialchars($p['edafo']) ?></textarea>

        <label>Documentació</label>
        <textarea name="documentacio"><?= htmlspecialchars($p['documentacio']) ?></textarea>

        <label>Foto de la parcel·la</label>
        <?php if (!empty($p['foto_url'])): ?>
          <p>Actual: <?= htmlspecialchars($p['foto_url']) ?></p>
        <?php endif; ?>
        <input type="file" name="foto" accept="image/*">

        <button type="submit" class="btn btn-primary btn-full mt-2">Guardar</button>
        <a class="btn btn-ghost btn-full mt-2" href="consulta_parcela_sector.php">Cancel·lar</a>
      </form>
    </div>
  </div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-draw/dist/leaflet.draw.js"></script>
<script src="https://unpkg.com/@turf/turf@6/turf.min.js"></script>
<script>
  var map = L.map('map').setView([41.65, 1.0], 15);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; OpenStreetMap'
  }).addTo(map);

  var drawnItems = new L.FeatureGroup();
  map.addLayer(drawnItems);
  var drawStyle = { color: '#1f78b4', weight: 2, fillOpacity: 0.2 };

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

  var mapError = document.getElementById('map-error');

  function showMapError(message) {
    if (!mapError) return;
    mapError.textContent = message;
    mapError.style.display = 'block';
  }

  function loadExistingGeometry(raw) {
    if (!raw) return false;
    try {
      var parsed = JSON.parse(raw);
      var existingLayer = L.geoJSON(parsed, { style: drawStyle });
      existingLayer.eachLayer(function(layer){
        if (layer.setStyle) {
          layer.setStyle(drawStyle);
        }
        drawnItems.addLayer(layer);
        updateGeometry(layer);
      });
      map.fitBounds(existingLayer.getBounds().pad(0.2));
      return true;
    } catch (e) {
      return false;
    }
  }

  var existingGeojson = <?php echo json_encode($p['geometria_kml'] ?? ''); ?>;
  var fallbackGeojson = <?php echo json_encode($p['geometria_geojson'] ?? ''); ?>;
  var loaded = loadExistingGeometry(existingGeojson);
  if (!loaded) {
    loaded = loadExistingGeometry(fallbackGeojson);
  }
  if (!loaded) {
    showMapError('No s\'ha pogut carregar la geometria de la parcel·la.');
  }

  map.on(L.Draw.Event.CREATED, function (e) {
    drawnItems.clearLayers();
    if (e.layer && e.layer.setStyle) {
      e.layer.setStyle(drawStyle);
    }
    drawnItems.addLayer(e.layer);
    updateGeometry(e.layer);
  });

  map.on(L.Draw.Event.EDITED, function (e) {
    e.layers.eachLayer(function(layer){
      updateGeometry(layer);
    });
  });
</script>
<script>
  (function () {
    function initMultiselect(root) {
      var toggle = root.querySelector('.multiselect-toggle');
      var panel = root.querySelector('.multiselect-panel');
      var checkboxes = root.querySelectorAll('input[type="checkbox"]');
      if (!toggle || !panel) return;

      function updateLabel() {
        var labels = [];
        checkboxes.forEach(function (cb) {
          if (cb.checked) {
            var text = cb.parentElement ? cb.parentElement.innerText.trim() : '';
            if (text) labels.push(text);
          }
        });
        if (labels.length === 0) {
          toggle.textContent = 'Cap sòl seleccionat';
        } else if (labels.length <= 2) {
          toggle.textContent = labels.join(', ');
        } else {
          toggle.textContent = labels.length + ' sòls seleccionats';
        }
      }

      toggle.addEventListener('click', function () {
        root.classList.toggle('is-open');
      });

      document.addEventListener('click', function (e) {
        if (!root.contains(e.target)) {
          root.classList.remove('is-open');
        }
      });

      checkboxes.forEach(function (cb) {
        cb.addEventListener('change', updateLabel);
      });

      updateLabel();
    }

    document.querySelectorAll('[data-multiselect]').forEach(initMultiselect);
  })();
</script>
</body>
</html>
