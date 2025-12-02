<!DOCTYPE html>
<html lang="ca">
<head>
<meta charset="UTF-8">
<title>Registre Climàtic</title>
<link rel="stylesheet" href="../HTML/styles.css">
</head>

<body>

<div class="page">
<div class="page-header">
  <h1>Afegir registre climàtic</h1>
  <p class="page-subtitle">Desa dades anuals de temperatura, precipitació i altres factors.</p>
</div>

<div class="panel">
<form action="guardar_clima.php" method="post">

    <label>ID Plantació *</label>
    <input type="number" name="id_plantacio" required>

    <label>Any *</label>
    <input type="number" name="any_temp" required>

    <label>Temperatura mitjana (°C)</label>
    <input type="number" step="0.01" name="temperatura_mitjana">

    <label>Precipitació total (mm)</label>
    <input type="number" step="0.01" name="precipitacio_total">

    <label>Altres factors climàtics</label>
    <textarea name="altres_factors"></textarea>

    <button type="submit" class="btn btn-primary btn-full mt-2">Guardar registre</button>
</form>
</div>
</div>

</body>
</html>
