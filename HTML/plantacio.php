<!DOCTYPE html>
<html lang="ca">
<head>
<meta charset="UTF-8">
<title>Nova Plantació</title>
<link rel="stylesheet" href="styles.css">
</head>

<body>
<div class="page">

<div class="page-header">
  <h2>Afegir Plantació</h2>
</div>

<div class="panel">
<form action="../PHP/guardar_plantacio.php" method="post">

    <!-- SECTOR -->
    <label>Sector *</label>
    <select name="id_sector" required>
        <option value="">Selecciona un sector</option>
        <?php
        $conn = new mysqli("localhost", "root", "", "web");
        if ($conn->connect_error) die("Error connexió");

        $res = $conn->query("SELECT id_sector, nom FROM sector");
        while ($row = $res->fetch_assoc()) {
            echo "<option value='{$row['id_sector']}'>{$row['nom']}</option>";
        }
        ?>
    </select>

    <!-- VARIETAT -->
    <label>Varietat *</label>
    <select name="id_varietat" required>
        <option value="">Selecciona una varietat</option>
        <?php
        $res2 = $conn->query("SELECT id_varietat, nom_comu FROM varietat");
        while ($row = $res2->fetch_assoc()) {
            echo "<option value='{$row['id_varietat']}'>{$row['nom_comu']}</option>";
        }
        ?>
    </select>

    <?php $conn->close(); ?>

    <label>Data de plantació *</label>
    <input type="date" name="data_plantacio" required>

    <label>Data inici *</label>
    <input type="date" name="data_inici" required>

    <label>Data fi</label>
    <input type="date" name="data_fi">

    <label>Marc files</label>
    <input type="number" step="0.01" name="marc_plantacio_files">

    <label>Marc arbres</label>
    <input type="number" step="0.01" name="marc_plantacio_arbres">

    <label>Número arbres</label>
    <input type="number" name="num_arbres_total">

    <label>Origen material</label>
    <input type="text" name="origen_material">

    <label>Certificacions</label>
    <input type="text" name="certificacions">

    <label>Sistema formació</label>
    <input type="text" name="sistema_formacio">

    <label>Inversió inicial</label>
    <input type="number" step="0.01" name="inversio_inicial">

    <label>Entrada producció (any)</label>
    <input type="number" name="entrada_produccio_prevista">

    <label>Sistema reg</label>
    <select name="sistema_reg">
        <option value="Goteig">Goteig</option>
        <option value="Aspersió">Aspersió</option>
        <option value="Fertirrigació">Fertirrigació</option>
        <option value="Altre">Altre</option>
    </select>

    <label>Observacions</label>
    <textarea name="observacions"></textarea>

    <button type="submit" class="btn btn-primary btn-full mt-2">
        Guardar plantació
    </button>

</form>
</div>

</div>
</body>
</html>