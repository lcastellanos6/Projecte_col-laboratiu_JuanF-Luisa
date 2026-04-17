<?php
require_once __DIR__ . '/db.php';

$conn = db_connect();

$diesCaducitat = filter_input(INPUT_GET, 'dies_caducitat', FILTER_VALIDATE_INT);
$diesCaducitat = ($diesCaducitat && $diesCaducitat > 0) ? $diesCaducitat : 30;

$diesConsum = filter_input(INPUT_GET, 'dies_consum', FILTER_VALIDATE_INT);
$diesConsum = ($diesConsum && $diesConsum > 0) ? $diesConsum : 90;

$idAplicacioMapa = filter_input(INPUT_GET, 'id_aplicacio_mapa', FILTER_VALIDATE_INT);

function fetchAllAssoc(mysqli_stmt $stmt): array
{
    $rows = [];
    $result = $stmt->get_result();
    if (!$result) {
        return $rows;
    }
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    $result->free();
    return $rows;
}

function scalar(mysqli $conn, string $sql): int
{
    $res = $conn->query($sql);
    if (!$res) {
        return 0;
    }
    $row = $res->fetch_row();
    $res->free();
    return (int) ($row[0] ?? 0);
}

$kpis = [
    'plans' => scalar($conn, "SELECT COUNT(*) FROM pla_tractament"),
    'aplicacions' => scalar($conn, "SELECT COUNT(*) FROM aplicacio"),
    'aplicacions_fila' => scalar($conn, "SELECT COUNT(*) FROM aplicacio_fila"),
    'aplicacions_pendents' => scalar($conn, "SELECT COUNT(*) FROM aplicacio_fila WHERE estat <> 'Fet'"),
    'productes' => scalar($conn, "SELECT COUNT(*) FROM producte"),
];

// 1) Alertes de tractaments pendents segons finestra planificada
$sqlPlansPendents = "
SELECT
    pt.id_pla,
    pt.nom AS pla_nom,
    pt.tipus,
    pt.finestra_data_inici,
    pt.finestra_data_fi,
    pt.plaga_malaltia_objectiu,
    v.nom_comu AS varietat,
    COALESCE(ap.num_aplicacions, 0) AS num_aplicacions,
    CASE
      WHEN CURDATE() > pt.finestra_data_fi AND COALESCE(ap.num_aplicacions, 0) = 0 THEN 'Crítica'
      WHEN CURDATE() BETWEEN pt.finestra_data_inici AND pt.finestra_data_fi AND COALESCE(ap.num_aplicacions, 0) = 0 THEN 'Alta'
      ELSE 'Control'
    END AS nivell_alerta
FROM pla_tractament pt
LEFT JOIN varietat v ON v.id_varietat = pt.id_varietat
LEFT JOIN (
    SELECT id_pla, COUNT(*) AS num_aplicacions
    FROM aplicacio
    GROUP BY id_pla
) ap ON ap.id_pla = pt.id_pla
WHERE (
    (CURDATE() BETWEEN pt.finestra_data_inici AND pt.finestra_data_fi)
    OR (CURDATE() > pt.finestra_data_fi)
)
ORDER BY
    CASE nivell_alerta WHEN 'Crítica' THEN 1 WHEN 'Alta' THEN 2 ELSE 3 END,
    pt.finestra_data_fi ASC
";
$stmtPlansPendents = $conn->prepare($sqlPlansPendents);
$stmtPlansPendents->execute();
$plansPendents = fetchAllAssoc($stmtPlansPendents);
$stmtPlansPendents->close();

// 2) Monitoratge de plagues/malalties (segons últim seguiment per sector)
$sqlMonitoratge = "
SELECT
    s.id_sector,
    s.nom AS sector_nom,
    seg.data_registre,
    seg.incidencies_detectades,
    seg.estat_fenologic,
    CASE
      WHEN seg.incidencies_detectades IS NULL OR TRIM(seg.incidencies_detectades) = '' OR LOWER(TRIM(seg.incidencies_detectades)) LIKE 'sense%' THEN 'Sense incidències'
      ELSE 'Amb incidències'
    END AS estat_sanitari
