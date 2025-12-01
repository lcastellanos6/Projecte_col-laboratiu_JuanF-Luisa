<!DOCTYPE html>
<html lang="ca">
<head>
<meta charset="UTF-8">
<title>Registre Climàtic</title>
<style>
body { font-family: Arial; margin: 20px; }
        label { font-weight: bold; display: block; margin-top: 10px; }
        input, textarea, button { width: 100%; padding: 8px; margin-top: 5px; border-radius:5px; border:1px solid #ccc; }
        button { background-color: #4CAF50; color: white; border:none; margin-top:15px; cursor:pointer; }
        button:hover { background-color: #45a049; }
</style>
</head>

<body>

<h2>Afegir Registre Climàtic</h2>

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

    <button type="submit">Guardar registre</button>
</form>

</body>
</html>
