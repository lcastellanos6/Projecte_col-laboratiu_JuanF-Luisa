<?php
require_once __DIR__ . '/db.php';
$conn = db_connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom_cientific = trim($_POST['nom_cientific'] ?? '');
    $nom_comu = trim($_POST['nom_comu'] ?? '');

    if ($nom_cientific === '' || $nom_comu === '') {
        $conn->close();
        header('Location: consulta_cultius_varietats.php?err=especie_guardar');
        exit;
    }

    $stmt = $conn->prepare('INSERT INTO especie (nom_cientific, nom_comu) VALUES (?, ?)');
    $stmt->bind_param('ss', $nom_cientific, $nom_comu);

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header('Location: consulta_cultius_varietats.php?msg=especie_creada');
        exit;
    }

    $stmt->close();
    $conn->close();
    header('Location: consulta_cultius_varietats.php?err=especie_guardar');
    exit;
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Nova espècie</title>
    <link rel="stylesheet" href="../HTML/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
  <div style="padding:15px; background:#f4fff4; border-bottom:1px solid #ddd; text-align:right;">
    <a href="#" class="btn btn-primary" onclick="history.back(); return false;"><i class="fa-solid fa-arrow-left"></i> Tornar</a>
  </div>
<div class="page">
    <div class="page-header">
        <h1>Registrar nova espècie</h1>
        <p class="page-subtitle">Introdueix el nom comú i el nom científic.</p>
    </div>

    <div class="panel">
        <form method="post" action="especie_nova.php" id="form-especie">
            <label>Nom comú:</label>
            <input type="text" name="nom_comu" id="nom_comu" list="llista_nom_comu" required>
            <datalist id="llista_nom_comu"></datalist>
            <p id="avisa_comu" class="alert err" style="display:none;">Ja existeix una espècie amb aquest nom comú.</p>

            <label>Nom científic:</label>
            <input type="text" name="nom_cientific" id="nom_cientific" list="llista_nom_cientific" required>
            <datalist id="llista_nom_cientific"></datalist>
            <p id="avisa_cientific" class="alert err" style="display:none;">Ja existeix una espècie amb aquest nom científic.</p>

            <button type="submit" class="btn btn-primary btn-full mt-2">Guardar espècie</button>
            <a class="btn btn-ghost btn-full mt-2" href="consulta_cultius_varietats.php">Tornar a la consulta</a>
        </form>
    </div>
</div>
<script>
  function carregarSuggeriments(camp, valor, datalistId, avisaId) {
    const list = document.getElementById(datalistId);
    const avisa = document.getElementById(avisaId);
    if (!list) return;
    if (avisa) avisa.style.display = 'none';

    fetch('ajax_especies_autocomplete.php?camp=' + encodeURIComponent(camp) + '&q=' + encodeURIComponent(valor || ''))
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
    const inputComu = document.getElementById('nom_comu');
    const inputCientific = document.getElementById('nom_cientific');
    if (inputComu) {
      inputComu.addEventListener('input', debounce(() => {
        carregarSuggeriments('nom_comu', inputComu.value, 'llista_nom_comu', 'avisa_comu');
      }, 200));
    }
    if (inputCientific) {
      inputCientific.addEventListener('input', debounce(() => {
        carregarSuggeriments('nom_cientific', inputCientific.value, 'llista_nom_cientific', 'avisa_cientific');
      }, 200));
    }
  });
</script>
</body>
</html>
