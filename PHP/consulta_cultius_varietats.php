<?php
require_once __DIR__ . '/db.php';
$conn = db_connect();

$id_especie = filter_input(INPUT_GET, 'id_especie', FILTER_VALIDATE_INT);
$id_varietat = filter_input(INPUT_GET, 'id_varietat', FILTER_VALIDATE_INT);

$id_especie = $id_especie ? $id_especie : 0;
$id_varietat = $id_varietat ? $id_varietat : 0;

$msg = $_GET['msg'] ?? '';
$err = $_GET['err'] ?? '';

$especies = [];
$res = $conn->query("SELECT id_especie, nom_comu, nom_cientific FROM especie ORDER BY nom_comu");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $especies[] = $row;
    }
    $res->free();
}

$sql = "SELECT e.id_especie, e.nom_comu AS especie_nom_comu, e.nom_cientific AS especie_nom_cientific,
               v.id_varietat, v.nom_comu AS varietat_nom_comu, v.nom_cientific AS varietat_nom_cientific,
               v.productivitat_mitjana
        FROM especie e
        LEFT JOIN varietat v ON v.id_especie = e.id_especie";

$condicions = [];
$types = '';
$params = [];

if ($id_especie > 0) {
    $condicions[] = 'e.id_especie = ?';
    $types .= 'i';
    $params[] = $id_especie;
}
if ($id_varietat > 0) {
    $condicions[] = 'v.id_varietat = ?';
    $types .= 'i';
    $params[] = $id_varietat;
}

if ($condicions) {
    $sql .= ' WHERE ' . implode(' AND ', $condicions);
}

$sql .= ' ORDER BY e.nom_comu, v.nom_comu';

$stmt = $conn->prepare($sql);
if ($stmt && $types !== '') {
    $stmt->bind_param($types, ...$params);
}

$rows = [];
if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $result->free();
    }
    $stmt->close();
}

$conn->close();

function mostra_missatge($msg, $err): ?array {
    $map_msg = [
        'especie_creada' => 'Espècie creada correctament.',
        'especie_actualitzada' => 'Espècie actualitzada correctament.',
        'especie_eliminada' => 'Espècie eliminada correctament.',
        'varietat_creada' => 'Varietat creada correctament.',
        'varietat_actualitzada' => 'Varietat actualitzada correctament.',
        'varietat_eliminada' => 'Varietat eliminada correctament.'
    ];
    $map_err = [
        'especie_guardar' => 'No s\'ha pogut guardar l\'espècie.',
        'especie_actualitzar' => 'No s\'ha pogut actualitzar l\'espècie.',
        'especie_eliminar_dependencia' => 'No es pot eliminar l\'espècie perquè té varietats associades.',
        'especie_eliminar' => 'No s\'ha pogut eliminar l\'espècie.',
        'varietat_guardar' => 'No s\'ha pogut guardar la varietat.',
        'varietat_actualitzar' => 'No s\'ha pogut actualitzar la varietat.',
        'varietat_eliminar_dependencia' => 'No es pot eliminar la varietat perquè té dependències en plantacions o sectors.',
        'varietat_eliminar' => 'No s\'ha pogut eliminar la varietat.'
    ];

    if ($msg && isset($map_msg[$msg])) {
        return ['tipus' => 'ok', 'text' => $map_msg[$msg]];
    }
    if ($err && isset($map_err[$err])) {
        return ['tipus' => 'err', 'text' => $map_err[$err]];
    }
    return null;
}

$missatge = mostra_missatge($msg, $err);
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Consulta de cultius i varietats</title>
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
      .thumb {
        width: 48px;
        height: 48px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid #d8e3c8;
      }
      .alert {
        padding: 10px 12px;
        margin-bottom: 12px;
        border-radius: 6px;
      }
      .alert.ok {
        background: #f1fff1;
        border: 1px solid #c9e6c9;
        color: #1f6b1f;
      }
      .alert.err {
        background: #fff5f5;
        border: 1px solid #e3b2b2;
        color: #8a2a2a;
      }
    </style>
</head>
<body>

<div class="page">
<div class="page-header">
  <div class="panel-header">
    <div>
      <h1>Consulta de cultius i varietats</h1>
      <p class="page-subtitle">Filtra per espècie i varietat per veure el detall.</p>
    </div>
    <div class="page-header-actions">
      <a class="btn btn-primary" href="especie_nova.php">+ Nova espècie</a>
      <a class="btn btn-primary" href="varietat_nova.php">+ Nova varietat</a>
    </div>
  </div>
</div>

<?php if ($missatge): ?>
<div class="panel">
  <div class="alert <?php echo $missatge['tipus'] === 'ok' ? 'ok' : 'err'; ?>">
    <?php echo htmlspecialchars($missatge['text']); ?>
  </div>
</div>
<?php endif; ?>

<div class="panel">
<form method="get" class="form-grid-2">
    <label>Espècie</label>
    <select name="id_especie" id="id_especie">
        <option value="">(Totes)</option>
        <?php foreach ($especies as $especie): ?>
            <?php $sel = ((int)($especie['id_especie'] ?? 0) === $id_especie) ? 'selected' : ''; ?>
            <option value="<?php echo (int)$especie['id_especie']; ?>" <?php echo $sel; ?>
                data-nom="<?php echo htmlspecialchars(($especie['nom_comu'] ?? '') . ' (' . ($especie['nom_cientific'] ?? '') . ')'); ?>">
                <?php echo htmlspecialchars(($especie['nom_comu'] ?? '') . ' (' . ($especie['nom_cientific'] ?? '') . ')'); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Varietat</label>
    <select name="id_varietat" id="id_varietat" data-selected="<?php echo (int)$id_varietat; ?>">
        <option value="">(Totes)</option>
    </select>

    <button type="submit" class="btn btn-primary mt-2">Filtrar</button>
