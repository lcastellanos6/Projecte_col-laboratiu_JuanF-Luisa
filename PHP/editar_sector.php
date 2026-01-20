<?php
$conn = new mysqli("localhost","root","","web");
$id = intval($_GET['id'] ?? 0);

$stmt = $conn->prepare("SELECT id_sector, nom, superficie, geometria_kml, foto_url, estat_productiu FROM sector WHERE id_sector=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$sector = $res ? $res->fetch_assoc() : null;
$stmt->close();

if (!$sector) {
    echo "<p style='color:red; font-weight:bold;'>Sector no trobat.</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="ca">
<head>
<meta charset="utf-8">
<title>Editar sector</title>
<link rel="stylesheet" href="../HTML/styles.css">
</head>
<body>
<div class="page">
  <div class="page-header">
    <h1>Editar sector</h1>
  </div>

  <div class="panel">
    <form method="post" action="guardar_edicion_sector.php">
      <input type="hidden" name="id" value="<?php echo htmlspecialchars($sector['id_sector']); ?>">

      <label>Nom del sector:</label>
      <input type="text" name="nom" value="<?php echo htmlspecialchars($sector['nom'] ?? ''); ?>">

      <label>Superfície (ha):</label>
      <input type="number" step="0.01" name="superficie" value="<?php echo htmlspecialchars($sector['superficie'] ?? ''); ?>">

      <label>Geometria KML:</label>
      <textarea name="geometria_kml" rows="3" required><?php echo htmlspecialchars($sector['geometria_kml'] ?? ''); ?></textarea>

      <label>URL de la foto:</label>
      <input type="text" name="foto_url" value="<?php echo htmlspecialchars($sector['foto_url'] ?? ''); ?>">

      <label>Estat productiu:</label>
      <select name="estat_productiu">
        <?php
        $estats = ['Repos','Plantat','Productiu','Reconvertit','Abandonat'];
        foreach ($estats as $estat) {
            $selected = ($estat === ($sector['estat_productiu'] ?? 'Plantat')) ? 'selected' : '';
            echo "<option value=\"$estat\" $selected>$estat</option>";
        }
        ?>
      </select>

      <button type="submit" class="btn btn-primary btn-full mt-2">Guardar canvis</button>
      <a class="btn btn-ghost btn-full mt-2" href="consulta_parcela_sector.php">Cancel·lar</a>
    </form>
  </div>
</div>
</body>
</html>
