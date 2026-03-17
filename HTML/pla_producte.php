<?php
$conn = new mysqli("localhost", "root", "", "web");
if ($conn->connect_error) die("Error BD");

$conn->set_charset("utf8");

// DATOS
$plans = $conn->query("SELECT id_pla, nom FROM pla_tractament");
$productes = $conn->query("SELECT id_producte, nom_comercial FROM producte");
?>

<!DOCTYPE html>
<html lang="ca">
<head>
<meta charset="UTF-8">
<title>Assignar Producte a Pla</title>
<link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="page">

<div class="page-header">
  <h2>Assignar Producte a Pla de Tractament</h2>
</div>

<div class="panel">
<form action="../PHP/guardar_pla_producte.php" method="post">

    <!-- PLA -->
    <label>ID Pla de Tractament *</label>
    <select name="id_pla" required>
        <option value="">-- Selecciona --</option>
        <?php while($p = $plans->fetch_assoc()): ?>
            <option value="<?= $p['id_pla'] ?>">
                <?= htmlspecialchars($p['nom']) ?> (ID: <?= $p['id_pla'] ?>)
            </option>
        <?php endwhile; ?>
    </select>

    <!-- PRODUCTE -->
    <label>ID Producte *</label>
    <select name="id_producte" required>
        <option value="">-- Selecciona --</option>
        <?php while($pr = $productes->fetch_assoc()): ?>
            <option value="<?= $pr['id_producte'] ?>">
                <?= htmlspecialchars($pr['nom_comercial']) ?> (ID: <?= $pr['id_producte'] ?>)
            </option>
        <?php endwhile; ?>
    </select>

    <label>Dosi *</label>
    <input type="text" name="dosi" required>

    <label>Volum de Caldo</label>
    <input type="text" name="volum_caldo">

    <button type="submit" class="btn btn-primary btn-full mt-2">Afegir Producte</button>

</form>
</div>

</div>
</body>
</html>

<?php $conn->close(); ?>
