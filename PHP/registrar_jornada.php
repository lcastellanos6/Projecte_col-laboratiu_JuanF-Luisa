<?php
// Mostrar errores de PHP para depuración
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Conexión a la base de datos
$conn = new mysqli("localhost", "root", "", "web");
if ($conn->connect_error) {
    die("Error al conectar con la base de datos: " . $conn->connect_error);
}

// Consultas a la base de datos
$treballadors = $conn->query("SELECT id_treballador, nom_complet FROM treballador ORDER BY nom_complet");
if (!$treballadors) {
    die("Error al consultar treballadors: " . $conn->error);
}

$tasques = $conn->query("SELECT id_tasca, nom_tasca FROM tasca ORDER BY nom_tasca");
if (!$tasques) {
    die("Error al consultar tasques: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
<meta charset="UTF-8">
<title>Registrar jornada laboral</title>
<link rel="stylesheet" href="styles.css">
<style>
body { font-family: Arial, sans-serif; background:#f5fff5; padding:20px; }
form {
    background:white; padding:20px; border-radius:8px;
    max-width:500px; margin:auto;
    box-shadow:0 2px 6px rgba(0,0,0,0.1);
}
label { font-weight:bold; margin-top:10px; display:block; }
input, select, textarea, button {
    width:100%; padding:8px; margin-top:5px;
    border-radius:4px; border:1px solid #ccc;
}
textarea { height:80px; resize:vertical; }
button {
    margin-top:15px; background:#2f7d2f; color:white;
    padding:10px; border:none; border-radius:5px;
    cursor:pointer;
}
button:hover { background:#3d9b3d; }
</style>
</head>
<body>

<h2>➕ Registrar jornada laboral</h2>

<form action="guardar_jornada.php" method="post">
    <!-- Trabajador -->
    <label>Treballador</label>
    <select name="id_treballador" required>
        <?php if ($treballadors->num_rows > 0): ?>
            <?php while ($t = $treballadors->fetch_assoc()): ?>
                <option value="<?php echo $t['id_treballador']; ?>">
                    <?php echo htmlspecialchars($t['nom_complet']); ?>
                </option>
            <?php endwhile; ?>
        <?php else: ?>
            <option value="">No hi ha treballadors</option>
        <?php endif; ?>
    </select>

    <!-- Fecha y hora inicio -->
    <label>Data i hora inici</label>
    <input type="datetime-local" name="data_hora_inici" required>

    <!-- Fecha y hora fin -->
    <label>Data i hora fi</label>
    <input type="datetime-local" name="data_hora_fi" required>

    <!-- Minutos pausa -->
    <label>Minuts pausa</label>
    <input type="number" name="minuts_pausa" value="0" min="0">

    <!-- Tarea -->
    <label>Tasca (opcional)</label>
    <select name="id_tasca">
        <option value="">-- cap --</option>
        <?php if ($tasques->num_rows > 0): ?>
            <?php while ($ta = $tasques->fetch_assoc()): ?>
                <option value="<?php echo $ta['id_tasca']; ?>">
                    <?php echo htmlspecialchars($ta['nom_tasca']); ?>
                </option>
            <?php endwhile; ?>
        <?php else: ?>
            <option value="">No hi ha tasques</option>
        <?php endif; ?>
    </select>

    <!-- Incidencias -->
    <label>Incidències</label>
    <textarea name="incidencies" rows="4"></textarea>

    <button type="submit">Guardar jornada</button>
</form>

</body>
</html>

