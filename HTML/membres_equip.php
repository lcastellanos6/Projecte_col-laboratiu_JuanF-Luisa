<?php
require_once __DIR__ . '/../PHP/db.php';
$conn = db_connect();

// Carregar equips
$equips = $conn->query("SELECT id_equip, tipus FROM equip ORDER BY tipus");

// Carregar treballadors
$treballadors = $conn->query("SELECT id_treballador, nom_complet FROM treballador ORDER BY nom_complet");
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Assignar Treballador a Equip</title>
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
        <h2><i class="fa-solid fa-users-gear"></i> Assignar Treballador a Equip</h2>
    </div>

    <div class="panel">
        <form action="../PHP/guardar_membres.php" method="POST">
            <div class="form-grid-2">
                <div>
                    <label for="id_equip">Equip</label>
                    <select name="id_equip" required>
                        <option value="">-- Selecciona un equip --</option>
                        <?php while($e = $equips->fetch_assoc()): ?>
                            <option value="<?= $e['id_equip'] ?>"><?= htmlspecialchars($e['tipus']) ?> (ID: <?= $e['id_equip'] ?>)</option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div>
                    <label for="id_treballador">Treballador</label>
                    <select name="id_treballador" required>
                        <option value="">-- Selecciona un treballador --</option>
                        <?php while($t = $treballadors->fetch_assoc()): ?>
                            <option value="<?= $t['id_treballador'] ?>"><?= htmlspecialchars($t['nom_complet']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div>
                    <label for="rol_equip">Rol a l'equip</label>
                    <input type="text" name="rol_equip" placeholder="Ex: Cap de plantació, Operari, etc.">
                </div>

                <div>
                    <label for="data_alta">Data d'alta</label>
                    <input type="date" name="data_alta" value="<?= date('Y-m-d') ?>">
                </div>

                <div>
                    <label for="data_baixa">Data de baixa</label>
                    <input type="date" name="data_baixa">
                </div>
            </div>

            <div class="mt-2">
                <button type="submit" class="btn btn-primary btn-full">
                    <i class="fa-solid fa-user-plus"></i> Assignar Treballador
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
