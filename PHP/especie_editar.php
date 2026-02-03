<?php
require_once __DIR__ . '/db.php';
$conn = db_connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_especie = filter_input(INPUT_POST, 'id_especie', FILTER_VALIDATE_INT);
    $nom_cientific = trim($_POST['nom_cientific'] ?? '');
    $nom_comu = trim($_POST['nom_comu'] ?? '');

    if (!$id_especie || $nom_cientific === '' || $nom_comu === '') {
        $conn->close();
        header('Location: consulta_cultius_varietats.php?err=especie_actualitzar');
        exit;
    }

    $stmt = $conn->prepare('UPDATE especie SET nom_cientific = ?, nom_comu = ? WHERE id_especie = ?');
    $stmt->bind_param('ssi', $nom_cientific, $nom_comu, $id_especie);

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header('Location: consulta_cultius_varietats.php?msg=especie_actualitzada');
        exit;
    }

    $stmt->close();
    $conn->close();
    header('Location: consulta_cultius_varietats.php?err=especie_actualitzar');
    exit;
}

$id_especie = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id_especie) {
    $conn->close();
    echo "<p style='color:red; font-weight:bold;'>ID d'espècie no vàlid.</p>";
    exit;
}

$stmt = $conn->prepare('SELECT id_especie, nom_cientific, nom_comu FROM especie WHERE id_especie = ?');
$stmt->bind_param('i', $id_especie);
$stmt->execute();
$res = $stmt->get_result();
$especie = $res ? $res->fetch_assoc() : null;
$stmt->close();
$conn->close();

if (!$especie) {
    echo "<p style='color:red; font-weight:bold;'>Espècie no trobada.</p>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Editar espècie</title>
    <link rel="stylesheet" href="../HTML/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
  <div style="padding:15px; background:#f4fff4; border-bottom:1px solid #ddd; text-align:right;">
    <a href="#" class="btn btn-primary" onclick="history.back(); return false;"><i class="fa-solid fa-arrow-left"></i> Tornar</a>
  </div>
<div class="page">
    <div class="page-header">
        <h1>Editar espècie</h1>
    </div>

    <div class="panel">
        <form method="post" action="especie_editar.php" id="form-especie" data-id="<?php echo (int)$especie['id_especie']; ?>">
            <input type="hidden" name="id_especie" value="<?php echo (int)$especie['id_especie']; ?>">

            <label>Nom comú:</label>
            <input type="text" name="nom_comu" id="nom_comu" list="llista_nom_comu" value="<?php echo htmlspecialchars($especie['nom_comu'] ?? ''); ?>" required>
            <datalist id="llista_nom_comu"></datalist>
            <p id="avisa_comu" class="alert err" style="display:none;">Ja existeix una espècie amb aquest nom comú.</p>

            <label>Nom científic:</label>
            <input type="text" name="nom_cientific" id="nom_cientific" list="llista_nom_cientific" value="<?php echo htmlspecialchars($especie['nom_cientific'] ?? ''); ?>" required>
            <datalist id="llista_nom_cientific"></datalist>
            <p id="avisa_cientific" class="alert err" style="display:none;">Ja existeix una espècie amb aquest nom científic.</p>

            <button type="submit" class="btn btn-primary btn-full mt-2">Guardar canvis</button>
            <a class="btn btn-ghost btn-full mt-2" href="consulta_cultius_varietats.php">Cancel·lar</a>
        </form>
    </div>
</div>
<script>
  function carregarSuggeriments(camp, valor, datalistId, avisaId, idEspecie) {
    const list = document.getElementById(datalistId);
    const avisa = document.getElementById(avisaId);
    if (!list) return;
    if (avisa) avisa.style.display = 'none';

    const params = new URLSearchParams({
      camp: camp,
      q: valor || '',
      id_especie: idEspecie || ''
    });

    fetch('ajax_especies_autocomplete.php?' + params.toString())
      .then(res => res.json())
      .then(data => {
        list.innerHTML = '';
        (data.items || []).forEach(item => {
          const opt = document.createElement('option');
          opt.value = item;
          list.appendChild(opt);
        });
        if (avisa && data.exact) {
          avisa.style.display = 'block';
        }
      })
      .catch(() => {
        if (avisa) avisa.style.display = 'none';
      });
  }

  function debounce(fn, wait) {
    let t;
    return function (...args) {
      clearTimeout(t);
      t = setTimeout(() => fn.apply(this, args), wait);
    };
  }

  document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('form-especie');
    const idEspecie = form ? form.dataset.id : '';
    const inputComu = document.getElementById('nom_comu');
    const inputCientific = document.getElementById('nom_cientific');
    if (inputComu) {
      inputComu.addEventListener('input', debounce(() => {
        carregarSuggeriments('nom_comu', inputComu.value, 'llista_nom_comu', 'avisa_comu', idEspecie);
      }, 200));
    }
    if (inputCientific) {
      inputCientific.addEventListener('input', debounce(() => {
        carregarSuggeriments('nom_cientific', inputCientific.value, 'llista_nom_cientific', 'avisa_cientific', idEspecie);
      }, 200));
    }
  });
</script>
</body>
</html>
