<!DOCTYPE html>
<html lang="ca">
<head>
<meta charset="UTF-8">
<title>Nou Registre</title>
<link rel="stylesheet" href="styles.css">
</head>

<body>
<div class="page">

<div class="page-header">
  <h2>Afegir Registre de Plantació</h2>
</div>

<div class="panel">
<form action="../PHP/guardar_registre.php" method="post">

    <!-- VARIETAT -->
    <label>ID Varietat *</label>
    <select name="id_varietat" required>
        <option value="">Selecciona una varietat</option>
        <?php
        $conn = new mysqli("localhost", "root", "", "web");
        if ($conn->connect_error) die("Error connexió");

        $res = $conn->query("SELECT id_varietat, nom_comu FROM varietat");
        while ($row = $res->fetch_assoc()) {
            echo "<option value='{$row['id_varietat']}'>{$row['nom_comu']}</option>";
        }
        ?>
    </select>

    <!-- PLANTACIÓ -->
    <label>ID Plantació (opcional)</label>
    <select name="id_plantacio">
        <option value="">Selecciona una plantació</option>
        <?php
        $res2 = $conn->query("
            SELECT p.id_plantacio, p.data_plantacio, v.nom_comu
            FROM plantacio p
            JOIN varietat v ON p.id_varietat = v.id_varietat
        ");
        while ($row = $res2->fetch_assoc()) {
            echo "<option value='{$row['id_plantacio']}'>
                ID {$row['id_plantacio']} - {$row['nom_comu']} ({$row['data_plantacio']})
            </option>";
        }
        ?>
    </select>

    <!-- PARCELA -->
    <label>ID Parcel·la (opcional)</label>
    <select name="id_parcela">
        <option value="">Selecciona una parcel·la</option>
        <?php
        $res3 = $conn->query("SELECT id_parcela FROM parcela");
        while ($row = $res3->fetch_assoc()) {
            echo "<option value='{$row['id_parcela']}'>Parcel·la {$row['id_parcela']}</option>";
        }
        $conn->close();
        ?>
    </select>

    <label>Data de plantació *</label>
    <input type="date" name="data_plantacio" required>

    <label>Data d'arrencada</label>
    <input type="date" name="data_arrencada">

    <label>Rendiment</label>
    <input type="number" step="0.01" name="rendiment">

    <label>Problemes fitosanitaris</label>
    <textarea name="problemes_fitosanitaris"></textarea>

    <button type="submit" class="btn btn-primary btn-full mt-2">Guardar registre</button>
</form>
</div>

</div>
</body>
</html>