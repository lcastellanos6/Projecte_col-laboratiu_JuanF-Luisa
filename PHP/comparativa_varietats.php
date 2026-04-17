<?php
require_once __DIR__ . '/db.php';
$conn = db_connect();

$yearFilter = filter_input(INPUT_GET, 'any', FILTER_VALIDATE_INT);
$yearFilter = $yearFilter ?: null;

// Factors editables per normalitzar unitats de collita a kg equivalents.
$caixaKg = filter_input(INPUT_GET, 'caixa_kg', FILTER_VALIDATE_FLOAT);
$binKg = filter_input(INPUT_GET, 'bin_kg', FILTER_VALIDATE_FLOAT);
$caixaKg = ($caixaKg && $caixaKg > 0) ? $caixaKg : 18.0;
$binKg = ($binKg && $binKg > 0) ? $binKg : 300.0;

$sql = "
SELECT
    p.id_parcela,
    p.nom AS parcela_nom,
    s.nom AS sector_nom,
    v.nom_comu AS varietat_nom,
    COALESCE(pl.num_arbres_total, 0) AS num_arbres_total,
    COALESCE(
        SUM(
            CASE
                WHEN c.unitat = 'kg' THEN c.quantitat_total
                WHEN c.unitat = 'caixa' THEN c.quantitat_total * ?
                WHEN c.unitat = 'bin' THEN c.quantitat_total * ?
                ELSE 0
            END
        ),
        0
    ) AS total_collita_kg
FROM plantacio pl
JOIN sector s ON s.id_sector = pl.id_sector
JOIN sector_parcela sp ON sp.id_sector = s.id_sector
JOIN parcela p ON p.id_parcela = sp.id_parcela
JOIN varietat v ON v.id_varietat = pl.id_varietat
LEFT JOIN collita c
    ON c.plantacio_id = pl.id_plantacio
   AND (? IS NULL OR YEAR(c.data_inici) = ?)
GROUP BY
    p.id_parcela, p.nom, s.nom, v.nom_comu, pl.num_arbres_total
ORDER BY p.nom, v.nom_comu
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Error SQL: " . $conn->error);
}

$stmt->bind_param('ddii', $caixaKg, $binKg, $yearFilter, $yearFilter);
$stmt->execute();
$result = $stmt->get_result();

$parceles = [];
$varietatsPerParcela = [];
$varietatNames = [];

while ($row = $result->fetch_assoc()) {
    $parcelaId = (int) ($row['id_parcela'] ?? 0);
    $varietat = (string) ($row['varietat_nom'] ?? '');
    $valorKg = (float) ($row['total_collita_kg'] ?? 0);

    if (!isset($parceles[$parcelaId])) {
        $parceles[$parcelaId] = [
            'nom' => (string) ($row['parcela_nom'] ?? "Parcel·la {$parcelaId}"),
            'sector' => (string) ($row['sector_nom'] ?? '-'),
            'arbres' => (int) ($row['num_arbres_total'] ?? 0),
        ];
    }

    if ($varietat !== '') {
        if (!isset($varietatsPerParcela[$parcelaId][$varietat])) {
            $varietatsPerParcela[$parcelaId][$varietat] = 0.0;
        }
        $varietatsPerParcela[$parcelaId][$varietat] += $valorKg;
        if (!in_array($varietat, $varietatNames, true)) {
            $varietatNames[] = $varietat;
        }
    }
}

$stmt->close();
$conn->close();

sort($varietatNames, SORT_NATURAL | SORT_FLAG_CASE);

$labels = [];
foreach ($parceles as $idParcela => $meta) {
    $labels[] = $meta['nom'] . " (#{$idParcela})";
}

$colors = ['#2f7d2f', '#55a455', '#7fc97f', '#a8d5a2', '#cce5cc', '#1f5f8b', '#f59e0b', '#dc2626'];
$datasets = [];
foreach ($varietatNames as $index => $varietat) {
    $serie = [];
    foreach (array_keys($parceles) as $idParcela) {
        $serie[] = (float) ($varietatsPerParcela[$idParcela][$varietat] ?? 0);
    }
    $datasets[] = [
        'label' => $varietat,
        'data' => $serie,
        'backgroundColor' => $colors[$index % count($colors)],
    ];
}
?>

<!DOCTYPE html>
<html lang="ca">
<head>
<meta charset="UTF-8">
<title>Comparativa parcel·les i varietats</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="../HTML/styles.css">
</head>
<body>
<div class="page">
  <div class="page-header">
    <h1>Comparativa parcel·les i varietats</h1>
    <p class="page-subtitle">Producció normalitzada en kg equivalents per comparar resultats entre parcel·les i varietats.</p>
  </div>

  <div class="panel">
    <form method="get" class="form-grid-3">
      <label>Filtrar per any (opcional)</label>
      <input type="number" name="any" min="2000" max="2100" value="<?= htmlspecialchars((string) ($yearFilter ?? '')) ?>" placeholder="2026">

      <label>Conversió 1 caixa = kg</label>
      <input type="number" step="0.1" name="caixa_kg" value="<?= htmlspecialchars((string) $caixaKg) ?>">

      <label>Conversió 1 bin = kg</label>
      <input type="number" step="0.1" name="bin_kg" value="<?= htmlspecialchars((string) $binKg) ?>">

      <button type="submit" class="btn btn-primary mt-2">Aplicar filtres</button>
    </form>
  </div>

  <div class="panel mt-2">
    <h2 class="panel-title">Producció per parcel·la i varietat</h2>
    <canvas id="graficaVarietats"></canvas>
  </div>

  <div class="panel mt-2">
    <h2 class="panel-title">Taula resum (kg equivalents)</h2>
    <div class="table-scroll">
      <table class="table">
        <thead>
        <tr>
          <th>Parcel·la</th>
          <th>Sector</th>
          <th>Arbres</th>
          <?php foreach ($varietatNames as $v): ?>
            <th><?= htmlspecialchars($v) ?></th>
          <?php endforeach; ?>
          <th>Total</th>
        </tr>
        </thead>
        <tbody>
        <?php if (empty($parceles)): ?>
          <tr><td colspan="<?= 4 + count($varietatNames) ?>">No hi ha dades per als filtres actuals.</td></tr>
        <?php else: ?>
          <?php foreach ($parceles as $idParcela => $meta): ?>
            <?php $totalFila = 0.0; ?>
            <tr>
              <td><?= htmlspecialchars($meta['nom']) ?> (#<?= (int) $idParcela ?>)</td>
              <td><?= htmlspecialchars($meta['sector']) ?></td>
              <td><?= (int) $meta['arbres'] ?></td>
              <?php foreach ($varietatNames as $v): ?>
                <?php
                  $valor = (float) ($varietatsPerParcela[$idParcela][$v] ?? 0);
                  $totalFila += $valor;
                ?>
                <td><?= number_format($valor, 2, ',', '.') ?></td>
              <?php endforeach; ?>
              <td><strong><?= number_format($totalFila, 2, ',', '.') ?></strong></td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
const ctx = document.getElementById('graficaVarietats').getContext('2d');
const data = {
  labels: <?= json_encode($labels, JSON_UNESCAPED_UNICODE) ?>,
  datasets: <?= json_encode($datasets, JSON_UNESCAPED_UNICODE) ?>
};

new Chart(ctx, {
  type: 'bar',
  data,
  options: {
    responsive: true,
    plugins: {
      legend: { position: 'top' }
    },
    scales: {
      y: {
        beginAtZero: true,
        title: { display: true, text: 'kg equivalents' }
      }
    }
  }
});
</script>
</body>
</html>
