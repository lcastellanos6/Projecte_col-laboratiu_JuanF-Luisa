<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Registrar seguiment</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="page">
    <div class="page-header">
        <h1>Registrar nou seguiment</h1>
    </div>
    <div class="panel">
        <form action="../PHP/guardar_seguiment.php" method="post">

            <label>Sector:</label>
            <select name="id_sector" required>
                <option value="">Selecciona un sector</option>
                <?php
                $conn = new mysqli("localhost", "root", "", "web");
                if ($conn->connect_error) die("Error de connexió: " . $conn->connect_error);

                // Carregar sectors existents
                $result = $conn->query("SELECT id_sector, nom FROM sector ORDER BY id_sector ASC");
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . $row['id_sector'] . "'>" . $row['nom'] . "</option>";
                    }
                } elseif (!$result) {
                    echo "<option value=''>Error a la consulta: " . $conn->error . "</option>";
                } else {
                    echo "<option value=''>No hi ha sectors registrats</option>";
                }

                $conn->close();
                ?>
            </select>

            <label>Data de registre:</label>
            <input type="date" name="data_registre" required>

            <label>Estat fenològic:</label>
            <input type="text" name="estat_fenologic">

            <label>Creixement vegetatiu:</label>
            <textarea name="creixement_vegetatiu"></textarea>

            <label>Incidències detectades:</label>
            <textarea name="incidencies_detectades"></textarea>

            <label>Intervencions realitzades:</label>
            <textarea name="intervencions_realitzades"></textarea>

            <label>Estimació actualitzada de collita:</label>
            <input type="number" step="0.01" name="estimacio_actualizada_collita">

            <button type="submit" class="btn btn-primary btn-full mt-2">Guardar Seguiment</button>
        </form>
    </div>
</div>
</body>
</html>