<?php
$conn = new mysqli("localhost", "root", "", "web");
if ($conn->connect_error) die("Error BD");

$conn->set_charset("utf8");

// DATOS
$aplicacions = $conn->query("SELECT id_aplicacio FROM aplicacio");
$productes = $conn->query("SELECT id_producte, nom_comercial FROM producte");
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Afegir Producte a Aplicació</title>
<link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="page">

    <div class="page-header">
        <h2>Afegir Producte Utilitzat en una Aplicació</h2>
    </div>

    <div class="panel">
    <form action="../PHP/guardar_aplicacio_productes.php" method="post">

        <!-- APLICACIO -->
        <label>ID Aplicació *</label>
        <select name="id_aplicacio" required>
            <option value="">-- Selecciona --</option>
            <?php while($a = $aplicacions->fetch_assoc()): ?>
                <option value="<?= $a['id_aplicacio'] ?>">
                    Aplicació ID <?= $a['id_aplicacio'] ?>
                </option>
            <?php endwhile; ?>
        </select>

        <!-- PRODUCTE -->
        <label>ID Producte *</label>
        <select name="id_producte" required>
            <option value="">-- Selecciona --</option>
            <?php while($p = $productes->fetch_assoc()): ?>
                <option value="<?= $p['id_producte'] ?>">
                    <?= htmlspecialchars($p['nom_comercial']) ?> (ID: <?= $p['id_producte'] ?>)
                </option>
            <?php endwhile; ?>
        </select>

        <label>Quantitat aplicada *</label>
        <input type="number" step="0.001" name="quantitat" required>

        <label>Unitat *</label>
        <select name="unitat" required>
            <option value="L">L</option>
            <option value="mL">mL</option>
            <option value="kg">kg</option>
            <option value="g">g</option>
        </select>

        <label>Concentració</label>
        <input type="text" name="concentracio">

        <label>Lot / Referència</label>
        <input type="text" name="lot_referencia">

        <button type="submit" class="btn btn-primary btn-full mt-2">Guardar Producte</button>

    </form>
    </div>

</div>
</body>
</html>

<?php $conn->close(); ?>