<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Afegir Control de Qualitat</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="page">

<div class="page-header">
  <h2>Registrar Control de Qualitat</h2>
</div>

<div class="panel">
<form action="../PHP/guardar_control_qualitat.php" method="post">

    <!-- LOT PRODUCCIO -->
    <label>ID Lot *</label>
    <select name="lot_id" required>
        <option value="">Selecciona un lot</option>
        <?php
        $conn = new mysqli("localhost", "root", "", "web");
        if ($conn->connect_error) die("Error connexió");

        $result = $conn->query("
            SELECT lot_id, codi_lot, data_creacio, qualitat
            FROM lot_produccio
            ORDER BY lot_id DESC
        ");

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<option value='{$row['lot_id']}'>
                    {$row['codi_lot']} ({$row['data_creacio']}) - {$row['qualitat']}
                </option>";
            }
        } else {
            echo "<option value=''>No hi ha lots</option>";
        }

        $conn->close();
        ?>
    </select>

    <label>Data del Control *</label>
    <input type="date" name="data_control" required>

    <label>Calibre</label>
    <input type="text" name="calibre">

    <label>Color</label>
    <input type="text" name="color">

    <label>Fermesa</label>
    <input type="text" name="fermesa">

    <label>Defectes</label>
    <textarea name="defectes"></textarea>

    <label>Sabor</label>
    <input type="text" name="sabor">

    <label>Aroma</label>
    <input type="text" name="aroma">

    <label>Observacions</label>
    <textarea name="observacions"></textarea>

    <label>Qualificació Final</label>
    <input type="text" name="qualificacio_final">

    <button type="submit" class="btn btn-primary btn-full mt-2">Guardar Control</button>

</form>
</div>

</div>
</body>
</html>