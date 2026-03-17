<?php
require_once __DIR__ . '/db.php';

$conn = db_connect();

// --- LLEGIR FILTRES DEL FORMULARI (GET) ---
$nom_comercial = trim($_GET['nom_comercial'] ?? '');
$tipus = $_GET['tipus'] ?? '';
$id_magatzem = $_GET['id_magatzem'] ?? '';
$numero_lot = trim($_GET['numero_lot'] ?? '');
$proveidor = trim($_GET['proveidor'] ?? '');
$data_caducitat_des = $_GET['data_caducitat_des'] ?? '';
$data_caducitat_fins = $_GET['data_caducitat_fins'] ?? '';
$quantitat_min = $_GET['quantitat_min'] ?? '';
$quantitat_max = $_GET['quantitat_max'] ?? '';

$mostrar_tecnica = isset($_GET['mostrar_tecnica']) ? (int) $_GET['mostrar_tecnica'] : 1;
$mostrar_seguretat = isset($_GET['mostrar_seguretat']) ? (int) $_GET['mostrar_seguretat'] : 1;
$mostrar_estoc = isset($_GET['mostrar_estoc']) ? (int) $_GET['mostrar_estoc'] : 1;

// --- CARREGAR MAGATZEMS PER AL SELECT ---
$magatzems = [];
$magatzem_result = $conn->query("SELECT id_magatzem, nom FROM magatzem ORDER BY nom");
if ($magatzem_result) {
    while ($row = $magatzem_result->fetch_assoc()) {
        $magatzems[] = $row;
    }
}

// --- CONSTRUIR CONSULTA AMB PREPARED STATEMENTS ---
$sql = "
    SELECT
        pl.id_lot,
        p.id_producte,
        pl.numero_lot,
        pl.data_caducitat,
        pl.quantitat_disponible,
        pl.unitat,
        pl.fabricant,
        pl.proveidor,
        pl.data_compra,
        pl.preu_unitari,
        p.nom_comercial,
        p.tipus,
        p.materia_activa,
        p.concentracio,
        p.espectre_accio,
        p.cultius_autoritzats,
        p.dosi_recomendada,
        p.dosi_maxima,
        p.termini_seguretat_dies,
        p.classificacio_toxicologica,
        p.restriccions_usu,
        p.compatible_integrada,
        m.nom AS magatzem_nom,
        m.ubicacio AS magatzem_ubicacio
    FROM producte_lot pl
    JOIN producte p ON p.id_producte = pl.id_producte
    LEFT JOIN magatzem m ON m.id_magatzem = pl.id_magatzem
";

$condicions = [];
$params = [];
$types = '';

if ($nom_comercial !== '') {
    $condicions[] = "p.nom_comercial LIKE ?";
    $params[] = '%' . $nom_comercial . '%';
    $types .= 's';
}
if ($tipus !== '') {
    $condicions[] = "p.tipus = ?";
    $params[] = $tipus;
    $types .= 's';
}
if ($id_magatzem !== '' && ctype_digit($id_magatzem)) {
    $condicions[] = "pl.id_magatzem = ?";
    $params[] = (int) $id_magatzem;
    $types .= 'i';
}
if ($numero_lot !== '') {
    $condicions[] = "pl.numero_lot LIKE ?";
    $params[] = '%' . $numero_lot . '%';
    $types .= 's';
}
if ($proveidor !== '') {
    $condicions[] = "pl.proveidor LIKE ?";
    $params[] = '%' . $proveidor . '%';
    $types .= 's';
}
if ($data_caducitat_des !== '') {
    $condicions[] = "pl.data_caducitat >= ?";
    $params[] = $data_caducitat_des;
    $types .= 's';
}
if ($data_caducitat_fins !== '') {
    $condicions[] = "pl.data_caducitat <= ?";
    $params[] = $data_caducitat_fins;
    $types .= 's';
}
if ($quantitat_min !== '' && is_numeric($quantitat_min)) {
    $condicions[] = "pl.quantitat_disponible >= ?";
    $params[] = (float) $quantitat_min;
    $types .= 'd';
}
if ($quantitat_max !== '' && is_numeric($quantitat_max)) {
    $condicions[] = "pl.quantitat_disponible <= ?";
    $params[] = (float) $quantitat_max;
    $types .= 'd';
}

