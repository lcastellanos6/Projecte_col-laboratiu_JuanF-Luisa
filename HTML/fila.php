<?php
require_once __DIR__ . '/../PHP/db.php';
$conn = db_connect();

// Carregar plantacions amb el seu sector i varietat per identificar-les millor
$plantacions = $conn->query("
    SELECT p.id_plantacio, s.nom AS sector_nom, v.nom_comu AS varietat_nom 
    FROM plantacio p
    JOIN sector s ON p.id_sector = s.id_sector
    JOIN varietat v ON p.id_varietat = v.id_varietat
    ORDER BY s.nom, p.id_plantacio
");
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Registrar Fila</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<div class="page">
    <div class="page-header">
        <h2><i class="fa-solid fa-grip-lines"></i> Registrar nova Fila</h2>
    </div>

    <div class="panel">
        <form action="../PHP/guardar_fila.php" method="post">
            <label>Plantació:</label>
            <select name="id_increment" required>
                <option value="">-- Selecciona una plantació --</option>
                <?php while($p = $plantacions->fetch_assoc()): ?>
                    <option value="<?= $p['id_plantacio'] ?>">
                        <?= htmlspecialchars($p['sector_nom']) ?> - <?= htmlspecialchars($p['varietat_nom']) ?> (ID: <?= $p['id_plantacio'] ?>)
                    </option>
                <?php endwhile; ?>
            </select>

            <label>Número de fila:</label>
            <input type="number" name="numero_fila" required placeholder="Exemple: 1, 2, 3...">

            <div class="mt-2">
                <button type="submit" class="btn btn-primary btn-full">
                    <i class="fa-solid fa-save"></i> Guardar Fila
                </button>
                <a href="javascript:history.back()" class="btn btn-ghost btn-full mt-1">
                    <i class="fa-solid fa-arrow-left"></i> Tornar
                </a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
<?php $conn->close(); ?>
