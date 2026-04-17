<?php
require_once __DIR__ . '/db.php';

$conn = db_connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accio'] ?? '') === 'crear_pla_rotacio') {
    $idSector = filter_input(INPUT_POST, 'id_sector', FILTER_VALIDATE_INT);
    $tipusPla = trim((string) ($_POST['tipus_pla'] ?? ''));
    $dataInici = trim((string) ($_POST['data_inici'] ?? ''));
    $dataFinal = trim((string) ($_POST['data_final'] ?? ''));
    $detallPla = trim((string) ($_POST['detall_pla'] ?? ''));

    if ($idSector && in_array($tipusPla, ['Renovar', 'Reconvertir', 'Mantenir'], true)) {
        $nomTasca = "Pla de rotació: {$tipusPla}";
        $tipusTasca = 'Planificació rotació';
        $durada = 'Planificació de campanya';
        $equip = 'Equip tècnic i gestió agronòmica';
        $instruccions = $detallPla !== '' ? $detallPla : 'Pla generat des del mòdul de planificació.';
        $dependencies = 'Incloure revisió varietal, guaret i calendari d\'implantació.';
        $estat = 'Planificada';
        $personalRequerit = 1;

        $stmtGuardarPla = $conn->prepare("
            INSERT INTO tasca (
                nom_tasca, tipus_tasca, id_sector, data_inici, data_final, durada_estimada,
                personal_requerit, equipament_necessari, instruccions, dependencies, estat
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        if ($stmtGuardarPla) {
            $stmtGuardarPla->bind_param(
                'ssisssissss',
                $nomTasca,
                $tipusTasca,
                $idSector,
                $dataInici,
                $dataFinal,
                $durada,
                $personalRequerit,
                $equip,
                $instruccions,
                $dependencies,
                $estat
            );
            $stmtGuardarPla->execute();
            $stmtGuardarPla->close();
        }
    }

    $campanyaRedir = filter_input(INPUT_POST, 'campanya', FILTER_VALIDATE_INT);
    $kgHoraRedir = filter_input(INPUT_POST, 'kg_hora_operari', FILTER_VALIDATE_FLOAT);
    $campanyaRedir = $campanyaRedir ?: (int) date('Y');
    $kgHoraRedir = $kgHoraRedir ?: 120;
    header('Location: planificacio_explotacio.php?campanya=' . urlencode((string) $campanyaRedir) . '&kg_hora_operari=' . urlencode((string) $kgHoraRedir) . '&msg=pla_guardat');
    exit;
}

$campanya = filter_input(INPUT_GET, 'campanya', FILTER_VALIDATE_INT);
if (!$campanya) {
    $campanya = (int) date('Y');
}

$kgHora = filter_input(INPUT_GET, 'kg_hora_operari', FILTER_VALIDATE_FLOAT);
if (!$kgHora || $kgHora <= 0) {
    $kgHora = 120.0;
}
$msg = trim((string) ($_GET['msg'] ?? ''));

$sqlRotacions = "
SELECT
    pl.id_plantacio,
    pl.data_plantacio,
    pl.data_fi,
    pl.entrada_produccio_prevista,
    TIMESTAMPDIFF(YEAR, pl.data_plantacio, CURDATE()) AS edat_anys,
    s.id_sector,
    s.nom AS nom_sector,
    p.id_parcela,
    p.nom AS nom_parcela,
    v.nom_comu AS varietat,
    e.nom_comu AS especie,
    COALESCE(AVG(r.rendiment), 0) AS rendiment_mitja,
    MAX(sg.data_registre) AS ultim_seguiment
FROM plantacio pl
JOIN sector s ON s.id_sector = pl.id_sector
LEFT JOIN sector_parcela sp ON sp.id_sector = s.id_sector
LEFT JOIN parcela p ON p.id_parcela = sp.id_parcela
JOIN varietat v ON v.id_varietat = pl.id_varietat
LEFT JOIN especie e ON e.id_especie = v.id_especie
LEFT JOIN registre r ON r.id_plantacio = pl.id_plantacio
LEFT JOIN seguiment sg ON sg.id_plantacio = pl.id_plantacio
GROUP BY
    pl.id_plantacio, pl.data_plantacio, pl.data_fi, pl.entrada_produccio_prevista,
    s.id_sector, s.nom, p.id_parcela, p.nom, v.nom_comu, e.nom_comu
ORDER BY edat_anys DESC, pl.id_plantacio DESC
";

$rotacions = [];
$resRotacions = $conn->query($sqlRotacions);
if ($resRotacions) {
    while ($row = $resRotacions->fetch_assoc()) {
        $edat = (int) ($row['edat_anys'] ?? 0);
        $rendiment = (float) ($row['rendiment_mitja'] ?? 0);
        $estat = 'Mantenir';
        $accio = 'Seguiment ordinari i revisió anual.';

        if (!empty($row['data_fi'])) {
            $estat = 'Tancada';
            $accio = 'Plantació finalitzada. Preparar nova implantació després del guaret.';
        } elseif ($edat >= 20 || ($edat >= 12 && $rendiment > 0 && $rendiment < 1200)) {
            $estat = 'Renovar';
            $accio = 'Planificar arrencada i renovació varietal en la pròxima campanya.';
        } elseif ($edat >= 12) {
            $estat = 'Reconvertir';
            $accio = 'Estudiar reconversió progressiva (2-3 anys) i diversificació.';
        } elseif ((int) ($row['entrada_produccio_prevista'] ?? 0) > (int) date('Y')) {
            $estat = 'En implantació';
            $accio = 'Encara no en plena producció. Prioritzar consolidació vegetativa.';
        }

        $row['estat_recomanat'] = $estat;
        $row['accio_recomanada'] = $accio;
        $rotacions[] = $row;
    }
    $resRotacions->free();
}

$sqlEstimacio = "
SELECT
    p.id_parcela,
    p.nom AS nom_parcela,
    p.superficie,
    COALESCE(pc.previsio_kg, 0) AS previsio_kg,
    COALESCE(rh.rendiment_historic_kg, 0) AS rendiment_historic_kg,
    COALESCE(su.seguiment_kg, 0) AS seguiment_kg,
    COALESCE(ta.tasques_pendents, 0) AS tasques_pendents,
    COALESCE(infra.infraestructures, 0) AS infraestructures,
    COALESCE(cl.temperatura_mitjana, 0) AS temperatura_mitjana,
    COALESCE(cl.precipitacio_total, 0) AS precipitacio_total
FROM parcela p
LEFT JOIN (
    SELECT
        id_parcela,
        SUM(
            CASE
                WHEN unitat = 'Tn' THEN estimacio_produccio * 1000
                ELSE estimacio_produccio
            END
        ) AS previsio_kg
    FROM previsio_collita
    WHERE campanya_any = ?
    GROUP BY id_parcela
) pc ON pc.id_parcela = p.id_parcela
LEFT JOIN (
    SELECT
        id_parcela,
        AVG(rendiment) AS rendiment_historic_kg
    FROM registre
    GROUP BY id_parcela
) rh ON rh.id_parcela = p.id_parcela
LEFT JOIN (
    SELECT
        sp.id_parcela,
        SUM(COALESCE(ult.estimacio_actualizada_collita, 0)) AS seguiment_kg
    FROM sector_parcela sp
    LEFT JOIN (
        SELECT s1.id_sector, s1.estimacio_actualizada_collita
        FROM seguiment s1
        INNER JOIN (
            SELECT id_sector, MAX(data_registre) AS max_data
            FROM seguiment
            GROUP BY id_sector
        ) s2 ON s2.id_sector = s1.id_sector AND s2.max_data = s1.data_registre
    ) ult ON ult.id_sector = sp.id_sector
    GROUP BY sp.id_parcela
) su ON su.id_parcela = p.id_parcela
LEFT JOIN (
    SELECT
        sp.id_parcela,
        COUNT(*) AS tasques_pendents
    FROM sector_parcela sp
    JOIN tasca t ON t.id_sector = sp.id_sector
    WHERE t.estat IN ('Planificada', 'En curs')
    GROUP BY sp.id_parcela
) ta ON ta.id_parcela = p.id_parcela
LEFT JOIN (
    SELECT
        id_parcela,
        COUNT(*) AS infraestructures
    FROM infraestructura
    GROUP BY id_parcela
) infra ON infra.id_parcela = p.id_parcela
LEFT JOIN (
    SELECT
        sp.id_parcela,
        AVG(c.temperatura_mitjana) AS temperatura_mitjana,
        AVG(c.precipitacio_total) AS precipitacio_total
    FROM sector_parcela sp
    JOIN plantacio pl ON pl.id_sector = sp.id_sector
    JOIN clima c ON c.id_plantacio = pl.id_plantacio AND c.any_temp = ?
    GROUP BY sp.id_parcela
) cl ON cl.id_parcela = p.id_parcela
ORDER BY p.nom
";

$estimacions = [];
$stmtEstimacio = $conn->prepare($sqlEstimacio);
if ($stmtEstimacio) {
    $stmtEstimacio->bind_param('ii', $campanya, $campanya);
    $stmtEstimacio->execute();
    $resEstimacio = $stmtEstimacio->get_result();
    if ($resEstimacio) {
        while ($row = $resEstimacio->fetch_assoc()) {
            $previsio = (float) ($row['previsio_kg'] ?? 0);
            $historic = (float) ($row['rendiment_historic_kg'] ?? 0);
            $seguiment = (float) ($row['seguiment_kg'] ?? 0);

            if ($previsio > 0) {
                $estimacioFinal = ($previsio * 0.60) + ($seguiment * 0.25) + ($historic * 0.15);
                $model = 'Combinat (previsió + seguiment + històric)';
            } elseif ($seguiment > 0) {
                $estimacioFinal = ($seguiment * 0.70) + ($historic * 0.30);
                $model = 'Combinat (seguiment + històric)';
            } else {
                $estimacioFinal = $historic;
                $model = 'Històric';
            }

            $hores = $kgHora > 0 ? $estimacioFinal / $kgHora : 0;
            $operaris8h = (int) ceil($hores / 8);
            if ($operaris8h < 1 && $estimacioFinal > 0) {
                $operaris8h = 1;
            }

            $row['estimacio_final_kg'] = $estimacioFinal;
            $row['model'] = $model;
            $row['hores_estimades'] = $hores;
            $row['operaris_8h'] = $operaris8h;
            $estimacions[] = $row;
        }
        $resEstimacio->free();
    }
    $stmtEstimacio->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Planificació i estimacions</title>
    <link rel="stylesheet" href="../HTML/styles.css">
    <style>
      .status-chip {
        display: inline-block;
        border-radius: 999px;
        padding: 0.2rem 0.55rem;
        font-size: 0.75rem;
        font-weight: 700;
      }
      .status-renovar { background: #fde2e2; color: #8f1f1f; }
      .status-reconvertir { background: #fff2cc; color: #7a5a00; }
      .status-ok { background: #def7e5; color: #1a5c2e; }
      .status-tancada { background: #eceff3; color: #4b5563; }
      .status-implantacio { background: #e0ecff; color: #1f4f9d; }
      .status-row {
        display: flex;
        gap: 0.5rem;
        align-items: center;
        flex-wrap: wrap;
      }
      .mini-form {
        display: inline-flex;
        gap: 0.35rem;
        align-items: center;
        flex-wrap: wrap;
      }
      .mini-form input,
      .mini-form textarea,
      .mini-form select {
        width: auto;
        min-width: 140px;
        padding: 0.35rem 0.45rem;
      }
      .mini-form textarea {
        min-width: 230px;
        min-height: 42px;
      }
    </style>
</head>
<body>
<div class="page">
  <div class="page-header">
    <h1>Planificació de rotacions i estimacions</h1>
    <p class="page-subtitle">Eina de suport per a renovació de plantacions, previsió productiva i planificació de recursos.</p>
  </div>
  <?php if ($msg === 'pla_guardat'): ?>
    <div class="panel">
      <p style="margin:0; color:#1f6b1f; font-weight:700;">Pla de rotació guardat com a tasca planificada.</p>
    </div>
  <?php endif; ?>

  <div class="panel">
    <form method="get" class="form-grid-2">
      <label>Campanya de càlcul</label>
      <input type="number" name="campanya" min="2000" max="2100" value="<?php echo htmlspecialchars((string) $campanya); ?>">

      <label>Kg/hora per operari (referència)</label>
      <input type="number" step="0.1" name="kg_hora_operari" value="<?php echo htmlspecialchars((string) $kgHora); ?>">

      <button type="submit" class="btn btn-primary mt-2">Recalcular</button>
    </form>
  </div>

  <div class="panel mt-2">
    <h2 class="panel-title">Pla de rotacions i renovacions</h2>
    <div class="table-scroll">
      <table class="table">
        <thead>
          <tr>
            <th>Parcel·la</th>
            <th>Sector</th>
            <th>Espècie / varietat</th>
            <th>Edat</th>
            <th>Rendiment mitjà (kg)</th>
            <th>Estat recomanat</th>
            <th>Acció recomanada</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($rotacions)): ?>
            <tr><td colspan="7">No hi ha dades de plantacions per analitzar.</td></tr>
          <?php else: ?>
            <?php foreach ($rotacions as $row): ?>
              <?php
                $statusClass = 'status-ok';
                if (($row['estat_recomanat'] ?? '') === 'Renovar') {
                    $statusClass = 'status-renovar';
                } elseif (($row['estat_recomanat'] ?? '') === 'Reconvertir') {
                    $statusClass = 'status-reconvertir';
                } elseif (($row['estat_recomanat'] ?? '') === 'Tancada') {
                    $statusClass = 'status-tancada';
                } elseif (($row['estat_recomanat'] ?? '') === 'En implantació') {
                    $statusClass = 'status-implantacio';
                }
              ?>
              <tr>
                <td><?php echo htmlspecialchars($row['nom_parcela'] ?? '-'); ?></td>
                <td><?php echo htmlspecialchars($row['nom_sector'] ?? '-'); ?></td>
                <td><?php echo htmlspecialchars(($row['especie'] ?? '-') . ' / ' . ($row['varietat'] ?? '-')); ?></td>
                <td><?php echo (int) ($row['edat_anys'] ?? 0); ?> anys</td>
                <td><?php echo number_format((float) ($row['rendiment_mitja'] ?? 0), 0, ',', '.'); ?></td>
                <td>
                  <div class="status-row">
                    <span class="status-chip <?php echo $statusClass; ?>"><?php echo htmlspecialchars($row['estat_recomanat'] ?? 'Mantenir'); ?></span>
                  </div>
                </td>
                <td>
                  <div><?php echo htmlspecialchars($row['accio_recomanada'] ?? ''); ?></div>
                  <?php if (in_array($row['estat_recomanat'] ?? '', ['Renovar', 'Reconvertir'], true)): ?>
                    <form method="post" class="mini-form mt-1">
                      <input type="hidden" name="accio" value="crear_pla_rotacio">
                      <input type="hidden" name="id_sector" value="<?php echo (int) ($row['id_sector'] ?? 0); ?>">
                      <input type="hidden" name="campanya" value="<?php echo htmlspecialchars((string) $campanya); ?>">
                      <input type="hidden" name="kg_hora_operari" value="<?php echo htmlspecialchars((string) $kgHora); ?>">
                      <select name="tipus_pla" required>
                        <option value="Renovar" <?php echo (($row['estat_recomanat'] ?? '') === 'Renovar') ? 'selected' : ''; ?>>Renovar</option>
                        <option value="Reconvertir" <?php echo (($row['estat_recomanat'] ?? '') === 'Reconvertir') ? 'selected' : ''; ?>>Reconvertir</option>
                        <option value="Mantenir">Mantenir</option>
                      </select>
                      <input type="date" name="data_inici" required>
                      <input type="date" name="data_final">
                      <textarea name="detall_pla" placeholder="Detall de la planificació"></textarea>
                      <button type="submit" class="btn btn-primary">Guardar pla</button>
                    </form>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="panel mt-2">
    <h2 class="panel-title">Estimació avançada de producció i mà d'obra</h2>
    <div class="table-scroll">
      <table class="table">
        <thead>
          <tr>
            <th>Parcel·la</th>
            <th>Previsió campanya (kg)</th>
            <th>Seguiment actualitzat (kg)</th>
            <th>Històric mitjà (kg)</th>
            <th>Estimació final (kg)</th>
            <th>Model</th>
            <th>Hores estimades</th>
            <th>Operaris (jornada 8h)</th>
            <th>Tasques pendents</th>
            <th>Infraestructures</th>
            <th>Temp. mitjana</th>
            <th>Precipitació</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($estimacions)): ?>
            <tr><td colspan="12">No hi ha dades per calcular estimacions.</td></tr>
          <?php else: ?>
            <?php foreach ($estimacions as $row): ?>
              <tr>
                <td><?php echo htmlspecialchars($row['nom_parcela'] ?? '-'); ?></td>
                <td><?php echo number_format((float) ($row['previsio_kg'] ?? 0), 0, ',', '.'); ?></td>
                <td><?php echo number_format((float) ($row['seguiment_kg'] ?? 0), 0, ',', '.'); ?></td>
                <td><?php echo number_format((float) ($row['rendiment_historic_kg'] ?? 0), 0, ',', '.'); ?></td>
                <td><strong><?php echo number_format((float) ($row['estimacio_final_kg'] ?? 0), 0, ',', '.'); ?></strong></td>
                <td><?php echo htmlspecialchars($row['model'] ?? '-'); ?></td>
                <td><?php echo number_format((float) ($row['hores_estimades'] ?? 0), 1, ',', '.'); ?></td>
                <td><?php echo (int) ($row['operaris_8h'] ?? 0); ?></td>
                <td><?php echo (int) ($row['tasques_pendents'] ?? 0); ?></td>
                <td><?php echo (int) ($row['infraestructures'] ?? 0); ?></td>
                <td><?php echo number_format((float) ($row['temperatura_mitjana'] ?? 0), 1, ',', '.'); ?> ºC</td>
                <td><?php echo number_format((float) ($row['precipitacio_total'] ?? 0), 1, ',', '.'); ?> mm</td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</body>
</html>
