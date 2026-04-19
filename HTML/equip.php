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
<title>Registrar Equip</title>
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
  <h2>Registrar Equip</h2>
</div>

<div class="panel">
<form action="../PHP/guardar_equip.php" method="POST">

    <label for="nom">Nom de l'equip</label>
    <input type="text" name="nom" required>

    <label for="id_departament">ID Departament *</label>
    <input type="number" name="id_departament" required>

    <label for="descripcio">Descripció</label>
    <textarea name="descripcio"></textarea>

    <button type="submit" class="btn btn-primary btn-full mt-2">Guardar Equip</button>

</form>
</div>

</div>
</body>
</html>
