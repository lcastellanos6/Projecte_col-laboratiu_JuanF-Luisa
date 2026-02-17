<?php
require_once __DIR__ . '/db.php';
$conn = db_connect();

$stmt = $conn->prepare("SELECT id_sol, tipus, ph, materia_organica, observacions FROM sol ORDER BY tipus");
$stmt->execute();
$res = $stmt->get_result();
$sols = [];
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $sols[] = $row;
    }
    $res->free();
}
$stmt->close();
$conn->close();

function format_num($valor): string {
    if ($valor === null || $valor === '') {
        return '-';
    }
    if (!is_numeric($valor)) {
        return htmlspecialchars((string)$valor);
    }
    return number_format((float)$valor, 2, ',', '.');
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Consulta de sòls</title>
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
    </style>
</head>
<body>

<div class="page">
  <div class="page-header">
    <div class="panel-header">
      <div>
        <h1>Consulta de sòls</h1>
        <p class="page-subtitle">Consulta, edita o elimina els tipus de sòl registrats.</p>
      </div>
      <div class="page-header-actions">
        <a class="btn btn-primary" href="../HTML/nou_sol.html">+Nou sol</a>
        <a class="btn btn-ghost" href="#" onclick="history.back(); return false;">Tornar</a>
      </div>
    </div>
  </div>

  <div class="panel mt-2">
    <?php if (!empty($sols)): ?>
      <div class="table-scroll">
        <table class="table">
          <tr>
            <th>Tipus de sòl</th>
            <th>pH</th>
            <th>Matèria orgànica (%)</th>
            <th>Observacions</th>
            <th>Accions</th>
          </tr>
          <?php foreach ($sols as $sol): ?>
            <tr>
              <td><?php echo htmlspecialchars($sol['tipus'] ?? ''); ?></td>
              <td><?php echo format_num($sol['ph'] ?? null); ?></td>
              <td><?php echo format_num($sol['materia_organica'] ?? null); ?></td>
              <td><?php echo htmlspecialchars($sol['observacions'] ?? ''); ?></td>
              <td>
                <div class="table-actions">
                  <a href="editar_sol.php?id=<?php echo (int)$sol['id_sol']; ?>" title="Editar sòl">
                    <i class="fa-solid fa-pen-to-square" aria-hidden="true"></i>
                  </a>
                  <a href="eliminar_sol.php?id=<?php echo (int)$sol['id_sol']; ?>" onclick="return confirm('Segur que vols eliminar aquest sòl?');" title="Eliminar sòl">
                    <i class="fa-solid fa-trash" aria-hidden="true"></i>
                  </a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </table>
      </div>
    <?php else: ?>
      <p>No hi ha tipus de sòl registrats.</p>
    <?php endif; ?>
  </div>
</div>

</body>
</html>
