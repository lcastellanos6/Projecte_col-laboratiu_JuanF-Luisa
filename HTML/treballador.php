<?php
session_start();
require_once __DIR__ . '/../PHP/db.php';
$conn = db_connect();

// Restricció de rol: només administradors poden registrar treballadors
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    die("Accés denegat. Només els administradors poden registrar nous treballadors.");
}

// Carregar posicions (la columna a la BD es diu 'nom')
$posicions = $conn->query("SELECT id_posicio, nom as nom_posicio FROM posicio ORDER BY nom");

// Carregar calendaris model
$calendaris = $conn->query("SELECT id_calendari_model, nom FROM calendari_model ORDER BY nom");

// Carregar horaris model
$horaris = $conn->query("SELECT id_horari_model, nom FROM horari_model ORDER BY nom");
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Registrar Treballador</title>
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
        <div class="flex justify-between items-center">
            <div>
                <h2><i class="fa-solid fa-user-plus"></i> Registrar nou treballador</h2>
                <p class="page-subtitle">Introdueix les dades bàsiques del treballador.</p>
            </div>
            <a href="../PHP/consulta_treballadors.php" class="btn btn-ghost">
                <i class="fa-solid fa-users"></i> Veure llistat
            </a>
        </div>
    </div>

    <div class="panel">
        <form action="../PHP/guardar_treballador.php" method="post" enctype="multipart/form-data">
            <div class="form-grid-2">
                <div>
                    <label>Nom complet *</label>
                    <input type="text" name="nom_complet" required placeholder="Nom i cognoms">
                </div>

                <div>
                    <label>Fotografia (opcional):</label>
                    <input type="file" name="fotografia" accept="image/*">
                </div>

                <div>
                    <label>Document d'identitat / Passaport *</label>
                    <input type="text" name="document_identitat" required placeholder="DNI/NIE/Passaport">
                </div>

                <div>
                    <label>Número de la Seguretat Social:</label>
                    <input type="text" name="num_seguretat_social">
                </div>

                <div>
                    <label>Data de naixement:</label>
                    <input type="date" name="data_naixement">
                </div>

                <div>
                    <label>Lloc de naixement:</label>
                    <input type="text" name="lloc_naixement">
                </div>

                <div>
                    <label>Nacionalitat:</label>
                    <input type="text" name="nacionalitat">
                </div>

                <div>
                    <label>Situacio de Residència:</label>
                    <input type="text" name="residencia">
                </div>

                <div>
                    <label>Telèfon:</label>
                    <input type="text" name="telefon">
                </div>

                <div>
                    <label>Email:</label>
                    <input type="email" name="email">
                </div>

                <div>
                    <label>Adreça:</label>
                    <input type="text" name="adreca">
                </div>

                <div>
                    <label>Contacte d'emergència:</label>
                    <input type="text" name="contacte_emergencia">
                </div>

                <div>
                    <label>Telèfon d'emergència:</label>
                    <input type="text" name="telefon_emergencia">
                </div>

                <div>
                    <label>Compte bancari (IBAN):</label>
                    <input type="text" name="compte_bancari">
                </div>

                <div>
                    <label>Posició / Rol *</label>
                    <select name="id_posicio" required>
                        <option value="">Selecciona una posició...</option>
                        <?php while ($p = $posicions->fetch_assoc()): ?>
                            <option value="<?= $p['id_posicio'] ?>"><?= htmlspecialchars($p['nom_posicio']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div>
                    <label>Model de calendari *</label>
                    <select name="id_calendari_model" required>
                        <option value="">Selecciona un calendari...</option>
                        <?php while ($c = $calendaris->fetch_assoc()): ?>
                            <option value="<?= $c['id_calendari_model'] ?>"><?= htmlspecialchars($c['nom']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div>
                    <label>Model d'horari *</label>
                    <select name="id_horari_model" required>
                        <option value="">Selecciona un horari...</option>
                        <?php while ($h = $horaris->fetch_assoc()): ?>
                            <option value="<?= $h['id_horari_model'] ?>"><?= htmlspecialchars($h['nom']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>

            <div class="mt-2">
                <label class="flex items-center gap-1">
                    <input type="checkbox" name="consentiment_rgpd" value="1">
                    Consentiment RGPD (El treballador accepta el tractament de les seves dades)
                </label>
            </div>

            <button type="submit" class="btn btn-primary btn-full mt-2">
                <i class="fa-solid fa-save"></i> Guardar treballador
            </button>
        </form>
    </div>
</div>
</body>
</html>
<?php $conn->close(); ?>
