<?php
session_start();
require_once __DIR__ . '/../PHP/db.php';
$conn = db_connect();

$sectores = [];
$sql = "SELECT id_sector, nom FROM sector ORDER BY nom";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $sectores[] = $row;
    }
    $result->free();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Registrar Trampa</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<div class="page">
    <div class="mb-2">
        <a href="javascript:history.back()" class="btn btn-ghost">
            <i class="fa-solid fa-arrow-left"></i> Tornar
        </a>
    </div>
    <div class="page-header">
        <h2><i class="fa-solid fa-bug"></i> Registrar Nova Trampa</h2>
        <p class="page-subtitle">Afegeix una trampa de monitoratge a un sector.</p>
    </div>

    <div class="panel">
        <form action="../PHP/guardar_trampa.php" method="post">
            <div class="grid-2 mb-2">
                <div>
                    <label>Sector *</label>
                    <select name="id_sector" required>
                        <option value="">Selecciona un sector...</option>
                        <?php foreach ($sectores as $sector): ?>
                            <option value="<?= $sector['id_sector'] ?>"><?= htmlspecialchars($sector['nom']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label>Tipus de trampa *</label>
                    <select name="tipus" required>
                        <option value="Cromatogràfica">Cromatogràfica</option>
                        <option value="Feromona">Feromona</option>
                        <option value="Alimentària">Alimentària</option>
                        <option value="Llum">Llum</option>
                        <option value="Altres">Altres</option>
                    </select>
                </div>
            </div>

            <label>Model</label>
            <input type="text" name="model" placeholder="Ex: Delta, Mosquero..." class="mb-2">

            <label>Observacions</label>
            <textarea name="observacions" rows="3" placeholder="Notes addicionals..."></textarea>

            <button type="submit" class="btn btn-primary btn-full mt-3">
                <i class="fa-solid fa-save"></i> Guardar Trampa
            </button>
        </form>
    </div>
</div>
</body>
</html>
