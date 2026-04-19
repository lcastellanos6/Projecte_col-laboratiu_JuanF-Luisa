<?php
require_once __DIR__ . '/../PHP/db.php';
$conn = db_connect();

// Carregar espècies
$especies = $conn->query("SELECT id_especie, nom_comu FROM especie ORDER BY nom_comu");
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Registrar Varietat</title>
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
        <h2><i class="fa-solid fa-leaf"></i> Registrar nova varietat</h2>
    </div>

    <div class="panel">
        <form action="../PHP/guardar_varietat.php" method="post">
            <div class="form-grid-2">
                <div>
                    <label>Espècie *</label>
                    <select name="id_especie" required>
                        <option value="">-- Selecciona una espècie --</option>
                        <?php while($e = $especies->fetch_assoc()): ?>
                            <option value="<?= $e['id_especie'] ?>"><?= htmlspecialchars($e['nom_comu']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div>
                    <label>Nom comú *</label>
                    <input type="text" name="nom_comu" required placeholder="Ex: Golden Delicious">
                </div>

                <div>
                    <label>Nom científic de la varietat *</label>
                    <input type="text" name="nom_cientific" required placeholder="Ex: Malus domestica 'Golden'">
                </div>

                <div>
                    <label>Productivitat mitjana (kg/arbre):</label>
                    <input type="number" step="0.01" name="productivitat_mitjana" placeholder="Ex: 45.50">
                </div>
            </div>

            <label>Característiques agronòmiques:</label>
            <textarea name="caracteristiques_agronomiques" rows="2"></textarea>

            <label>Cicle vegetatiu:</label>
            <textarea name="cicle_vegetatiu" rows="2"></textarea>

            <label>Requisits de pol·linització:</label>
            <textarea name="requisits_pollinitzacio" rows="2"></textarea>

            <label>Qualitats organolèptiques:</label>
            <textarea name="qualitats_organoleptiques" rows="2"></textarea>

            <div class="mt-2">
                <button type="submit" class="btn btn-primary btn-full">
                    <i class="fa-solid fa-save"></i> Guardar varietat
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
