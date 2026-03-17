<!DOCTYPE html> 
<html lang="ca">
<head>
<meta charset="UTF-8">
<title>Registrar Moviment de Lot</title>
<link rel="stylesheet" href="styles.css">
</head>
<body>
<h2>Registrar Moviment de Lot</h2>

<form action="../PHP/guardar_moviment_lot.php" method="post">

    <label>ID Lot *</label>
    <select name="id_lot" required>
        <option value="">Selecciona un lot</option>
        <?php
        // Connexió per carregar els lots
        $conn = new mysqli("localhost", "root", "", "web");
        if ($conn->connect_error) {
            die("Error de connexió: " . $conn->connect_error);
        }

        $result = $conn->query("SELECT id_lot, numero_lot FROM producte_lot ORDER BY id_lot DESC");

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<option value='" . $row['id_lot'] . "'>" . $row['numero_lot'] . "</option>";
            }
        } elseif (!$result) {
            echo "<option value=''>Error a la consulta: " . $conn->error . "</option>";
        } else {
            echo "<option value=''>No hi ha lots registrats</option>";
        }

        $conn->close();
        ?>
    </select>

    <label>Data (si no s’indica, s’utilitzarà l’actual)</label>
    <input type="datetime-local" name="data">

    <label>Quantitat *</label>
    <input type="number" step="0.001" name="quantitat" required>

    <label>Motiu *</label>
    <select name="motiu" required>
        <option value="Compra">Compra</option>
        <option value="Ajust">Ajust</option>
        <option value="Aplicacio">Aplicació</option>
    </select>

    <label>ID Aplicació (opcional)</label>
    <input type="number" name="id_aplicacio">

    <label>Observacions</label>
    <textarea name="observacions"></textarea>

    <button type="submit">Guardar Moviment</button>
</form>

</body>
</html>
