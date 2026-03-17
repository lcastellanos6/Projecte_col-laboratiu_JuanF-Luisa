<?php
$conn = new mysqli("localhost", "root", "", "web");
if ($conn->connect_error) die("Error BD");

$conn->set_charset("utf8");

// CARGAR DATOS
$plans = $conn->query("SELECT id_pla, nom FROM pla_tractament");
$operaris = $conn->query("SELECT id_treballador, nom_complet FROM treballador");
$equips = $conn->query("SELECT id_equip, tipus FROM equip"); // CORREGIDO
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Registrar Aplicació</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="page">
  <div class="page-header">
    <h1>Registre d'aplicació de tractament</h1>
    <p class="page-subtitle">Desa les dades de cada aplicació.</p>
  </div>

  <div class="panel">
    <form action="../PHP/guardar_aplicacio.php" method="post">

        <!-- PLA -->
        <label>ID Pla de Tractament</label>
        <select name="id_pla">
            <option value="">-- Selecciona --</option>
            <?php while($p = $plans->fetch_assoc()): ?>
                <option value="<?= $p['id_pla'] ?>">
                    <?= htmlspecialchars($p['nom']) ?> (ID: <?= $p['id_pla'] ?>)
                </option>
            <?php endwhile; ?>
        </select>

        <label>Data *</label>
        <input type="date" name="data" required>

        <label>Hora inici</label>
        <input type="time" name="hora_inici">

        <label>Hora fi</label>
        <input type="time" name="hora_fi">

        <label>Mètode</label>
        <select name="metode">
            <option value="">-- Selecciona --</option>
            <option value="Fertirrigacio">Fertirrigació</option>
            <option value="Foliar">Foliar</option>
            <option value="Sòl">Sòl</option>
            <option value="Altres">Altres</option>
        </select>

        <!-- OPERARI -->
        <label>ID Operari</label>
        <select name="id_operari">
            <option value="">-- Selecciona --</option>
            <?php while($o = $operaris->fetch_assoc()): ?>
                <option value="<?= $o['id_treballador'] ?>">
                    <?= htmlspecialchars($o['nom_complet']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <!-- EQUIP -->
        <label>ID Equip</label>
        <select name="id_equip">
            <option value="">-- Selecciona --</option>
            <?php while($e = $equips->fetch_assoc()): ?>
                <option value="<?= $e['id_equip'] ?>">
                    <?= htmlspecialchars($e['tipus']) ?> (ID: <?= $e['id_equip'] ?>)
                </option>
            <?php endwhile; ?>
        </select>

        <label>Condicions Ambientals</label>
        <textarea name="condicions_ambientals"></textarea>

        <label>Observacions</label>
        <textarea name="observacions"></textarea>

        <button type="submit" class="btn btn-primary btn-full mt-2">Guardar</button>

    </form>
  </div>
</div>
</body>
</html>

<?php $conn->close(); ?>