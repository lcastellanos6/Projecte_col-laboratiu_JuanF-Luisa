<?php
$conn = new mysqli("localhost", "root", "", "web");
if ($conn->connect_error) {
    die("Error de connexió: " . $conn->connect_error);
}

$id_parcela = isset($_GET['id_parcela']) ? intval($_GET['id_parcela']) : 0;
if ($id_parcela <= 0) {
    echo "<p style='color:red; font-weight:bold;'>Parcel·la no trobada.</p>";
    exit;
}

function h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function show_value($value) {
    if ($value === null || $value === '') {
        return '-';
    }
    return h($value);
}

function format_number($value, $decimals = 2) {
    if ($value === null || $value === '') {
        return '-';
    }
    if (!is_numeric($value)) {
        return h($value);
    }
    return number_format((float)$value, $decimals, ',', '.');
}

function format_date($value) {
    if ($value === null || $value === '') {
        return '-';
    }
    $ts = strtotime($value);
    if ($ts === false) {
        return h($value);
    }
    return date('d/m/Y H:i', $ts);
}

// Parcel·la
$stmt = $conn->prepare("
    SELECT p.*, ST_AsGeoJSON(p.geometria) AS geometria_geojson
    FROM parcela p
    WHERE p.id_parcela = ?
");
$stmt->bind_param("i", $id_parcela);
$stmt->execute();
$res = $stmt->get_result();
$parcela = $res ? $res->fetch_assoc() : null;
$stmt->close();

if (!$parcela) {
    echo "<p style='color:red; font-weight:bold;'>Parcel·la no trobada.</p>";
    exit;
}

// Sols de la parcel·la
$parcela_sols = [];
$stmt = $conn->prepare("
    SELECT so.tipus
    FROM parcela_sol ps
    JOIN sol so ON so.id_sol = ps.id_sol
    WHERE ps.id_parcela = ?
    ORDER BY so.tipus
");
$stmt->bind_param("i", $id_parcela);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $parcela_sols[] = $row['tipus'];
}
$stmt->close();

// Sectors associats
$stmt = $conn->prepare("
    SELECT s.*, ST_AsGeoJSON(s.geometria) AS geometria_geojson
    FROM sector s
    JOIN sector_parcela sp ON sp.id_sector = s.id_sector
    WHERE sp.id_parcela = ?
    ORDER BY s.id_sector
");
$stmt->bind_param("i", $id_parcela);
$stmt->execute();
$res = $stmt->get_result();
$sectors = [];
while ($row = $res->fetch_assoc()) {
    $sectors[] = $row;
}
$stmt->close();

// Sols per sector
$sector_sols = [];
$stmt = $conn->prepare("
    SELECT ss.id_sector, so.id_sol, so.tipus, so.ph, so.materia_organica, so.observacions
    FROM sector_sol ss
    JOIN sol so ON so.id_sol = ss.id_sol
    JOIN sector_parcela sp ON sp.id_sector = ss.id_sector
    WHERE sp.id_parcela = ?
    ORDER BY ss.id_sector, so.id_sol
");
$stmt->bind_param("i", $id_parcela);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $sid = $row['id_sector'];
    if (!isset($sector_sols[$sid])) {
        $sector_sols[$sid] = [];
    }
    $sector_sols[$sid][] = $row;
}
$stmt->close();

// Varietats per sector
$sector_varietats = [];
$stmt = $conn->prepare("
    SELECT sv.id_sector, v.id_varietat, v.nom_cientific, v.nom_comu, d.id_data, d.data_inici, d.data_fi
    FROM sector_varietat sv
    JOIN varietat v ON v.id_varietat = sv.id_varietat
    JOIN data d ON d.id_data = sv.id_data
    JOIN sector_parcela sp ON sp.id_sector = sv.id_sector
    WHERE sp.id_parcela = ?
    ORDER BY sv.id_sector, v.id_varietat, d.id_data
");
$stmt->bind_param("i", $id_parcela);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $sid = $row['id_sector'];
    if (!isset($sector_varietats[$sid])) {
        $sector_varietats[$sid] = [];
    }
    $sector_varietats[$sid][] = $row;
}
$stmt->close();

// Preparem dades per al mapa
$parcela_feature = null;
if (!empty($parcela['geometria_geojson'])) {
    $parcela_feature = [
        'id' => $parcela['id_parcela'],
        'nom' => $parcela['nom'] ?? '',
        'geojson' => json_decode($parcela['geometria_geojson'], true),
    ];
}

$sector_features = [];
foreach ($sectors as $sector) {
    if (!empty($sector['geometria_geojson'])) {
        $sector_features[] = [
            'id' => $sector['id_sector'],
            'nom' => $sector['nom'] ?? '',
            'geojson' => json_decode($sector['geometria_geojson'], true),
        ];
    }
}

$parcela_js = json_encode($parcela_feature ? [$parcela_feature] : []);
$sectors_js = json_encode($sector_features);

$fallback_payload = [
    'parcela' => $parcela['geometria_geojson'] ?? null,
    'sectors' => array_map(function ($sector) {
        return [
            'id_sector' => $sector['id_sector'],
            'geojson' => $sector['geometria_geojson'] ?? null,
        ];
    }, $sectors),
];
$fallback_text = json_encode($fallback_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

$conn->close();
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Visualització de parcel·la</title>
    <link rel="stylesheet" href="../HTML/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <style>
        .page-header-actions {
            display: flex;
            gap: 0.75rem;
            justify-content: flex-end;
            flex-wrap: wrap;
        }
        .data-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            gap: 0.6rem 1.25rem;
            margin: 0;
        }
        .data-grid dt {
            font-weight: 600;
            color: #2f4f2f;
        }
        .data-grid dd {
            margin: 0;
            color: #445f44;
        }
        .photo-preview {
            max-width: 100%;
            border-radius: 0.6rem;
            border: 1px solid #d7e7d7;
            box-shadow: 0 6px 14px rgba(47, 125, 47, 0.12);
        }
        .sectors-list {
            display: grid;
            gap: 0.75rem;
        }
        .sector-card {
            border: 1px solid #d7e7d7;
            border-radius: 0.6rem;
            padding: 0.75rem 0.9rem;
            background: #f7fbf7;
            cursor: pointer;
            text-align: left;
        }
        .sector-card:hover {
            background: #eef6ee;
        }
        .sector-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            color: #4c6b4c;
            font-size: 0.9rem;
            margin-top: 0.35rem;
        }
        .sector-extra {
            margin-top: 0.6rem;
            color: #445f44;
            font-size: 0.9rem;
        }
        .sector-extra ul {
            margin: 0.25rem 0 0.5rem;
            padding-left: 1.1rem;
        }
        #map {
            width: 100%;
            min-height: 420px;
            border-radius: 0.75rem;
            border: 1px solid #aacbaa;
        }
        #geojsonFallback {
            background: #f7fbf7;
            border-radius: 0.75rem;
            border: 1px solid #aacbaa;
            padding: 0.9rem;
            color: #2f4f2f;
            overflow: auto;
            max-height: 320px;
        }
    </style>