</form>
<p id="varietats-error" class="alert err" style="display:none; margin-top:12px;">No s'han pogut carregar les varietats. Torna-ho a provar.</p>
</div>

<div class="panel mt-2">
<?php if (!empty($rows)): ?>
    <div class="table-scroll">
    <table class="table">
        <tr>
            <th>Espècie</th>
            <th>Varietat</th>
            <th>Productivitat mitjana</th>
            <th>Foto</th>
            <th>Accions espècie</th>
            <th>Accions varietat</th>
        </tr>
        <?php foreach ($rows as $row): ?>
        <tr>
            <td>
              <?php echo htmlspecialchars(($row['especie_nom_comu'] ?? '') . ' (' . ($row['especie_nom_cientific'] ?? '') . ')'); ?>
            </td>
            <td>
              <?php if (!empty($row['id_varietat'])): ?>
                <?php echo htmlspecialchars(($row['varietat_nom_comu'] ?? '') . ' (' . ($row['varietat_nom_cientific'] ?? '') . ')'); ?>
              <?php else: ?>
                -
              <?php endif; ?>
            </td>
            <td><?php echo htmlspecialchars($row['productivitat_mitjana'] ?? '-'); ?></td>
            <td>
              <?php if (!empty($row['foto_url'])): ?>
                <img class="thumb" src="<?php echo htmlspecialchars($row['foto_url']); ?>" alt="Foto varietat" onerror="this.style.display='none';">
              <?php else: ?>
                -
              <?php endif; ?>
            </td>
            <td>
              <div class="table-actions">
                <a href="especie_detall.php?id=<?php echo (int)$row['id_especie']; ?>" title="Veure espècie">
                  <i class="fa-solid fa-eye" aria-hidden="true"></i>
                </a>
                <a href="especie_editar.php?id=<?php echo (int)$row['id_especie']; ?>" title="Editar espècie">
                  <i class="fa-solid fa-pen-to-square" aria-hidden="true"></i>
                </a>
                <a href="especie_eliminar.php?id=<?php echo (int)$row['id_especie']; ?>" onclick="return confirm('Segur que vols eliminar aquesta espècie?');" title="Eliminar espècie">
                  <i class="fa-solid fa-trash" aria-hidden="true"></i>
                </a>
              </div>
            </td>
            <td>
              <?php if (!empty($row['id_varietat'])): ?>
              <div class="table-actions">
                <a href="varietat_detall.php?id=<?php echo (int)$row['id_varietat']; ?>" title="Veure varietat">
                  <i class="fa-solid fa-eye" aria-hidden="true"></i>
                </a>
                <a href="varietat_editar.php?id=<?php echo (int)$row['id_varietat']; ?>" title="Editar varietat">
                  <i class="fa-solid fa-pen-to-square" aria-hidden="true"></i>
                </a>
                <a href="varietat_eliminar.php?id=<?php echo (int)$row['id_varietat']; ?>" onclick="return confirm('Segur que vols eliminar aquesta varietat?');" title="Eliminar varietat">
                  <i class="fa-solid fa-trash" aria-hidden="true"></i>
                </a>
              </div>
              <?php else: ?>
                -
              <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    </div>
<?php else: ?>
    <p>No hi ha resultats amb aquests filtres.</p>
<?php endif; ?>
</div>

</div>

<script>
  function openPage(url) {
    if (window.parent && window.parent !== window && typeof window.parent.openPage === 'function') {
      window.parent.openPage(url);
      return;
    }
    window.location.href = url;
  }

  function carregarVarietats(idEspecie, seleccionada) {
    const selectVarietat = document.getElementById('id_varietat');
    const errorBox = document.getElementById('varietats-error');
    if (!selectVarietat) return;
    if (errorBox) errorBox.style.display = 'none';

    fetch('ajax_varietats_by_especie.php?id_especie=' + encodeURIComponent(idEspecie || ''))
      .then(res => res.json())
      .then(data => {
        selectVarietat.innerHTML = '<option value="">(Totes)</option>';
        data.forEach(item => {
          const opt = document.createElement('option');
          opt.value = item.id_varietat;
          opt.textContent = item.nom_comu + ' (' + item.nom_cientific + ')';
          if (String(item.id_varietat) === String(seleccionada)) {
            opt.selected = true;
          }
          selectVarietat.appendChild(opt);
        });
      })
      .catch(() => {
        selectVarietat.innerHTML = '<option value="">(Totes)</option>';
        if (errorBox) errorBox.style.display = 'block';
      });
  }

  document.addEventListener('DOMContentLoaded', () => {
    const selectEspecie = document.getElementById('id_especie');
    const selectVarietat = document.getElementById('id_varietat');
    const seleccionada = selectVarietat ? selectVarietat.dataset.selected : '';

    carregarVarietats(selectEspecie ? selectEspecie.value : '', seleccionada);

    if (selectEspecie) {
      selectEspecie.addEventListener('change', () => {
        if (selectVarietat) {
          selectVarietat.dataset.selected = '';
        }
        carregarVarietats(selectEspecie.value, '');
      });
    }
  });
</script>

</body>
</html>
