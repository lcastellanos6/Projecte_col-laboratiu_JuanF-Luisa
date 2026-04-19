<?php
require_once __DIR__ . '/../PHP/db.php';
$conn = db_connect();

// Carregar parcel·les
$parceles = $conn->query("SELECT id_parcela, nom FROM parcela ORDER BY nom");

// Carregar varietats
$varietats = $conn->query("SELECT id_varietat, nom_comu FROM varietat ORDER BY nom_comu");
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Afegir Previsió de Producció</title>
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
        <h2><i class="fa-solid fa-chart-line"></i> Registrar Previsió de Producció</h2>
    </div>

    <div class="panel">
        <form action="../PHP/guardar_previsio_collita.php" method="post">
            <div class="form-grid-2">
                <div>
                    <label>Parcel·la *</label>
                    <select name="id_parcela" required>
                        <option value="">-- Selecciona una parcel·la --</option>
                        <?php while($p = $parceles->fetch_assoc()): ?>
                            <option value="<?= $p['id_parcela'] ?>"><?= htmlspecialchars($p['nom']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div>
                    <label>Varietat *</label>
                    <select name="id_varietat" required>
                        <option value="">-- Selecciona una varietat --</option>
                        <?php while($v = $varietats->fetch_assoc()): ?>
                            <option value="<?= $v['id_varietat'] ?>"><?= htmlspecialchars($v['nom_comu']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div>
                    <label>Campanya Any *</label>
                    <input type="number" name="campanya_any" min="2000" max="2100" value="<?= date('Y') ?>" required>
                </div>

                <div>
                    <label>Estimació Producció</label>
                    <input type="number" step="0.01" name="estimacio_produccio" placeholder="Ex: 1500.00">
                </div>

                <div>
                    <label>Unitat *</label>
                    <select name="unitat" required>
                        <option value="kg">Kg</option>
                        <option value="Tn">Tn</option>
                    </select>
                </div>

                <div>
                    <label>Data Estimada de Collita</label>
                    <input type="date" name="data_estimada_collita">
                </div>
            </div>

            <label>Model Predictiu</label>
            <input type="text" name="model_predictiu" placeholder="Ex: Rendiment_hist + clima">

            <div class="mt-2">
                <button type="submit" class="btn btn-primary btn-full">
                    <i class="fa-solid fa-save"></i> Guardar Previsió
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
