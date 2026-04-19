<?php
session_start();
require_once __DIR__ . '/../PHP/db.php';
$conn = db_connect();

// Restricció de rol: només administradors poden registrar lliuraments
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    die("Accés denegat. Només els administradors poden registrar lliuraments d'EPI.");
}

// Carregar treballadors
$treballadors = $conn->query("SELECT id_treballador, nom_complet FROM treballador ORDER BY nom_complet");

// Carregar tipus d'EPI
$tipus_epi = $conn->query("SELECT id_epi_tipus, nom, stock_actual FROM epi_tipus ORDER BY nom");
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Lliurament d'EPI - SIGA</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="page">
    <div class="mb-2">
        <a href="javascript:history.back()" class="btn btn-ghost">
            <i class="fa-solid fa-arrow-left"></i> Tornar
        </a>
    </div>

    <div class="page-header">
        <div class="flex justify-between items-center">
            <div>
                <h2><i class="fa-solid fa-vest"></i> Lliurament d'EPI</h2>
                <p class="page-subtitle">Registra l'entrega d'Equips de Protecció Individual al personal.</p>
            </div>
        </div>
    </div>

    <div class="panel">
        <form action="../PHP/guardar_epis.php" method="post" enctype="multipart/form-data">
            <div class="form-grid-2">
                <div>
                    <label>Treballador *</label>
                    <select name="id_treballador" required>
                        <option value="">Selecciona un treballador...</option>
                        <?php while ($t = $treballadors->fetch_assoc()): ?>
                            <option value="<?= $t['id_treballador'] ?>"><?= htmlspecialchars($t['nom_complet']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div>
                    <label>Equip de Protecció (EPI) *</label>
                    <select name="id_epi_tipus" required>
                        <option value="">Selecciona el material...</option>
                        <?php while ($e = $tipus_epi->fetch_assoc()): ?>
                            <option value="<?= $e['id_epi_tipus'] ?>"><?= htmlspecialchars($e['nom']) ?> (Stock: <?= $e['stock_actual'] ?>)</option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div>
                    <label>Data de lliurament *</label>
                    <input type="date" name="data_lliurament" required value="<?= date('Y-m-d') ?>">
                </div>

                <div>
                    <label>Data de caducitat / revisió (si en té)</label>
                    <input type="date" name="data_caducitat">
                </div>

                <div>
                    <label>Quantitat entregada *</label>
                    <input type="number" name="quantitat" required value="1" min="1">
                </div>

                <div>
                    <label>Talla (opcional)</label>
                    <input type="text" name="talla" placeholder="Ex: M, 42, L...">
                </div>

                <div>
                    <label>Document signat (opcional)</label>
                    <input type="file" name="document_signat_url">
                </div>
            </div>

            <div class="mt-2">
                <label>Observacions</label>
                <textarea name="observacions" rows="3" placeholder="Notes sobre el lliurament..."></textarea>
            </div>

            <button type="submit" class="btn btn-primary btn-full mt-2">
                <i class="fa-solid fa-save"></i> Registrar Lliurament
            </button>
        </form>
    </div>
</body>
</html>
<?php $conn->close(); ?>