</head>
<body>
<div class="page">
    <div class="page-header">
        <div class="panel-header">
            <div>
                <h1>Visualització de parcel·la</h1>
                <p class="page-subtitle">Detall complet de la parcel·la i sectors associats.</p>
            </div>
            <div class="page-header-actions">
                <a class="btn btn-primary" href="consulta_parcela_sector.php"><i class="fa-solid fa-arrow-left"></i> Tornar</a>
                <a class="btn btn-primary" href="editar_parcela.php?id=<?php echo intval($parcela['id_parcela']); ?>"><i class="fa-solid fa-pen-to-square"></i> Editar</a>
                <a class="btn btn-primary" href="eliminar_parcela.php?id=<?php echo intval($parcela['id_parcela']); ?>" onclick="return confirm('Segur que vols eliminar aquesta parcel·la?');"><i class="fa-solid fa-trash"></i> Eliminar</a>
            </div>
        </div>
    </div>

    <div class="layout-two-columns">
        <div>
            <div class="panel">
                <h2>Dades de la parcel·la</h2>
                <dl class="data-grid">
                    <dt>ID</dt><dd><?php echo show_value($parcela['id_parcela']); ?></dd>
                    <dt>Ref. cadastral</dt><dd><?php echo show_value($parcela['ref_cadastral']); ?></dd>
                    <dt>Nom</dt><dd><?php echo show_value($parcela['nom']); ?></dd>
                    <dt>Superfície</dt><dd><?php echo format_number($parcela['superficie']); ?></dd>
                    <dt>Municipi</dt><dd><?php echo show_value($parcela['municipi']); ?></dd>
                    <dt>Descripció</dt><dd><?php echo show_value($parcela['descripcio']); ?></dd>
                    <dt>Pendent</dt><dd><?php echo format_number($parcela['pendent']); ?></dd>
                    <dt>Orientació</dt><dd><?php echo show_value($parcela['orientacio']); ?></dd>
                    <dt>Tipus de sòl</dt><dd><?php echo show_value(!empty($parcela_sols) ? implode(', ', $parcela_sols) : null); ?></dd>
                    <dt>Edafo</dt><dd><?php echo show_value($parcela['edafo']); ?></dd>
                    <dt>Documentació</dt><dd><?php echo show_value($parcela['documentacio']); ?></dd>
                    <dt>Creat</dt><dd><?php echo format_date($parcela['created_at']); ?></dd>
                </dl>
            </div>

            <div class="panel mt-2">
                <h2>Fitxers / Foto</h2>
                <?php if (!empty($parcela['foto_url'])): ?>
                    <p><strong>URL foto:</strong> <?php echo h($parcela['foto_url']); ?></p>
                    <img class="photo-preview" src="<?php echo h($parcela['foto_url']); ?>" alt="Foto de la parcel·la">
                <?php else: ?>
                    <p>No hi ha foto associada.</p>
                <?php endif; ?>
            </div>

            <div class="panel mt-2">
                <h2>Sectors associats</h2>
                <?php if (empty($sectors)): ?>
                    <p>Sense sectors associats.</p>
                <?php else: ?>
                    <div class="sectors-list">
                        <?php foreach ($sectors as $sector): ?>
                            <button type="button" class="sector-card" data-sector-id="<?php echo intval($sector['id_sector']); ?>">
                                <strong><?php echo show_value($sector['nom']); ?></strong>
                                <div class="sector-meta">
                                    <span>ID: <?php echo show_value($sector['id_sector']); ?></span>
                                    <span>Superfície: <?php echo format_number($sector['superficie']); ?></span>
                                    <span>Estat: <?php echo show_value($sector['estat_productiu']); ?></span>
                                    <span>Creat: <?php echo format_date($sector['created_at']); ?></span>
                                </div>
                                <?php if (!empty($sector['foto_url'])): ?>
                                    <div class="sector-extra">Foto: <?php echo h($sector['foto_url']); ?></div>
                                <?php endif; ?>

                                <?php if (!empty($sector_sols[$sector['id_sector']])): ?>
                                    <div class="sector-extra">
                                        <strong>Sòls</strong>
                                        <ul>
                                            <?php foreach ($sector_sols[$sector['id_sector']] as $sol): ?>
                                                <li>
                                                    <?php echo h($sol['tipus']); ?>
                                                    (pH <?php echo format_number($sol['ph'], 2); ?>, MO <?php echo format_number($sol['materia_organica'], 2); ?>)
                                                    <?php if (!empty($sol['observacions'])): ?>
                                                        - <?php echo h($sol['observacions']); ?>
                                                    <?php endif; ?>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($sector_varietats[$sector['id_sector']])): ?>
                                    <div class="sector-extra">
                                        <strong>Varietats</strong>
                                        <ul>
                                            <?php foreach ($sector_varietats[$sector['id_sector']] as $var): ?>
                                                <li>
                                                    <?php echo h($var['nom_comu']); ?>
                                                    (<?php echo h($var['nom_cientific']); ?>)
                                                    - <?php echo format_date($var['data_inici']); ?>
                                                    <?php if (!empty($var['data_fi'])): ?>
                                                        a <?php echo format_date($var['data_fi']); ?>
                                                    <?php endif; ?>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div>
            <div class="panel">
                <h2>Mapa</h2>
                <div id="map"></div>
                <pre id="geojsonFallback"><?php echo h($fallback_text); ?></pre>
            </div>
        </div>
    </div>
