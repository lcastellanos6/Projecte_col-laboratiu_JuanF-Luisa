<?php
session_start();
require_once __DIR__ . '/../PHP/db.php';
$conn = db_connect();

// Restricció de rol: només administradors poden registrar formacions
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    die("Accés denegat. Només els administradors poden registrar noves formacions.");
}

// Carregar treballadors
$treballadors = $conn->query("SELECT id_treballador, nom_complet FROM treballador ORDER BY nom_complet");

// Carregar tipus de formacions/certificacions
$tipus_formacions = $conn->query("SELECT id_formacio_cert, nom FROM formacio_certificacio ORDER BY nom");
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Registrar Formació/Certificació - SIGA</title>
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
                <h2><i class="fa-solid fa-certificate"></i> Registrar Formació / Certificació</h2>
                <p class="page-subtitle">Afegeix una nova competència o certificat a un treballador.</p>
            </div>
        </div>
    </div>

    <div class="panel">
        <form action="../PHP/guardar_certificacio.php" method="post" enctype="multipart/form-data">
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
                    <label>Tipus de Formació / Certificació *</label>
                    <select name="id_formacio_cert" required>
                        <option value="">Selecciona el títol...</option>
                        <?php while ($f = $tipus_formacions->fetch_assoc()): ?>
                            <option value="<?= $f['id_formacio_cert'] ?>"><?= htmlspecialchars($f['nom']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div>
                    <label>Data d'expedició / inici *</label>
                    <input type="date" name="data_inici" required>
                </div>

                <div>
                    <label>Data de caducitat (si en té)</label>
                    <input type="date" name="data_caducitat">
                </div>

                <div>
                    <label>Hores de formació</label>
                    <input type="number" step="0.5" name="hores" placeholder="Ex: 8.5">
                </div>

                <div>
                    <label>Document (PDF/JPG)</label>
                    <input type="file" name="document_url">
                </div>
            </div>

            <div class="mt-2">
                <label>Observacions o detalls addicionals</label>
                <textarea name="observacions" rows="3" placeholder="Detalla habilitats adquirides o notes rellevants..."></textarea>
            </div>

            <button type="submit" class="btn btn-primary btn-full mt-2">
                <i class="fa-solid fa-save"></i> Registrar Certificació
            </button>
        </form>
    </div>
</body>
</html>
<?php $conn->close(); ?>
