<?php
require_once __DIR__ . '/../PHP/db.php';
$conn = db_connect();

// Carregar sectors
$sectors = $conn->query("SELECT id_sector, nom FROM sector ORDER BY nom");

// Carregar varietats
$varietats = $conn->query("SELECT id_varietat, nom_comu FROM varietat ORDER BY nom_comu");
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Registrar plantació</title>
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
        <h2><i class="fa-solid fa-seedling"></i> Registrar nova plantació</h2>
    </div>

    <div class="panel">
        <form action="../PHP/guardar_plantacio.php" method="post">
            <div class="form-grid-2">
                <div>
                    <label>Sector:</label>
                    <select name="id_sector" required>
                        <option value="">-- Selecciona un sector --</option>
                        <?php while($s = $sectors->fetch_assoc()): ?>
                            <option value="<?= $s['id_sector'] ?>"><?= htmlspecialchars($s['nom']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div>
                    <label>Varietat:</label>
                    <select name="id_varietat" required>
                        <option value="">-- Selecciona una varietat --</option>
                        <?php while($v = $varietats->fetch_assoc()): ?>
                            <option value="<?= $v['id_varietat'] ?>"><?= htmlspecialchars($v['nom_comu']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div>
                    <label>Data de plantació:</label>
                    <input type="date" name="data_plantacio" required value="<?= date('Y-m-d') ?>">
                </div>

                <div>
                    <label>Data d'inici:</label>
                    <input type="date" name="data_inici" required value="<?= date('Y-m-d') ?>">
                </div>

                <div>
                    <label>Data de fi (opcional):</label>
                    <input type="date" name="data_fi">
                </div>

                <div>
                    <label>Marc plantació (files):</label>
                    <input type="number" step="0.01" name="marc_plantacio_files" placeholder="3.00">
                </div>

                <div>
                    <label>Marc plantació (arbres):</label>
                    <input type="number" step="0.01" name="marc_plantacio_arbres" placeholder="2.00">
                </div>

                <div>
                    <label>Nombre total d'arbres:</label>
                    <input type="number" name="num_arbres_total" placeholder="500">
                </div>

                <div>
                    <label>Origen del material:</label>
                    <input type="text" name="origen_material" placeholder="Viver local">
                </div>

                <div>
                    <label>Certificacions:</label>
                    <input type="text" name="certificacions" placeholder="Certif. ECO">
                </div>

                <div>
                    <label>Sistema de formació:</label>
                    <input type="text" name="sistema_formacio" placeholder="Eix central">
                </div>

                <div>
                    <label>Inversió inicial (€):</label>
                    <input type="number" step="0.01" name="inversio_inicial">
                </div>

                <div>
                    <label>Entrada en producció prevista (any):</label>
                    <input type="number" name="entrada_produccio_prevista" min="2000" max="2100" value="<?= date('Y') + 2 ?>">
                </div>

                <div>
                    <label>Sistema de reg:</label>
                    <select name="sistema_reg">
                        <option value="Goteig">Goteig</option>
                        <option value="Aspersió">Aspersió</option>
                        <option value="Fertirrigació">Fertirrigació</option>
                        <option value="Altres">Altres</option>
                    </select>
                </div>
            </div>

            <label>Observacions:</label>
            <textarea name="observacions" rows="3"></textarea>

            <div class="mt-2">
                <button type="submit" class="btn btn-primary btn-full">
                    <i class="fa-solid fa-save"></i> Guardar Plantació
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
