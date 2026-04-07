<!DOCTYPE html>
<html lang="ca">
<head>
<meta charset="UTF-8">
<title>Registrar Fila</title>
<link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="page">

<div class="page-header">
  <h1>Registrar nova Fila</h1>
</div>

<div class="panel">
<form action="../PHP/guardar_fila.php" method="post">

    <!-- PLANTACIÓ -->
    <label>Plantació *</label>
    <select name="id_plantacio" required>
        <option value="">Selecciona una plantació</option>
        <?php
        $conn = new mysqli("localhost", "root", "", "web");
        if ($conn->connect_error) die("Error connexió");

        $result = $conn->query("
            SELECT p.id_plantacio, p.data_plantacio, v.nom_comu
            FROM plantacio p
            JOIN varietat v ON p.id_varietat = v.id_varietat
            ORDER BY p.id_plantacio DESC
        ");

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<option value='{$row['id_plantacio']}'>
                    ID {$row['id_plantacio']} - {$row['nom_comu']} ({$row['data_plantacio']})
                </option>";
            }
        } else {
            echo "<option value=''>No hi ha plantacions</option>";
        }

        $conn->close();
        ?>
    </select>

    <!-- NUMERO FILA -->
    <label>Número de fila *</label>
    <input type="number" name="numero_fila" required placeholder="Número de la fila">

    <button type="submit" class="btn btn-primary btn-full mt-2">
        Guardar Fila
    </button>

</form>
</div>

</div>
</body>
</html>
