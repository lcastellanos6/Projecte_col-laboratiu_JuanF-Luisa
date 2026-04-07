<!DOCTYPE html>
<html lang="ca">
<head>
<meta charset="UTF-8">
<title>Registrar Contracte</title>
<link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="page">

<div class="page-header">
  <h2>Registrar Contracte</h2>
</div>

<div class="panel">
<form action="../PHP/guardar_contractre.php" method="POST">

    <!-- Treballador: desplegable -->
    <label for="id_treballador">Treballador</label>
    <select name="id_treballador" required>
        <option value="">Selecciona un treballador</option>
        <?php
        $conn = new mysqli("localhost","root","","web");
        if ($conn->connect_error) die("Error connexió: " . $conn->connect_error);

        $res = $conn->query("SELECT id_treballador, nom_complet FROM treballador");
        while($row = $res->fetch_assoc()){
            echo "<option value='{$row['id_treballador']}'>{$row['nom_complet']}</option>";
        }

        $conn->close();
        ?>
    </select>

    <label for="tipus_contracte">Tipus de contracte</label>
    <select name="tipus_contracte">
        <option value="Fix">Fix</option>
        <option value="Temporal">Temporal</option>
        <option value="Autonom">Autonom</option>
        <option value="Altres">Altres</option>
    </select>

    <label for="durada_contracte">Durada del contracte</label>
    <input type="text" name="durada_contracte" placeholder="Exemple: 6 mesos">

    <label for="categoria_professional">Categoria professional</label>
    <input type="text" name="categoria_professional">

    <label for="lloc_treball">Lloc de treball</label>
    <input type="text" name="lloc_treball">

    <label for="data_incorporacio">Data d'incorporació</label>
    <input type="date" name="data_incorporacio">

    <label for="data_finalitzacio">Data de finalització</label>
    <input type="date" name="data_finalitzacio">

    <label for="historial_laboral">Historial laboral</label>
    <textarea name="historial_laboral"></textarea>

    <button type="submit" class="btn btn-primary btn-full mt-2">Guardar Contracte</button>

</form>
</div>
</div>
</body>
</html>