FROM sector s
LEFT JOIN (
    SELECT sg1.id_sector, sg1.data_registre, sg1.incidencies_detectades, sg1.estat_fenologic
    FROM seguiment sg1
    JOIN (
        SELECT id_sector, MAX(data_registre) AS max_data
        FROM seguiment
        GROUP BY id_sector
    ) sg2 ON sg2.id_sector = sg1.id_sector AND sg2.max_data = sg1.data_registre
) seg ON seg.id_sector = s.id_sector
ORDER BY s.nom
";
$stmtMonitoratge = $conn->prepare($sqlMonitoratge);
$stmtMonitoratge->execute();
$monitoratge = fetchAllAssoc($stmtMonitoratge);
$stmtMonitoratge->close();

// 3) Compliment normatiu bàsic (operari amb carnet + termini de seguretat)
$sqlNormatiu = "
SELECT
    a.id_aplicacio,
    a.data,
    a.metode,
    o.nom AS operari,
    o.carnet_aplicador,
    p.nom_comercial,
    p.termini_seguretat_dies,
    ap.quantitat,
    ap.unitat,
    ap.lot_referencia
FROM aplicacio a
LEFT JOIN operari o ON o.id_operari = a.id_operari
LEFT JOIN aplicacio_producte ap ON ap.id_aplicacio = a.id_aplicacio
LEFT JOIN producte p ON p.id_producte = ap.id_producte
ORDER BY a.data DESC, a.id_aplicacio DESC
LIMIT 80
";
$stmtNormatiu = $conn->prepare($sqlNormatiu);
$stmtNormatiu->execute();
$normatiu = fetchAllAssoc($stmtNormatiu);
$stmtNormatiu->close();

// 4) Alertes d'estoc/caducitat + previsió de cobertura
$sqlEstoc = "
SELECT
    p.id_producte,
    p.nom_comercial,
    p.tipus,
    p.stock_minim,
    COALESCE(st.stock_actual, 0) AS stock_actual,
    COALESCE(consum.consum_dia, 0) AS consum_dia,
    MIN(pl.data_caducitat) AS propera_caducitat
FROM producte p
LEFT JOIN (
    SELECT id_producte, SUM(quantitat_disponible) AS stock_actual
    FROM producte_lot
    GROUP BY id_producte
) st ON st.id_producte = p.id_producte
LEFT JOIN (
    SELECT
        pl.id_producte,
        ABS(SUM(me.quantitat)) / ? AS consum_dia
    FROM moviment_estoc me
    JOIN producte_lot pl ON pl.id_lot = me.id_lot
    WHERE me.motiu = 'Aplicacio'
      AND me.data >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
    GROUP BY pl.id_producte
) consum ON consum.id_producte = p.id_producte
LEFT JOIN producte_lot pl ON pl.id_producte = p.id_producte
GROUP BY p.id_producte, p.nom_comercial, p.tipus, p.stock_minim, st.stock_actual, consum.consum_dia
ORDER BY p.nom_comercial
";
$stmtEstoc = $conn->prepare($sqlEstoc);
$stmtEstoc->bind_param('ii', $diesConsum, $diesConsum);
$stmtEstoc->execute();
$estoc = fetchAllAssoc($stmtEstoc);
$stmtEstoc->close();

// 5) Dades per mapa de files tractades (estat per fila dins d'una aplicació)
if (!$idAplicacioMapa) {
    $resUltimaAplicacio = $conn->query("SELECT id_aplicacio FROM aplicacio ORDER BY id_aplicacio DESC LIMIT 1");
    if ($resUltimaAplicacio && $rowUltima = $resUltimaAplicacio->fetch_assoc()) {
        $idAplicacioMapa = (int) $rowUltima['id_aplicacio'];
    } else {
        $idAplicacioMapa = 0;
    }
    if ($resUltimaAplicacio) {
        $resUltimaAplicacio->free();
    }
}

$aplicacionsSelector = [];
$resAplicacions = $conn->query("SELECT id_aplicacio, data, metode FROM aplicacio ORDER BY id_aplicacio DESC LIMIT 100");
if ($resAplicacions) {
    while ($r = $resAplicacions->fetch_assoc()) {
        $aplicacionsSelector[] = $r;
    }
    $resAplicacions->free();
}

$sqlMapaFiles = "
SELECT
    f.id_fila,
    f.numero_fila,
    f.id_increment AS id_plantacio,
    ST_AsGeoJSON(f.geometria_fila) AS fila_geojson,
    af.estat,
    af.volum_caldo_l,
    af.data_execucio,
    af.id_operari_execucio
FROM fila f
LEFT JOIN aplicacio_fila af
    ON af.id_fila = f.id_fila
   AND af.id_aplicacio = ?