</div>

<script>
  const parceles = <?php echo $parcela_js ?: '[]'; ?>;
  const sectors = <?php echo $sectors_js ?: '[]'; ?>;
  const fallback = document.getElementById('geojsonFallback');

  function hideFallback() {
    if (fallback) fallback.style.display = 'none';
  }

  if (typeof L !== 'undefined' && (parceles.length || sectors.length)) {
    const map = L.map('map');
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    const parcelaLayer = L.geoJSON(null, {
      style: { color: '#1f78b4', weight: 2, fillOpacity: 0.2 }
    }).addTo(map);
    const sectorLayer = L.geoJSON(null, {
      style: { color: '#33a02c', weight: 2, fillOpacity: 0.2 }
    }).addTo(map);

    const bySectorId = new Map();

    parceles.forEach(p => {
      if (!p.geojson) return;
      const layer = L.geoJSON(p.geojson);
      layer.eachLayer(l => {
        l.bindTooltip((p.nom || ('Parcel·la ' + p.id)), {sticky:true});
      });
      parcelaLayer.addLayer(layer);
    });

    sectors.forEach(s => {
      if (!s.geojson) return;
      const layer = L.geoJSON(s.geojson);
      layer.eachLayer(l => {
        l.options.sId = String(s.id);
        l.bindTooltip((s.nom || ('Sector ' + s.id)), {sticky:true});
      });
      sectorLayer.addLayer(layer);
      bySectorId.set(String(s.id), layer);
    });

    const group = L.featureGroup([parcelaLayer, sectorLayer]);
    try {
      const b = group.getBounds();
      if (b.isValid()) {
        map.fitBounds(b.pad(0.2));
        hideFallback();
      }
    } catch (e) {}

    document.querySelectorAll('.sector-card').forEach(card => {
      card.addEventListener('click', () => {
        const sid = card.getAttribute('data-sector-id');
        const layer = bySectorId.get(String(sid));
        if (layer) {
          try {
            map.fitBounds(layer.getBounds().pad(0.2));
          } catch (e) {}
        }
      });
    });
  }
</script>
</body>
</html>
