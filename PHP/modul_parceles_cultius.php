<?php
require_once __DIR__ . '/db.php';

$conn = db_connect();

function scalar(mysqli $conn, string $sql): int
{
    $result = $conn->query($sql);
    if (!$result) {
        return 0;
    }
    $row = $result->fetch_row();
    $result->free();
    return (int) ($row[0] ?? 0);
}

$metrics = [
    'parceles' => scalar($conn, "SELECT COUNT(*) FROM parcela"),
    'sectors' => scalar($conn, "SELECT COUNT(*) FROM sector"),
    'plantacions' => scalar($conn, "SELECT COUNT(*) FROM plantacio"),
    'files' => scalar($conn, "SELECT COUNT(*) FROM fila"),
    'registres' => scalar($conn, "SELECT COUNT(*) FROM registre"),
    'seguiments' => scalar($conn, "SELECT COUNT(*) FROM seguiment"),
    'previsions' => scalar($conn, "SELECT COUNT(*) FROM previsio_collita"),
];

$conn->close();

$items = [
    [
        'bloc' => 'Cadastre digital i geoespacial',
        'estat' => 'Validat amb SQL i codi',
        'tipus_estat' => 'ok',
        'detall' => 'Taules amb geometria i visualitzacio en mapa de parceles i sectors.',
        'enllac' => 'consulta_parcela_sector.php',
    ],
    [
        'bloc' => 'Assignacio de cultius i varietats',
        'estat' => 'Validat amb SQL i codi',
        'tipus_estat' => 'ok',
        'detall' => 'Hi ha cataleg d\'especies/varietats i plantacions per sector.',
        'enllac' => 'consulta_cultius_varietats.php',
    ],
    [
        'bloc' => 'Seguiment fenologic i incidencies',
        'estat' => 'Validat amb SQL i codi',
        'tipus_estat' => 'ok',
        'detall' => 'Registre de seguiment amb estat fenologic, incidencies i intervencions.',
        'enllac' => '../HTML/seguiment.php',
    ],
    [
        'bloc' => 'Historial de cultius per parcella',
        'estat' => 'Validat amb SQL i codi',
        'tipus_estat' => 'ok',
        'detall' => 'Registre historic disponible per varietat, plantacio i rendiment.',
        'enllac' => '../HTML/registre.php',
    ],
    [
        'bloc' => 'Calculs de superficies i previsio',
        'estat' => 'Implementat',
        'tipus_estat' => 'ok',
        'detall' => 'Inclou model combinat de previsio, seguiment i historic amb hores estimades de collita.',
        'enllac' => 'planificacio_explotacio.php',
    ],
    [
        'bloc' => 'Rotacions i planificacio estrategica',
        'estat' => 'Implementat',
        'tipus_estat' => 'ok',
        'detall' => 'Proposta i guardat de plans de rotacio com a tasques planificades.',
        'enllac' => 'planificacio_explotacio.php',
    ],
    [
        'bloc' => 'Capes tematiques SIG (meteo, restriccions, sensors)',
        'estat' => 'Implementat (WMS extern)',
        'tipus_estat' => 'ok',
        'detall' => 'Mapa temàtic intern + capes externes WMS (Catastro i Red Natura 2000). Meteo/sensors externs encara no integrats.',
        'enllac' => 'mapa_tematic_parceles.php',
    ],
];
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Estat modul parceles i cultius</title>
    <link rel="stylesheet" href="../HTML/styles.css">
    <style>
      .status-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
        gap: 0.8rem;
      }
      .kpi {
        border: 1px solid #d7e7d7;
        border-radius: 0.75rem;
        background: #f7fbf7;
        padding: 0.85rem 1rem;
      }
      .kpi strong {
        display: block;
        font-size: 1.35rem;
      }
      .badge-status {
        display: inline-block;
        border-radius: 999px;
        padding: 0.2rem 0.55rem;
        font-size: 0.75rem;
        font-weight: 700;
      }
      .ok { background: #def7e5; color: #1a5c2e; }
      .warn { background: #fff2cc; color: #7a5a00; }
      .ko { background: #fde2e2; color: #8f1f1f; }
      .module-table {
        width: 100%;
        border-collapse: collapse;
      }
      .module-table th, .module-table td {
        border-bottom: 1px solid #d7e7d7;
        padding: 0.6rem;
        text-align: left;
        vertical-align: top;
      }
      .module-table th {
        background: #eef8ee;
      }
    </style>
</head>
<body>
<div class="page">
  <div class="page-header">
    <h1>Estat del modul: Gestio de parceles i cultius</h1>
    <p class="page-subtitle">Panell de control per veure que esta implementat i que falta.</p>
  </div>

  <div class="panel">
    <h2 class="panel-title">Resum de dades disponibles</h2>
    <div class="status-grid">
      <div class="kpi"><span>Parceles</span><strong><?php echo $metrics['parceles']; ?></strong></div>
      <div class="kpi"><span>Sectors</span><strong><?php echo $metrics['sectors']; ?></strong></div>
      <div class="kpi"><span>Plantacions</span><strong><?php echo $metrics['plantacions']; ?></strong></div>
      <div class="kpi"><span>Files d'arbres</span><strong><?php echo $metrics['files']; ?></strong></div>
      <div class="kpi"><span>Registres historics</span><strong><?php echo $metrics['registres']; ?></strong></div>
      <div class="kpi"><span>Seguiments</span><strong><?php echo $metrics['seguiments']; ?></strong></div>
      <div class="kpi"><span>Previsions</span><strong><?php echo $metrics['previsions']; ?></strong></div>
    </div>
  </div>

  <div class="panel mt-2">
    <h2 class="panel-title">Analisi del que falta</h2>
    <div class="table-scroll">
    <table class="module-table">
      <thead>
      <tr>
        <th>Bloc funcional</th>
        <th>Estat</th>
        <th>Detall</th>
        <th>Acces</th>
      </tr>
      </thead>
      <tbody>
      <?php foreach ($items as $item): ?>
        <?php
          $class = $item['tipus_estat'] ?? 'ko';
        ?>
        <tr>
          <td><?php echo htmlspecialchars($item['bloc']); ?></td>
          <td><span class="badge-status <?php echo $class; ?>"><?php echo htmlspecialchars($item['estat']); ?></span></td>
          <td><?php echo htmlspecialchars($item['detall']); ?></td>
          <td>
            <?php if ($item['enllac'] !== ''): ?>
              <a class="btn btn-primary" href="<?php echo htmlspecialchars($item['enllac']); ?>">Obrir</a>
            <?php else: ?>
              <span>-</span>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    </div>
  </div>
</div>
</body>
</html>
