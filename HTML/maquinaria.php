<?php
session_start();
require_once __DIR__ . '/../PHP/db.php';
$conn = db_connect();

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    die("Accés denegat.");
}

$maquinaria = $conn->query("SELECT * FROM equip ORDER BY id_equip DESC LIMIT 10");
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Afegir Maquinària</title>
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
  <h2><i class="fa-solid fa-tractor"></i> Registrar Maquinària</h2>
</div>

<div class="panel">
<form action="../PHP/guardar_maquinaria.php" method="post">
    <div class="mb-1">
        <label>Tipus de maquinària *</label>
        <input type="text" name="tipus" required class="w-full" placeholder="Ex: Tractor, Atomitzador...">
    </div>
    <div class="mb-2">
        <label>Descripció</label>
        <textarea name="descripcio" class="w-full" rows="3"></textarea>
    </div>
    <button type="submit" class="btn btn-primary btn-full">Guardar Maquinària</button>
</form>
</div>

<div class="panel mt-3">
    <h2 class="panel-title"><i class="fa-solid fa-list"></i> Maquinària registrada</h2>
    <?php if ($maquinaria->num_rows === 0): ?>
        <p class="page-subtitle">No hi ha maquinària registrada.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Tipus</th>
                    <th>Descripció</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($m = $maquinaria->fetch_assoc()): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($m['tipus']) ?></strong></td>
                    <td><?= htmlspecialchars($m['descripcio'] ?? '—') ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</div>
</body>
</html>
<?php $conn->close(); ?>