if (!empty($condicions)) {
    $sql .= " WHERE " . implode(" AND ", $condicions);
}
$sql .= " ORDER BY p.nom_comercial, pl.numero_lot";

$lots = [];
$stmt = $conn->prepare($sql);
if ($stmt) {
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $lots[] = $row;
        }
    }
    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Consulta de productes</title>
    <link rel="stylesheet" href="../HTML/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
      .page-header-actions {
        display: flex;
        gap: 0.75rem;
        justify-content: flex-end;
        flex-wrap: wrap;
      }
      .table-actions {
        display: inline-flex;
        align-items: center;
        gap: 0.6rem;
      }
      .table-actions a {
        color: #2f4f2f;
        text-decoration: none;
      }
      .table-actions a:hover {
        color: #3d9b3d;
      }
      .filter-group {
        margin-top: 0.5rem;
      }
      .filter-checks {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
      }
      .col-hidden {
        display: none;
      }
    </style>
</head>
<body>

<div class="page">
  <div class="page-header">
    <div class="panel-header">
      <div>
        <h1>Consulta de productes</h1>
        <p class="page-subtitle">Llistat de lots amb dades de producte i magatzem.</p>
      </div>
      <div class="page-header-actions">
        <a class="btn btn-primary" href="../HTML/producte_lot.html">Nou lot de producte</a>
        <a class="btn" href="consulta_moviment_estoc.php">Veure moviments d'estoc</a>
      </div>
    </div>
  </div>

  <div class="panel">
    <form method="get" class="form-grid-2">
        <label>Nom comercial</label>
        <div>
          <input type="text" name="nom_comercial" value="<?php echo htmlspecialchars($nom_comercial); ?>" list="nom-comercial-list" autocomplete="off">
          <small id="nom-comercial-count"></small>
        </div>
        <datalist id="nom-comercial-list"></datalist>

        <label>Tipus</label>
        <select name="tipus">
            <option value="">(Qualsevol)</option>
            <?php
            $opcions_tipus = ['Fitosanitari', 'Fertilitzant'];
            foreach ($opcions_tipus as $opcio) {
                $sel = ($opcio === $tipus) ? 'selected' : '';
                echo "<option value=\"$opcio\" $sel>$opcio</option>";
            }
            ?>
        </select>

        <label>Magatzem</label>
        <select name="id_magatzem">
            <option value="">(Qualsevol)</option>
            <?php foreach ($magatzems as $magatzem): ?>
                <?php
                $mid = (string) ($magatzem['id_magatzem'] ?? '');
                $sel = ($mid === (string) $id_magatzem) ? 'selected' : '';
                ?>
                <option value="<?php echo htmlspecialchars($mid); ?>" <?php echo $sel; ?>>
                    <?php echo htmlspecialchars($magatzem['nom'] ?? ''); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Número de lot</label>
        <input type="text" name="numero_lot" value="<?php echo htmlspecialchars($numero_lot); ?>">

        <label>Proveïdor</label>
        <input type="text" name="proveidor" value="<?php echo htmlspecialchars($proveidor); ?>">

        <label>Data caducitat (des)</label>
        <input type="date" name="data_caducitat_des" value="<?php echo htmlspecialchars($data_caducitat_des); ?>">

        <label>Data caducitat (fins)</label>
        <input type="date" name="data_caducitat_fins" value="<?php echo htmlspecialchars($data_caducitat_fins); ?>">

        <label>Quantitat mínima</label>
        <input type="number" step="0.001" name="quantitat_min" value="<?php echo htmlspecialchars($quantitat_min); ?>">

        <label>Quantitat màxima</label>
        <input type="number" step="0.001" name="quantitat_max" value="<?php echo htmlspecialchars($quantitat_max); ?>">

        <div class="filter-group">
          <label>Mostrar columnes</label>
          <div class="filter-checks">
            <label>
              <input type="checkbox" id="mostrar-tecnica" name="mostrar_tecnica" value="1" <?php echo $mostrar_tecnica ? 'checked' : ''; ?>>
              Informació tècnica
            </label>
            <label>
              <input type="checkbox" id="mostrar-seguretat" name="mostrar_seguretat" value="1" <?php echo $mostrar_seguretat ? 'checked' : ''; ?>>
              Seguretat i restriccions
            </label>
            <label>
              <input type="checkbox" id="mostrar-estoc" name="mostrar_estoc" value="1" <?php echo $mostrar_estoc ? 'checked' : ''; ?>>
              Estoc, lots i altres
            </label>
          </div>
        </div>

        <button type="submit" class="btn btn-primary mt-2">Filtrar</button>
    </form>
  </div>

  <div class="panel mt-2">
  <?php if (!empty($lots)): ?>
      <div class="table-scroll">
      <table class="table">
          <tr>
              <th>Producte</th>
              <th>Tipus</th>
              <th class="col-tecnica">Matèria activa</th>
              <th class="col-tecnica">Concentració</th>
              <th class="col-tecnica">Espectre d'acció</th>
              <th class="col-tecnica">Cultius autoritzats</th>
              <th class="col-seguretat">Dosi recomanada</th>
              <th class="col-seguretat">Dosi màxima</th>
              <th class="col-seguretat">Termini seguretat (dies)</th>
              <th class="col-seguretat">Classificació toxicològica</th>
              <th class="col-seguretat">Restriccions d'ús</th>
              <th class="col-seguretat">Compatible integrada</th>
              <th class="col-estoc">Número lot</th>
              <th class="col-estoc">Data caducitat</th>
              <th class="col-estoc">Magatzem</th>
              <th class="col-estoc">Ubicació magatzem</th>
              <th class="col-estoc">Quantitat disponible</th>
              <th class="col-estoc">Unitat</th>
              <th class="col-estoc">Fabricant</th>
              <th class="col-estoc">Proveïdor</th>
              <th class="col-estoc">Data compra</th>
              <th class="col-estoc">Preu unitari</th>
              <th class="col-estoc">Accions</th>
          </tr>
          <?php foreach ($lots as $lot): ?>
          <tr>
              <td><?php echo htmlspecialchars($lot['nom_comercial'] ?? ''); ?></td>
              <td><?php echo htmlspecialchars($lot['tipus'] ?? ''); ?></td>
              <td class="col-tecnica"><?php echo htmlspecialchars($lot['materia_activa'] ?? ''); ?></td>
              <td class="col-tecnica"><?php echo htmlspecialchars($lot['concentracio'] ?? ''); ?></td>
              <td class="col-tecnica"><?php echo htmlspecialchars($lot['espectre_accio'] ?? ''); ?></td>
              <td class="col-tecnica"><?php echo htmlspecialchars($lot['cultius_autoritzats'] ?? ''); ?></td>
              <td class="col-seguretat"><?php echo htmlspecialchars($lot['dosi_recomendada'] ?? ''); ?></td>
              <td class="col-seguretat"><?php echo htmlspecialchars($lot['dosi_maxima'] ?? ''); ?></td>
              <td class="col-seguretat"><?php echo htmlspecialchars($lot['termini_seguretat_dies'] ?? ''); ?></td>
              <td class="col-seguretat"><?php echo htmlspecialchars($lot['classificacio_toxicologica'] ?? ''); ?></td>
              <td class="col-seguretat"><?php echo htmlspecialchars($lot['restriccions_usu'] ?? ''); ?></td>
              <td class="col-seguretat"><?php echo !empty($lot['compatible_integrada']) ? 'Sí' : 'No'; ?></td>
              <td class="col-estoc"><?php echo htmlspecialchars($lot['numero_lot'] ?? ''); ?></td>
              <td class="col-estoc"><?php echo htmlspecialchars($lot['data_caducitat'] ?? ''); ?></td>
              <td class="col-estoc"><?php echo htmlspecialchars($lot['magatzem_nom'] ?? ''); ?></td>
              <td class="col-estoc"><?php echo htmlspecialchars($lot['magatzem_ubicacio'] ?? ''); ?></td>
              <td class="col-estoc"><?php echo htmlspecialchars($lot['quantitat_disponible'] ?? ''); ?></td>
              <td class="col-estoc"><?php echo htmlspecialchars($lot['unitat'] ?? ''); ?></td>
              <td class="col-estoc"><?php echo htmlspecialchars($lot['fabricant'] ?? ''); ?></td>
              <td class="col-estoc"><?php echo htmlspecialchars($lot['proveidor'] ?? ''); ?></td>
              <td class="col-estoc"><?php echo htmlspecialchars($lot['data_compra'] ?? ''); ?></td>
              <td class="col-estoc"><?php echo htmlspecialchars($lot['preu_unitari'] ?? ''); ?></td>
              <td class="col-estoc">
                <div class="table-actions">
                  <a href="producte_detall.php?id_producte=<?php echo urlencode((string) ($lot['id_producte'] ?? '')); ?>">Detall producte</a>
                  |
                  <a href="producte_lot_detall.php?id_lot=<?php echo urlencode((string) ($lot['id_lot'] ?? '')); ?>">Detall lot</a>
                  |
                  <a href="consulta_moviment_estoc.php?id_lot=<?php echo urlencode((string) ($lot['id_lot'] ?? '')); ?>">Moviments</a>
                </div>
              </td>
          </tr>
          <?php endforeach; ?>
      </table>
      </div>
  <?php else: ?>
      <p>No hi ha lots de producte disponibles.</p>
  <?php endif; ?>
  </div>
