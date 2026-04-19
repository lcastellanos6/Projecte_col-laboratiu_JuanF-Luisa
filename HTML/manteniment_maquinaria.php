<?php
session_start();
require_once __DIR__ . '/../PHP/db.php';
$conn = db_connect();

$equips = [];
$sql = "SELECT id_equip, tipus FROM equip ORDER BY tipus";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $equips[] = $row;
    }
    $result->free();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Manteniment Maquinària</title>
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
        <h2><i class="fa-solid fa-screwdriver-wrench"></i> Registrar Manteniment de Maquinària</h2>
        <p class="page-subtitle">Control de revisions, avaries i reparacions.</p>
    </div>

    <div class="panel">
        <form action="../PHP/guardar_manteniment.php" method="post">
            <div class="grid-2 mb-2">
                <div>
                    <label>Equip / Maquinària *</label>
                    <select name="id_equip" required>
                        <option value="">Selecciona un equip...</option>
                        <?php foreach ($equips as $equip): ?>
                            <option value="<?= $equip['id_equip'] ?>">
                                <?= htmlspecialchars($equip['tipus']) ?> (ID: <?= $equip['id_equip'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label>Data de Manteniment *</label>
                    <input type="date" name="data_manteniment" required value="<?= date('Y-m-d') ?>">
                </div>
            </div>

            <label>Tipus de Manteniment *</label>
            <select name="tipus" required class="mb-2">
                <option value="Preventiu">Preventiu</option>
                <option value="Avaria">Avaria</option>
                <option value="Reparació">Reparació</option>
                <option value="Altre">Altre</option>
            </select>

            <div class="grid-2 mb-2">
                <div>
                    <label>Hores de funcionament</label>
                    <input type="number" step="0.1" name="hores_funcionament" placeholder="0.0">
                </div>
                <div>
                    <label>Cost (€)</label>
                    <input type="number" step="0.01" name="cost" placeholder="0.00">
                </div>
            </div>

            <label>Descripció del treball realitzat *</label>
            <textarea name="descripcio" rows="4" required placeholder="Detalla les operacions realitzades..."></textarea>

            <label class="mt-2">Pròxim manteniment previst</label>
            <input type="date" name="proxim_manteniment" class="mb-2">

            <button type="submit" class="btn btn-primary btn-full mt-3">
                <i class="fa-solid fa-save"></i> Guardar Manteniment
            </button>
        </form>
    </div>
</div>
</body>
</html>
