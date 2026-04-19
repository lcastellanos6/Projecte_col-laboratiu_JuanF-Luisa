<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    die("Accés denegat.");
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
<meta charset="UTF-8">
<title>Registrar Horari Model</title>
<link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="page">
    <div class="mb-2">
        <a href="javascript:history.back()" class="btn btn-ghost">
            <i class="fa-solid fa-arrow-left"></i> Tornar
        </a>
    </div>

<div class="page-header">
  <h1>Registrar Nou Horari Model</h1>
</div>

<div class="panel">
<form action="../PHP/guardar_horari.php" method="post">
    <label>Nom de l'horari:</label>
    <input type="text" name="nom" required>

    <label>Descripció:</label>
    <textarea name="descripcio"></textarea>

    <label>Hora d'inici:</label>
    <input type="time" name="hora_inici">

    <label>Hora de fi:</label>
    <input type="time" name="hora_fi">

    <label>Pausa en minuts:</label>
    <input type="number" name="pausa_minuts" min="0" value="0">

    <button type="submit" class="btn btn-primary btn-full mt-2">Guardar Horari</button>
</form>
</div>

</div>
</body>
</html>