ORDER BY f.id_fila
";
$stmtMapaFiles = $conn->prepare($sqlMapaFiles);
$stmtMapaFiles->bind_param('i', $idAplicacioMapa);
$stmtMapaFiles->execute();
$rowsMapa = fetchAllAssoc($stmtMapaFiles);
$stmtMapaFiles->close();

$featuresFiles = [];
foreach ($rowsMapa as $r) {
    if (empty($r['fila_geojson'])) {
        continue;
    }
    $featuresFiles[] = [
        'type' => 'Feature',
        'geometry' => json_decode($r['fila_geojson'], true),
        'properties' => [
            'id_fila' => (int) $r['id_fila'],
            'numero_fila' => (int) $r['numero_fila'],
            'id_plantacio' => (int) $r['id_plantacio'],
            'estat' => $r['estat'] ?? 'No assignada',
            'volum_caldo_l' => $r['volum_caldo_l'],
            'data_execucio' => $r['data_execucio'],
            'id_operari_execucio' => $r['id_operari_execucio'],
        ],
    ];
}
$geojsonFiles = json_encode(['type' => 'FeatureCollection', 'features' => $featuresFiles], JSON_UNESCAPED_UNICODE);

// 6) Anàlisi nutricional (sòl + rendiment + clima)
$sqlNutricio = "
SELECT
    p.id_parcela,
    p.nom AS parcela_nom,
    COALESCE(AVG(so.ph), 0) AS ph_mitja,
    COALESCE(AVG(so.materia_organica), 0) AS mo_mitjana,
    COALESCE(AVG(r.rendiment), 0) AS rendiment_mitja,
    COALESCE(AVG(c.temperatura_mitjana), 0) AS temperatura_mitjana,
    COALESCE(AVG(c.precipitacio_total), 0) AS precipitacio_mitjana
FROM parcela p
LEFT JOIN parcela_sol ps ON ps.id_parcela = p.id_parcela
LEFT JOIN sol so ON so.id_sol = ps.id_sol
LEFT JOIN registre r ON r.id_parcela = p.id_parcela
LEFT JOIN sector_parcela sp ON sp.id_parcela = p.id_parcela
LEFT JOIN plantacio pl ON pl.id_sector = sp.id_sector
LEFT JOIN clima c ON c.id_plantacio = pl.id_plantacio
GROUP BY p.id_parcela, p.nom
ORDER BY p.nom
";
$stmtNutricio = $conn->prepare($sqlNutricio);
$stmtNutricio->execute();
$nutricio = fetchAllAssoc($stmtNutricio);
$stmtNutricio->close();

// 7) Traçabilitat lot -> collita -> aplicació (via tasca)
$sqlTracabilitat = "
SELECT
    lp.id_lot_produccio,
    lp.codi_lot,
    lp.data_produccio,
    lp.quantitat,
    lp.unitat,
    c.collita_id,
    c.data_inici AS data_collita,
    t.id_tasca,
    t.nom_tasca,
    t.id_aplicacio,
    a.data AS data_aplicacio,
    p.nom_comercial,
    ap.quantitat AS quantitat_producte,
    ap.unitat AS unitat_producte,
    ap.lot_referencia
