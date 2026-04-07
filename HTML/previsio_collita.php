<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Afegir Previsió de Producció</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="page">

<div class="page-header">
  <h2>Registrar Previsió de Producció</h2>
</div>

<div class="panel">
<form action="../PHP/guardar_previsio_collita.php" method="post">

    <!-- PARCELA -->
    <label>Parcel·la *</label>
    <select name="id_parcela" required>
        <option value="">Selecciona una parcel·la</option>
        <?php
        $conn = new mysqli("localhost", "root", "", "web");
        if ($conn->connect_error) die("Error connexió");

        $res = $conn->query("
            SELECT id_parcela, nom
            FROM parcela
        ");

        if ($res && $res->num_rows > 0) {
            while ($row = $res->fetch_assoc()) {
                echo "<option value='{$row['id_parcela']}'>
                    {$row['nom']} (ID {$row['id_parcela']})
                </option>";
            }
        } else {
            echo "<option value=''>No hi ha parcel·les</option>";
        }
        ?>
    </select>

    <!-- VARIETAT -->
    <label>Varietat *</label>
    <select name="id_varietat" required>
        <option value="">Selecciona una varietat</option>
        <?php
        $res2 = $conn->query("
            SELECT id_varietat, nom_comu
            FROM varietat
        ");

        if ($res2 && $res2->num_rows > 0) {
            while ($row = $res2->fetch_assoc()) {
                echo "<option value='{$row['id_varietat']}'>
                    {$row['nom_comu']}
                </option>";
            }
        } else {
            echo "<option value=''>No hi ha varietats</option>";
        }

        $conn->close();
        ?>
    </select>

    <label>Campanya Any *</label>
    <input type="number" name="campanya_any" min="2000" max="2100" required>

    <label>Estimació Producció</label>
    <input type="number" step="0.01" name="estimacio_produccio">

    <label>Unitat *</label>
    <select name="unitat" required>
        <option value="kg">Kg</option>
        <option value="Tn">Tn</option>
    </select>

    <label>Data Estimada de Collita</label>
    <input type="date" name="data_estimada_collita">

    <label>Model Predictiu</label>
    <input type="text" name="model_predictiu">

    <button type="submit" class="btn btn-primary btn-full mt-2">
        Guardar Previsió
    </button>

</form>
</div>

</div>
</body>
</html>
