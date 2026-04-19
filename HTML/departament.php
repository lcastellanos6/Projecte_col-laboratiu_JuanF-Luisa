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
    <title>Departament</title>
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
  <h2>Registrar Departament</h2>
</div>

<div class="panel">
<form action="../PHP/guardar_departaments.php" method="POST">
    <label>Nom del departament *</label>
    <input type="text" name="nom" required>

    <label>Descripció</label>
    <textarea name="descripcio" rows="4"></textarea>

    <button type="submit" class="btn btn-primary btn-full mt-2">Guardar</button>
</form>
</div>

</div>
</body>
</html>
