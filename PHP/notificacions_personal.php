<?php
require_once __DIR__ . '/personal_alerts.php';

$dies = filter_input(INPUT_GET, 'dies', FILTER_VALIDATE_INT);
$dies = ($dies && $dies > 0) ? $dies : 30;

$result = get_personal_alerts($dies);
$alerts = $result['alerts'];
$available = (bool) $result['available'];
$error = (string) $result['error'];
?>
<!DOCTYPE html>
<html lang="ca">
<head>
  <meta charset="UTF-8">
  <title>Alertes de personal</title>
  <link rel="stylesheet" href="../HTML/styles.css">
</head>
<body>
<div class="page">
  <div class="page-header">
    <h1>Alertes de personal</h1>
    <p class="page-subtitle">Contractes, certificacions i alertes pendents (finestra: <?php echo (int)$dies; ?> dies).</p>
  </div>

  <div class="panel">
    <form method="get" class="form-grid-2">
      <label>Finestra d'alerta (dies)</label>
      <input type="number" name="dies" value="<?php echo (int) $dies; ?>" min="1" max="365">
      <button type="submit" class="btn btn-primary mt-2">Recalcular</button>
    </form>

    <?php if (!$available): ?>
      <p class="alert err">No es poden calcular les alertes. <?php echo htmlspecialchars($error); ?></p>
    <?php elseif (empty($alerts)): ?>
      <p class="alert ok">No hi ha alertes de personal dins la finestra seleccionada.</p>
    <?php else: ?>
      <div class="table-scroll mt-2">
        <table class="table">
          <thead>
          <tr>
            <th>Data</th>
            <th>Tipus</th>
            <th>Treballador</th>
            <th>Detall</th>
            <th>Estat</th>
            <th>Accions</th>
          </tr>
          </thead>
          <tbody>
          <?php foreach ($alerts as $a): ?>
            <tr>
              <td><?php echo htmlspecialchars($a['data_avis']); ?></td>
              <td><?php echo htmlspecialchars($a['tipus_alerta']); ?></td>
              <td>
                <a href="perfil_treballador.php?id=<?= $a['id_treballador'] ?>" class="text-primary font-bold">
                  <?php echo htmlspecialchars($a['nom_complet']); ?>
                </a>
              </td>
              <td>
                <strong><?php echo htmlspecialchars($a['titol']); ?></strong><br>
                <small><?php echo htmlspecialchars($a['detall']); ?></small>
              </td>
              <td>
                <?php if (!empty($a['is_overdue'])): ?>
                  <span class="chip chip-bad">Caducada / vençuda</span>
                <?php else: ?>
                  <span class="chip chip-warn">Pendent</span>
                <?php endif; ?>
              </td>
              <td>
                <div class="flex gap-1">
                  <a href="perfil_treballador.php?id=<?= $a['id_treballador'] ?>" class="btn btn-ghost btn-sm" title="Veure perfil">
                    <i class="fa-solid fa-eye"></i> Veure
                  </a>
                  <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
                    <a href="editar_treballador.php?id=<?= $a['id_treballador'] ?>" class="btn btn-ghost btn-sm" title="Gestionar">
                      <i class="fa-solid fa-user-gear"></i> Gestionar
                    </a>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>

    <a class="btn btn-ghost mt-2" href="../HTML/index.php">Tornar al dashboard</a>
  </div>
</div>
</body>
</html>
