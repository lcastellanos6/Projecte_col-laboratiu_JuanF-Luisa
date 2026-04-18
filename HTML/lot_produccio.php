<?php
require_once __DIR__ . '/../PHP/db.php';

$conn = db_connect();

$collites = [];
$res = $conn->query("
  SELECT c.collita_id, c.data_inici, s.nom AS sector
  FROM collita c
  JOIN plantacio pl ON pl.id_plantacio = c.plantacio_id
  JOIN sector s ON s.id_sector = pl.id_sector
  ORDER BY c.data_inici DESC
");
if ($res) {
    while ($r = $res->fetch_assoc()) {
        $collites[] = $r;
    }
    $res->free();
}

$clients = [];
$resC = $conn->query("SELECT id_client, nom FROM desti_client ORDER BY nom");
if ($resC) {
    while ($r = $resC->fetch_assoc()) {
        $clients[] = $r;
    }
    $resC->free();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="ca">
<head>
  <meta charset="UTF-8">
  <title>Registrar Nou Lot</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="page">
  <div class="page-header">
    <h2>Registrar Nou Lot</h2>
  </div>

  <div class="panel">
    <form action="../PHP/guardar_lot_produccio.php" method="post">
      <label>Collita *</label>
      <select name="collita_id" required>
        <option value="">Selecciona una collita</option>
        <?php foreach ($collites as $c): ?>
          <option value="<?php echo (int)$c['collita_id']; ?>">
            #<?php echo (int)$c['collita_id']; ?> · <?php echo htmlspecialchars(($c['data_inici'] ?? '') . ' · ' . ($c['sector'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
          </option>
        <?php endforeach; ?>
      </select>

      <label>Codi Lot *</label>
      <input type="text" name="codi_lot" required placeholder="Ex: LOT-2026-0001">

      <label>Data de Creació *</label>
      <input type="date" name="data_creacio" required>

      <label>Quantitat *</label>
      <input type="number" step="0.01" name="quantitat" required>

      <label>Unitat *</label>
      <select name="unitat" required>
        <option value="kg">Kg</option>
        <option value="caixa">Caixa</option>
        <option value="bin">Bin</option>
      </select>

      <label>Qualitat</label>
      <input type="text" name="qualitat">

      <label>Estat *</label>
      <select name="estat" required>
        <option value="Emmagatzemat">Emmagatzemat</option>
        <option value="En transport">En transport</option>
        <option value="Venut">Venut</option>
      </select>

      <label>Client / destí</label>
      <select name="id_client">
        <option value="">-- Sense assignar --</option>
        <?php foreach ($clients as $cl): ?>
          <option value="<?php echo (int)$cl['id_client']; ?>">
            <?php echo htmlspecialchars($cl['nom'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
          </option>
        <?php endforeach; ?>
      </select>

      <label>URL del QR</label>
      <input type="text" name="qr_url" placeholder="(opcional)">

      <button type="submit" class="btn btn-primary btn-full mt-2">Guardar Lot</button>
    </form>
  </div>
</div>
</body>
</html>

