<?php
require_once __DIR__ . '/db.php';
$conn = db_connect();

function normalitza_tipus(string $valor): string {
    $valor = mb_strtolower($valor, 'UTF-8');
    $valor = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $valor);
    $valor = preg_replace('/[^a-z0-9 ]/', '', $valor ?? '');
    $valor = preg_replace('/\s+/', ' ', trim($valor));
    return $valor;
}

function ruta_retorn_segura(string $ruta): bool {
    if ($ruta === '') {
        return false;
    }
    $parts = parse_url($ruta);
    if ($parts === false) {
        return false;
    }
    return empty($parts['scheme']) && empty($parts['host']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_sol = filter_input(INPUT_POST, 'id_sol', FILTER_VALIDATE_INT);
    $tipus = trim($_POST['tipus'] ?? '');
    $ph = $_POST['ph'] !== '' ? $_POST['ph'] : null;
    $materia_org = $_POST['materia_organica'] !== '' ? $_POST['materia_organica'] : null;
    $observacions = $_POST['observacions'] !== '' ? $_POST['observacions'] : null;
    $retorn = trim($_POST['retorn'] ?? '');

    if (!$id_sol || $tipus === '') {
        $conn->close();
        echo "<p style='color:red; font-weight:bold;'>Dades no vàlides per actualitzar el sòl.</p>";
        exit;
    }

    $tipus_norm = normalitza_tipus($tipus);
    $duplicat = false;
    $tipus_trobat = '';

    $stmt = $conn->prepare("SELECT id_sol, tipus FROM sol WHERE id_sol <> ?");
    $stmt->bind_param('i', $id_sol);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $row_norm = normalitza_tipus($row['tipus'] ?? '');
            if ($row_norm !== '' && $row_norm === $tipus_norm) {
                $duplicat = true;
                $tipus_trobat = $row['tipus'] ?? '';
                break;
            }
        }
        $res->free();
    }
    $stmt->close();

    if ($duplicat) {
        echo "<h3>Aquest tipus de sòl ja existeix.</h3>";
        if ($tipus_trobat !== '') {
            $tipus_seg = htmlspecialchars($tipus_trobat, ENT_QUOTES, 'UTF-8');
            echo "<p>Ja registrat com a: <strong>{$tipus_seg}</strong></p>";
        }
        echo "<a href='../HTML/nou_sol.html'>Tornar</a>";
        $conn->close();
        exit;
    }

    $stmt = $conn->prepare("UPDATE sol SET tipus = ?, ph = ?, materia_organica = ?, observacions = ? WHERE id_sol = ?");
    $stmt->bind_param('sddsi', $tipus, $ph, $materia_org, $observacions, $id_sol);
    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        if (ruta_retorn_segura($retorn)) {
            header("Location: $retorn");
        } else {
            header("Location: ../HTML/nou_sol.html");
        }
        exit;
    }

    $stmt->close();
    $conn->close();
    echo "<p style='color:red; font-weight:bold;'>No s'ha pogut actualitzar el sòl.</p>";
    exit;
}

$id_sol = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id_sol) {
    $conn->close();
    echo "<p style='color:red; font-weight:bold;'>ID de sòl no vàlid.</p>";
    exit;
}

$stmt = $conn->prepare("SELECT id_sol, tipus, ph, materia_organica, observacions FROM sol WHERE id_sol = ?");
$stmt->bind_param('i', $id_sol);
$stmt->execute();
$res = $stmt->get_result();
$sol = $res ? $res->fetch_assoc() : null;
$stmt->close();
$conn->close();

if (!$sol) {
    echo "<p style='color:red; font-weight:bold;'>Sòl no trobat.</p>";
    exit;
}

$retorn = $_SERVER['HTTP_REFERER'] ?? '';
if (!ruta_retorn_segura($retorn)) {
    $retorn = '';
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
<meta charset="UTF-8">
<title>Editar tipus de sòl</title>
<link rel="stylesheet" href="../HTML/styles.css">
</head>
<body>
<div class="page">
<div class="page-header">
  <h2>Editar tipus de sòl</h2>
</div>

<div class="panel">
<form action="editar_sol.php" method="post" id="form-sol" data-id="<?php echo (int)$sol['id_sol']; ?>">
    <input type="hidden" name="id_sol" value="<?php echo (int)$sol['id_sol']; ?>">
    <input type="hidden" name="retorn" value="<?php echo htmlspecialchars($retorn, ENT_QUOTES, 'UTF-8'); ?>">

    <label>Tipus de sòl *</label>
    <input type="text" name="tipus" id="tipus-sol" required autocomplete="off" value="<?php echo htmlspecialchars($sol['tipus'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
    <small id="tipus-avis" style="display:none;color:#b42318;">Aquest tipus de sòl ja existeix.</small>

    <label>pH</label>
    <input type="number" step="0.01" name="ph" value="<?php echo htmlspecialchars((string)($sol['ph'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">

    <label>Matèria orgànica (%)</label>
    <input type="number" step="0.01" name="materia_organica" value="<?php echo htmlspecialchars((string)($sol['materia_organica'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">

    <label>Observacions</label>
    <textarea name="observacions"><?php echo htmlspecialchars($sol['observacions'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>

    <button type="submit" class="btn btn-primary btn-full mt-2" id="btn-guardar-sol">Guardar canvis</button>
</form>
</div>

</div>
<script>
  const inputTipus = document.getElementById('tipus-sol');
  const avis = document.getElementById('tipus-avis');
  const btnGuardar = document.getElementById('btn-guardar-sol');
  const formSol = document.getElementById('form-sol');
  const idSol = formSol ? formSol.dataset.id : '';
  let debounceId = null;
  let controller = null;

  function normalitzaText(text) {
    return text
      .normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '')
      .toLowerCase()
      .trim();
  }

  function marcaDuplicat(existeix, nomTrobat) {
    if (existeix) {
      const nom = nomTrobat ? ` (${nomTrobat})` : '';
      avis.textContent = `Aquest tipus de sòl ja existeix${nom}.`;
      avis.style.display = 'block';
      inputTipus.setCustomValidity('Duplicat');
      btnGuardar.disabled = true;
      return;
    }
    avis.style.display = 'none';
    inputTipus.setCustomValidity('');
    btnGuardar.disabled = false;
  }

  async function comprovaDuplicat() {
    const valor = inputTipus.value;
    if (!valor.trim()) {
      marcaDuplicat(false);
      return;
    }

    if (controller) {
      controller.abort();
    }
    controller = new AbortController();

    try {
      const params = new URLSearchParams({
        tipus: valor,
        id_sol: idSol
      });
      const resp = await fetch(`../PHP/ajax_sol_tipus_check.php?${params.toString()}`, {
        signal: controller.signal
      });
      if (!resp.ok) {
        marcaDuplicat(false);
        return;
      }
      const data = await resp.json();
      const existeix = !!data.exists;
      if (existeix && data.normalized) {
        const inputNorm = normalitzaText(valor);
        if (inputNorm !== data.normalized) {
          marcaDuplicat(false);
          return;
        }
      }
      marcaDuplicat(existeix, data.match || '');
    } catch (err) {
      if (err.name !== 'AbortError') {
        marcaDuplicat(false);
      }
    }
  }

  inputTipus.addEventListener('input', () => {
    clearTimeout(debounceId);
    debounceId = setTimeout(comprovaDuplicat, 250);
  });
</script>
</body>
</html>