FROM lot_produccio lp
JOIN collita c ON c.collita_id = lp.collita_id
LEFT JOIN tasca t ON t.collita_id = c.collita_id AND t.id_aplicacio IS NOT NULL
LEFT JOIN aplicacio a ON a.id_aplicacio = t.id_aplicacio
LEFT JOIN aplicacio_producte ap ON ap.id_aplicacio = a.id_aplicacio
LEFT JOIN producte p ON p.id_producte = ap.id_producte
ORDER BY lp.id_lot_produccio DESC, a.data DESC
LIMIT 100
";
$stmtTracabilitat = $conn->prepare($sqlTracabilitat);
$stmtTracabilitat->execute();
$tracabilitat = fetchAllAssoc($stmtTracabilitat);
$stmtTracabilitat->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Mòdul de tractaments i fertilització</title>
    <link rel="stylesheet" href="../HTML/styles.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <style>
      .kpi-grid { display:grid; grid-template-columns: repeat(auto-fit, minmax(180px,1fr)); gap:0.7rem; }
      .kpi-card { border:1px solid #d7e7d7; border-radius:0.7rem; padding:0.75rem; background:#f7fbf7; }
      .kpi-card strong { display:block; font-size:1.3rem; }
      .chip { border-radius:999px; padding:0.2rem 0.55rem; font-size:0.75rem; font-weight:700; display:inline-block; }
      .chip-ok { background:#def7e5; color:#1a5c2e; }
      .chip-warn { background:#fff2cc; color:#7a5a00; }
      .chip-bad { background:#fde2e2; color:#8f1f1f; }
      #map-files { height: 560px; border:1px solid #d1d5db; border-radius:0.8rem; }
      .calc-grid { display:grid; grid-template-columns: repeat(auto-fit, minmax(190px,1fr)); gap:0.75rem; }
      .calc-result { border:1px solid #d7e7d7; border-radius:0.6rem; padding:0.6rem; background:#f7fbf7; }
      .small-note { font-size:0.85rem; color:#4b5563; }
    </style>
</head>
<body>
<div class="page">
  <div class="page-header">
    <h1>Mòdul de gestió de tractaments i fertilització</h1>
    <p class="page-subtitle">Planificació, execució, control d'estocs, càlcul de dosis, alertes i traçabilitat normativa.</p>
  </div>

  <div class="panel">
    <h2 class="panel-title">Resum operatiu</h2>
    <div class="kpi-grid">
      <div class="kpi-card"><span>Plans fitosanitaris</span><strong><?php echo $kpis['plans']; ?></strong></div>
      <div class="kpi-card"><span>Aplicacions registrades</span><strong><?php echo $kpis['aplicacions']; ?></strong></div>
      <div class="kpi-card"><span>Execucions per fila</span><strong><?php echo $kpis['aplicacions_fila']; ?></strong></div>
      <div class="kpi-card"><span>Files pendents/parcials</span><strong><?php echo $kpis['aplicacions_pendents']; ?></strong></div>
      <div class="kpi-card"><span>Productes catalogats</span><strong><?php echo $kpis['productes']; ?></strong></div>
    </div>
  </div>

  <div class="panel mt-2">
    <h2 class="panel-title">Alertes de tractaments pendents</h2>
    <div class="table-scroll">
      <table class="table">
        <thead><tr><th>ID Pla</th><th>Nom pla</th><th>Tipus</th><th>Varietat</th><th>Finestra</th><th>Objectiu</th><th>Aplicacions</th><th>Alerta</th></tr></thead>
        <tbody>
        <?php if (empty($plansPendents)): ?>
          <tr><td colspan="8">No hi ha plans en finestra activa o vençuda.</td></tr>
        <?php else: foreach ($plansPendents as $r): ?>
          <?php
            $cls = 'chip-ok';
            if (($r['nivell_alerta'] ?? '') === 'Alta') $cls = 'chip-warn';
            if (($r['nivell_alerta'] ?? '') === 'Crítica') $cls = 'chip-bad';
          ?>
          <tr>
            <td><?php echo (int) $r['id_pla']; ?></td>
            <td><?php echo htmlspecialchars($r['pla_nom'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($r['tipus'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($r['varietat'] ?? '-'); ?></td>
            <td><?php echo htmlspecialchars(($r['finestra_data_inici'] ?? '-') . ' -> ' . ($r['finestra_data_fi'] ?? '-')); ?></td>
            <td><?php echo htmlspecialchars($r['plaga_malaltia_objectiu'] ?? '-'); ?></td>
            <td><?php echo (int) ($r['num_aplicacions'] ?? 0); ?></td>
            <td><span class="chip <?php echo $cls; ?>"><?php echo htmlspecialchars($r['nivell_alerta'] ?? 'Control'); ?></span></td>
          </tr>
        <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="panel mt-2">
    <h2 class="panel-title">Monitoratge de plagues i malalties (últim seguiment)</h2>
    <div class="table-scroll">
      <table class="table">
        <thead><tr><th>Sector</th><th>Data registre</th><th>Estat fenològic</th><th>Incidències</th><th>Estat sanitari</th></tr></thead>
        <tbody>
        <?php if (empty($monitoratge)): ?>
          <tr><td colspan="5">No hi ha seguiments registrats.</td></tr>
        <?php else: foreach ($monitoratge as $r): ?>
          <tr>
            <td><?php echo htmlspecialchars($r['sector_nom'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($r['data_registre'] ?? '-'); ?></td>
            <td><?php echo htmlspecialchars($r['estat_fenologic'] ?? '-'); ?></td>
            <td><?php echo htmlspecialchars($r['incidencies_detectades'] ?? '-'); ?></td>
            <td>
              <?php if (($r['estat_sanitari'] ?? '') === 'Amb incidències'): ?>
                <span class="chip chip-bad">Amb incidències</span>
              <?php else: ?>
                <span class="chip chip-ok">Sense incidències</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="panel mt-2">
    <h2 class="panel-title">Càlcul de dosis (simulador web)</h2>
    <p class="small-note">Calcula dosis segons superfície, volum de caldo i capacitat de maquinària.</p>
    <div class="calc-grid" id="doseCalculator">
      <div>
        <label>Superfície a tractar (ha)</label>
        <input type="number" step="0.01" id="supHa" value="1.50">
      </div>
      <div>
        <label>Dosi recomanada (L o kg/ha)</label>
        <input type="number" step="0.01" id="dosiHa" value="2.00">
      </div>
      <div>
        <label>Volum de caldo (L/ha)</label>
        <input type="number" step="1" id="volHa" value="1000">
      </div>
      <div>
        <label>Capacitat màquina (L)</label>
        <input type="number" step="1" id="capMaquina" value="2000">
      </div>
      <div>
        <label>Concentració comercial (%)</label>
        <input type="number" step="0.01" id="concComercial" value="20">
      </div>
      <div style="display:flex;align-items:flex-end;">
        <button type="button" class="btn btn-primary" onclick="calculaDosi()">Calcular</button>
      </div>
    </div>
    <div class="calc-grid mt-2">
      <div class="calc-result"><span>Producte total</span><strong id="resProdTotal">-</strong></div>
      <div class="calc-result"><span>Volum caldo total</span><strong id="resCaldoTotal">-</strong></div>
      <div class="calc-result"><span>Càrregues de màquina</span><strong id="resCarregues">-</strong></div>
      <div class="calc-result"><span>Producte per càrrega</span><strong id="resProdCarrega">-</strong></div>
    </div>
  </div>

  <div class="panel mt-2">
    <h2 class="panel-title">Control d'estocs, caducitats i previsió de cobertura</h2>
    <form method="get" class="form-grid-2">
      <label>Dies per alerta de caducitat</label>
      <input type="number" name="dies_caducitat" value="<?php echo (int) $diesCaducitat; ?>">
      <label>Finestra de consum (dies)</label>
      <input type="number" name="dies_consum" value="<?php echo (int) $diesConsum; ?>">
      <input type="hidden" name="id_aplicacio_mapa" value="<?php echo (int) $idAplicacioMapa; ?>">
      <button type="submit" class="btn btn-primary mt-2">Recalcular</button>
    </form>
    <div class="table-scroll mt-2">
      <table class="table">
        <thead><tr><th>Producte</th><th>Tipus</th><th>Stock actual</th><th>Stock mínim</th><th>Consum/dia</th><th>Cobertura (dies)</th><th>Pròx. caducitat</th><th>Alertes</th></tr></thead>
        <tbody>
        <?php if (empty($estoc)): ?>
          <tr><td colspan="8">No hi ha dades d'estoc.</td></tr>
        <?php else: foreach ($estoc as $r): ?>
          <?php
            $stock = (float) ($r['stock_actual'] ?? 0);
            $min = (float) ($r['stock_minim'] ?? 0);
            $consumDia = (float) ($r['consum_dia'] ?? 0);
            $cobertura = $consumDia > 0 ? ($stock / $consumDia) : null;
            $cad = $r['propera_caducitat'] ?? null;
            $alertes = [];
            if ($stock <= $min) $alertes[] = 'Stock baix';
            if (!empty($cad)) {
                $diesCad = (int) floor((strtotime($cad) - strtotime(date('Y-m-d'))) / 86400);
                if ($diesCad <= $diesCaducitat) $alertes[] = 'Caducitat propera';
            }
            if ($cobertura !== null && $cobertura < 14) $alertes[] = 'Cobertura curta';
          ?>
          <tr>
            <td><?php echo htmlspecialchars($r['nom_comercial'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($r['tipus'] ?? ''); ?></td>
            <td><?php echo number_format($stock, 3, ',', '.'); ?></td>
            <td><?php echo number_format($min, 3, ',', '.'); ?></td>
            <td><?php echo number_format($consumDia, 3, ',', '.'); ?></td>
            <td><?php echo $cobertura === null ? '-' : number_format($cobertura, 1, ',', '.'); ?></td>
            <td><?php echo htmlspecialchars($cad ?? '-'); ?></td>
            <td>
              <?php if (empty($alertes)): ?>
                <span class="chip chip-ok">Sense alertes</span>
              <?php else: ?>
                <span class="chip chip-warn"><?php echo htmlspecialchars(implode(' · ', $alertes)); ?></span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="panel mt-2">
    <h2 class="panel-title">Traçabilitat a nivell de fila i continuïtat del tractament</h2>
    <form method="get" class="form-grid-2">
      <label>Aplicació per visualitzar al mapa</label>
      <select name="id_aplicacio_mapa">
        <?php foreach ($aplicacionsSelector as $opt): ?>
          <option value="<?php echo (int) $opt['id_aplicacio']; ?>" <?php echo ((int) $opt['id_aplicacio'] === (int) $idAplicacioMapa) ? 'selected' : ''; ?>>
            #<?php echo (int) $opt['id_aplicacio']; ?> · <?php echo htmlspecialchars(($opt['data'] ?? '') . ' · ' . ($opt['metode'] ?? '')); ?>
          </option>
        <?php endforeach; ?>
      </select>
      <input type="hidden" name="dies_caducitat" value="<?php echo (int) $diesCaducitat; ?>">
      <input type="hidden" name="dies_consum" value="<?php echo (int) $diesConsum; ?>">
      <button type="submit" class="btn btn-primary mt-2">Carregar mapa de files</button>
    </form>
    <div id="map-files" class="mt-2"></div>
    <p class="small-note mt-1">Verd = fet, groc = parcial, vermell = pendent/no registrada en aquesta aplicació.</p>
  </div>

  <div class="panel mt-2">
    <h2 class="panel-title">Anàlisi i seguiment nutricional</h2>
    <div class="table-scroll">
      <table class="table">
        <thead><tr><th>Parcel·la</th><th>pH mitjà sòl</th><th>Matèria orgànica (%)</th><th>Rendiment mitjà</th><th>Temp. mitjana</th><th>Precipitació mitjana</th></tr></thead>
        <tbody>
        <?php if (empty($nutricio)): ?>
          <tr><td colspan="6">No hi ha dades nutricionals per mostrar.</td></tr>
        <?php else: foreach ($nutricio as $r): ?>
          <tr>
            <td><?php echo htmlspecialchars($r['parcela_nom'] ?? ''); ?></td>
            <td><?php echo number_format((float) ($r['ph_mitja'] ?? 0), 2, ',', '.'); ?></td>
            <td><?php echo number_format((float) ($r['mo_mitjana'] ?? 0), 2, ',', '.'); ?></td>
            <td><?php echo number_format((float) ($r['rendiment_mitja'] ?? 0), 2, ',', '.'); ?></td>
            <td><?php echo number_format((float) ($r['temperatura_mitjana'] ?? 0), 2, ',', '.'); ?></td>
            <td><?php echo number_format((float) ($r['precipitacio_mitjana'] ?? 0), 2, ',', '.'); ?></td>
          </tr>
        <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="panel mt-2">
    <h2 class="panel-title">Registre normatiu i traçabilitat lot -> aplicació</h2>
    <div class="table-scroll">
      <table class="table">
        <thead><tr><th>ID aplicació</th><th>Data</th><th>Operari</th><th>Carnet</th><th>Producte</th><th>Dosi</th><th>Termini seguretat (dies)</th><th>Lot producte</th></tr></thead>
        <tbody>
        <?php if (empty($normatiu)): ?>
          <tr><td colspan="8">No hi ha registres normatius.</td></tr>
        <?php else: foreach ($normatiu as $r): ?>
          <tr>
            <td><?php echo (int) ($r['id_aplicacio'] ?? 0); ?></td>
            <td><?php echo htmlspecialchars($r['data'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($r['operari'] ?? '-'); ?></td>
            <td>
              <?php if (!empty($r['carnet_aplicador'])): ?>
                <span class="chip chip-ok"><?php echo htmlspecialchars($r['carnet_aplicador']); ?></span>
              <?php else: ?>
                <span class="chip chip-bad">Sense carnet</span>
              <?php endif; ?>
            </td>
            <td><?php echo htmlspecialchars($r['nom_comercial'] ?? '-'); ?></td>
            <td><?php echo htmlspecialchars(($r['quantitat'] ?? '-') . ' ' . ($r['unitat'] ?? '')); ?></td>
            <td><?php echo htmlspecialchars($r['termini_seguretat_dies'] ?? '-'); ?></td>
            <td><?php echo htmlspecialchars($r['lot_referencia'] ?? '-'); ?></td>
          </tr>
        <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>

    <h3 class="panel-title mt-2">Traçabilitat de lots de producció</h3>
    <div class="table-scroll">
      <table class="table">
        <thead><tr><th>Lot producció</th><th>Data collita</th><th>Tasca</th><th>Aplicació</th><th>Producte aplicat</th><th>Dosi producte</th><th>Lot producte</th></tr></thead>
        <tbody>
        <?php if (empty($tracabilitat)): ?>
          <tr><td colspan="7">No hi ha relacions de traçabilitat disponibles.</td></tr>
        <?php else: foreach ($tracabilitat as $r): ?>
          <tr>
            <td><?php echo htmlspecialchars(($r['codi_lot'] ?? '-') . ' (' . ($r['quantitat'] ?? '-') . ' ' . ($r['unitat'] ?? '') . ')'); ?></td>
            <td><?php echo htmlspecialchars($r['data_collita'] ?? '-'); ?></td>
            <td><?php echo htmlspecialchars($r['nom_tasca'] ?? '-'); ?></td>
            <td><?php echo htmlspecialchars($r['id_aplicacio'] ?? '-'); ?></td>
            <td><?php echo htmlspecialchars($r['nom_comercial'] ?? '-'); ?></td>
            <td><?php echo htmlspecialchars(($r['quantitat_producte'] ?? '-') . ' ' . ($r['unitat_producte'] ?? '')); ?></td>
            <td><?php echo htmlspecialchars($r['lot_referencia'] ?? '-'); ?></td>
          </tr>
        <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
function calculaDosi() {
  const sup = parseFloat(document.getElementById('supHa').value || '0');
  const dosi = parseFloat(document.getElementById('dosiHa').value || '0');
  const vol = parseFloat(document.getElementById('volHa').value || '0');
  const cap = parseFloat(document.getElementById('capMaquina').value || '0');
  const conc = parseFloat(document.getElementById('concComercial').value || '0');

  const producteTotal = sup * dosi;
  const caldoTotal = sup * vol;
  const carregues = (cap > 0) ? Math.ceil(caldoTotal / cap) : 0;
  const productePerCarrega = (carregues > 0) ? (producteTotal / carregues) : 0;

  document.getElementById('resProdTotal').textContent = producteTotal.toFixed(2) + ' unitats';
  document.getElementById('resCaldoTotal').textContent = caldoTotal.toFixed(2) + ' L';
  document.getElementById('resCarregues').textContent = String(carregues);
  document.getElementById('resProdCarrega').textContent = productePerCarrega.toFixed(2) + ' unitats/càrrega';
}
calculaDosi();

const geojsonFiles = <?php echo $geojsonFiles ?: '{"type":"FeatureCollection","features":[]}'; ?>;
const map = L.map('map-files');
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  maxZoom: 19,
  attribution: '&copy; OpenStreetMap'
}).addTo(map);

function colorByEstat(estat) {
  if (estat === 'Fet') return '#2f7d2f';
  if (estat === 'Parcial') return '#f59e0b';
  return '#dc2626';
}

const filesLayer = L.geoJSON(geojsonFiles, {
  style: (feature) => ({
    color: colorByEstat((feature.properties || {}).estat),
    weight: 4
  }),
  onEachFeature: (feature, layer) => {
    const p = feature.properties || {};
    const popup = `
      <strong>Fila #${p.numero_fila || '-'}</strong><br>
      ID fila: ${p.id_fila || '-'}<br>
      Plantació: ${p.id_plantacio || '-'}<br>
      Estat: ${p.estat || 'No assignada'}<br>
      Volum caldo: ${p.volum_caldo_l ?? '-'} L<br>
      Data execució: ${p.data_execucio || '-'}<br>
      Operari execució: ${p.id_operari_execucio || '-'}
    `;
    layer.bindPopup(popup);
  }
}).addTo(map);

try {
  const bounds = filesLayer.getBounds();
  if (bounds.isValid()) {
    map.fitBounds(bounds.pad(0.2));
  } else {
    map.setView([41.61, 0.87], 12);
  }
} catch (error) {
  map.setView([41.61, 0.87], 12);
}
</script>
</body>
</html>
