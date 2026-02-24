<?php
require_once __DIR__ . '/db.php';

$conn = db_connect();
$stmt = $conn->prepare("SELECT id_parcela, ref_cadastral, nom FROM parcela ORDER BY id_parcela DESC");
$stmt->execute();
$result = $stmt->get_result();
$parceles = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $parceles[] = $row;
    }
}
$te_parceles = count($parceles) > 0;
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Registrar infraestructura</title>
    <link rel="stylesheet" href="../HTML/styles.css">
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
      <h1>Registrar nova infraestructura</h1>
      <p class="page-subtitle">Dibuixa la infraestructura al mapa i completa les dades bàsiques.</p>
    </div>

    <div id="form-error" style="display:none; padding:10px 12px; margin:12px 0; border:1px solid #e3b2b2; background:#fff5f5; color:#8a2a2a; border-radius:6px;"></div>

    <div class="panel">
      <h2 class="panel-title">Mapa de la infraestructura</h2>
      <div id="map" style="height:400px;"></div>
    </div>

    <div class="panel">
      <h2 class="panel-title">Dades de la infraestructura</h2>
      <form id="form" action="guardar_infraestructura.php" method="post" enctype="multipart/form-data">
        <label>Parcel·la:</label>
        <select name="id_parcela" id="id_parcela" required <?= $te_parceles ? '' : 'disabled' ?>>
          <?php if ($te_parceles): ?>
            <option value="">--Selecciona--</option>
            <?php foreach ($parceles as $parcela): ?>
              <?php
                $id = htmlspecialchars($parcela['id_parcela'], ENT_QUOTES, 'UTF-8');
                $ref = htmlspecialchars($parcela['ref_cadastral'], ENT_QUOTES, 'UTF-8');
                $nom = $parcela['nom'] ?? '';
                $nomText = $nom !== '' ? ' - ' . htmlspecialchars($nom, ENT_QUOTES, 'UTF-8') : '';
              ?>
              <option value="<?= $id ?>"><?= $id ?> - <?= $ref ?><?= $nomText ?></option>
            <?php endforeach; ?>
          <?php else: ?>
            <option value="">No hi ha parcel·les disponibles</option>
          <?php endif; ?>
        </select>
        <?php if (!$te_parceles): ?>
          <p style="margin-top:8px; color:#8a2a2a;">No hi ha parcel·les disponibles. Crea una parcel·la abans de registrar infraestructura.</p>
          <a class="btn btn-ghost btn-full mt-2" href="../HTML/parcela_nou.php">Crear parcel·la</a>
        <?php endif; ?>

        <label>Tipus d'infraestructura:</label>
        <input type="text" name="tipus" required>

        <label>Descripció:</label>
        <textarea name="descripcio"></textarea>

        <input type="hidden" name="geometria_kml" id="geometria_kml">

        <label>Foto (opcional):</label>
        <input type="file" name="foto" accept="image/*">

        <button type="submit" class="btn btn-primary btn-full mt-2" <?= $te_parceles ? '' : 'disabled' ?>>Guardar infraestructura</button>
        <a class="btn btn-ghost btn-full mt-2" href="#" onclick="history.back(); return false;">Tornar a la consulta</a>
      </form>
    </div>
  </div>

  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script src="https://unpkg.com/leaflet-draw/dist/leaflet.draw.js"></script>

  <script>
    var form = document.getElementById('form');
    var errorBox = document.getElementById('form-error');
    var parcelaSelect = document.getElementById('id_parcela');

    function showError(message) {
      if (!errorBox) return;
      errorBox.textContent = message;
      errorBox.style.display = 'block';
    }

    function clearError() {
      if (!errorBox) return;
      errorBox.textContent = '';
      errorBox.style.display = 'none';
    }

    form.addEventListener('submit', function (e) {
      var raw = document.getElementById('geometria_kml').value;
      if (!raw) {
        e.preventDefault();
        showError('No hi ha geometria. Dibuixa la infraestructura al mapa per continuar.');
      } else {
        clearError();
      }
    });

    var map = L.map('map').setView([41.65, 1.0], 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 22
    }).addTo(map);

    var drawnItems = new L.FeatureGroup();
    map.addLayer(drawnItems);
    var parcelaLayer = L.geoJSON(null, {
      style: {
        color: '#2f7d32',
        weight: 2,
        fillOpacity: 0.08
      }
    }).addTo(map);

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
      document.getElementById('geometria_kml').value = geoJSONstring;
      clearError();
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

    map.on(L.Draw.Event.EDITSTART, function () {
      if (drawnItems.getLayers().length === 0) {
        showError('No hi ha cap geometria per editar. Dibuixa-la primer.');
        if (drawControl && drawControl._toolbars && drawControl._toolbars.edit) {
          drawControl._toolbars.edit._modes.edit.handler.disable();
        }
      }
    });

    map.on(L.Draw.Event.DELETESTART, function () {
      if (drawnItems.getLayers().length === 0) {
        showError('No hi ha cap geometria per eliminar. Dibuixa-la primer.');
        if (drawControl && drawControl._toolbars && drawControl._toolbars.edit) {
          drawControl._toolbars.edit._modes.remove.handler.disable();
        }
      }
    });

    function loadParcelaLimits(idParcela) {
      if (!idParcela) {
        parcelaLayer.clearLayers();
        return;
      }
      fetch('ajax_parcela_geojson.php?id_parcela=' + encodeURIComponent(idParcela))
        .then(function (response) { return response.json(); })
        .then(function (data) {
          parcelaLayer.clearLayers();
          if (!data || !data.geojson) {
            showError('No s\'ha pogut carregar la geometria de la parcel·la.');
            return;
          }
          var layer = L.geoJSON(data.geojson);
          layer.eachLayer(function (l) { parcelaLayer.addLayer(l); });
          if (parcelaLayer.getLayers().length) {
            map.fitBounds(parcelaLayer.getBounds(), { padding: [20, 20] });
          }
        })
        .catch(function () {
          showError('No s\'ha pogut carregar la geometria de la parcel·la.');
        });
    }

    if (parcelaSelect) {
      parcelaSelect.addEventListener('change', function () {
        loadParcelaLimits(parcelaSelect.value);
      });
      if (parcelaSelect.value) {
        loadParcelaLimits(parcelaSelect.value);
      }
    }
  </script>
</body>
</html>