</div>

<script>
  const toggleColumns = (selector, visible) => {
    document.querySelectorAll(selector).forEach((cell) => {
      cell.classList.toggle('col-hidden', !visible);
    });
  };

  const checkboxTecnica = document.getElementById('mostrar-tecnica');
  const checkboxSeguretat = document.getElementById('mostrar-seguretat');
  const checkboxEstoc = document.getElementById('mostrar-estoc');

  const applyColumnVisibility = () => {
    toggleColumns('.col-tecnica', checkboxTecnica ? checkboxTecnica.checked : true);
    toggleColumns('.col-seguretat', checkboxSeguretat ? checkboxSeguretat.checked : true);
    toggleColumns('.col-estoc', checkboxEstoc ? checkboxEstoc.checked : true);
  };

  applyColumnVisibility();

  if (checkboxTecnica) {
    checkboxTecnica.addEventListener('change', applyColumnVisibility);
  }
  if (checkboxSeguretat) {
    checkboxSeguretat.addEventListener('change', applyColumnVisibility);
  }
  if (checkboxEstoc) {
    checkboxEstoc.addEventListener('change', applyColumnVisibility);
  }

  const nomInput = document.querySelector('input[name="nom_comercial"]');
  const tipusSelect = document.querySelector('select[name="tipus"]');
  const magatzemSelect = document.querySelector('select[name="id_magatzem"]');
  const datalist = document.getElementById('nom-comercial-list');
  const countLabel = document.getElementById('nom-comercial-count');
  let lastQuery = '';

  const updateDatalist = (items) => {
    datalist.innerHTML = '';
    items.forEach((item) => {
      const option = document.createElement('option');
      option.value = item;
      datalist.appendChild(option);
    });
    if (countLabel) {
      countLabel.textContent = items.length ? `${items.length} opcions` : '';
    }
  };

  const fetchSuggestions = (query) => {
    if (!query || query.length < 1) {
      updateDatalist([]);
      return;
    }
    const tipus = tipusSelect ? tipusSelect.value : '';
    const idMagatzem = magatzemSelect ? magatzemSelect.value : '';
    const params = new URLSearchParams({
      q: query,
      tipus: tipus || '',
      id_magatzem: idMagatzem || ''
    });
    fetch(`ajax_producte_nom_comercial.php?${params.toString()}`)
      .then((response) => response.json())
      .then((items) => updateDatalist(items))
      .catch(() => updateDatalist([]));
  };

  if (nomInput) {
    nomInput.addEventListener('input', () => {
      const query = nomInput.value.trim();
      if (query === lastQuery) return;
      lastQuery = query;
      fetchSuggestions(query);
    });
  }
</script>

</body>
</html>
