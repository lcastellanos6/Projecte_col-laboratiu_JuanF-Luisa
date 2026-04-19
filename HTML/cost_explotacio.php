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
    <title>Registrar Cost d'Explotació</title>
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
        <h2><i class="fa-solid fa-coins"></i> Registrar Nou Cost d'Explotació</h2>
        <p class="page-subtitle">Gestió econòmica de l'explotació.</p>
    </div>

    <div class="panel">
        <form action="../PHP/guardar_cost.php" method="post">
            <div class="grid-2 mb-2">
                <div>
                    <label>Sector (opcional)</label>
                    <select name="id_sector">
                        <option value="">General (tota l'explotació)</option>
                        <?php foreach ($sectores as $sector): ?>
                            <option value="<?= $sector['id_sector'] ?>"><?= htmlspecialchars($sector['nom']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label>Data del Cost *</label>
                    <input type="date" name="data_cost" required value="<?= date('Y-m-d') ?>">
                </div>
            </div>

            <label>Concepte *</label>
            <input type="text" name="concepte" required placeholder="Ex: Reparació tractor, Compra llavor..." class="mb-2">

            <div class="grid-2 mb-2">
                <div>
                    <label>Import (€) *</label>
                    <input type="number" step="0.01" name="import" required placeholder="0.00">
                </div>
                <div>
                    <label>Categoria *</label>
                    <select name="categoria" required>
                        <option value="Ma d’obra">Ma d’obra</option>
                        <option value="Maquinària">Maquinària</option>
                        <option value="Productes">Productes</option>
                        <option value="Energia">Energia</option>
                        <option value="Aigua">Aigua</option>
                        <option value="Altres">Altres</option>
                    </select>
                </div>
            </div>

            <label>Observacions</label>
            <textarea name="observacions" rows="3" placeholder="Notes sobre la despesa..."></textarea>

            <button type="submit" class="btn btn-primary btn-full mt-3">
                <i class="fa-solid fa-save"></i> Guardar Cost
            </button>
        </form>
    </div>
</div>
</body>
</html>
