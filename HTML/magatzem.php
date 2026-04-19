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
    <title>Gestió de Magatzems</title>
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
  <h2>Afegir Magatzem</h2>
</div>

    <div class="panel">
<form action="../PHP/guardar_magatzem.php" method="post">

        <label>Nom del Magatzem *</label>
        <input type="text" name="nom" required>

        <label>Ubicació</label>
        <input type="text" name="ubicacio">

        <button type="submit" class="btn btn-primary btn-full mt-2">Guardar</button>

    </form>
</div>

</div>
</body>
</html>
