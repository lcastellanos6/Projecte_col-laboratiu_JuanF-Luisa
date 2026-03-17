<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "web";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connexió fallida: " . $conn->connect_error);
}

// CONSULTA FILES
$resultFiles = $conn->query("SELECT id_fila, numero_fila FROM fila");
?>

<!DOCTYPE html>
<html lang="ca">
<head>
<meta charset="UTF-8">
<title>Registrar Tractament</title>
<link rel="stylesheet" href="styles.css">
</head>

<body>
<div class="page">

<div class="page-header">
  <h1>Registrar nou Tractament</h1>
</div>

<div class="panel">
<form action="../PHP/guardar_tractament.php" method="post">

    <!-- 🔽 AQUÍ EL CANVI -->
    <label for="id_fila">ID Fila:</label>

    <select name="id_fila" required>
        <option value="">-- Selecciona una fila --</option>

        <?php while($fila = $resultFiles->fetch_assoc()): ?>
            <option value="<?= $fila['id_fila'] ?>">
                Fila <?= $fila['numero_fila'] ?> (ID: <?= $fila['id_fila'] ?>)
            </option>
        <?php endwhile; ?>

    </select>

    <!-- RESTO IGUAL -->
    <label for="data">Data:</label>
    <input type="date" name="data" required>

    <label for="tipus">Tipus de tractament:</label>
    <select name="tipus">
        <option value="Fitosanitari">Fitosanitari</option>
        <option value="Fertilitzant">Fertilitzant</option>
        <option value="Altre">Altre</option>
    </select>

    <label for="producte">Producte:</label>
    <input type="text" name="producte" placeholder="Nom del producte">

    <label for="dosi_ml">Dosi (ml):</label>
    <input type="text" name="dosi_ml" placeholder="Quantitat aplicada en ml">

    <button type="submit" class="btn btn-primary btn-full mt-2">
        Guardar Tractament
    </button>

</form>
</div>

</div>
</body>
</html>

<?php $conn->close(); ?>

