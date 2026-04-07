<!DOCTYPE html>
<html lang="ca">
<head>
<meta charset="UTF-8">
<title>Assignar Treballador a Equip</title>
<link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="page">

<div class="page-header">
  <h2>Assignar Treballador a Equip</h2>
</div>

<div class="panel">
<form action="../PHP/guardar_membres.php" method="POST">

    <!-- Equip: desplegable -->
    <label for="id_equip">Equip</label>
    <select name="id_equip" required>
        <option value="">Selecciona un equip</option>
        <?php
        $conn = new mysqli("localhost","root","","web");
        if ($conn->connect_error) die("Error connexió: " . $conn->connect_error);

        $res = $conn->query("SELECT id_equip, nom FROM equip_treball");
        while($row = $res->fetch_assoc()){
            echo "<option value='{$row['id_equip']}'>{$row['nom']}</option>";
        }
        ?>
    </select>

    <!-- Treballador: desplegable -->
    <label for="id_treballador">Treballador</label>
    <select name="id_treballador" required>
        <option value="">Selecciona un treballador</option>
        <?php
        $res = $conn->query("SELECT id_treballador, nom_complet FROM treballador");
        while($row = $res->fetch_assoc()){
            echo "<option value='{$row['id_treballador']}'>{$row['nom_complet']}</option>";
        }

        $conn->close();
        ?>
    </select>

    <label for="rol_equip">Rol a l'equip</label>
    <input type="text" name="rol_equip" placeholder="Ex: Cap de plantació">

    <label for="data_alta">Data d'alta</label>
    <input type="date" name="data_alta">

    <label for="data_baixa">Data de baixa</label>
    <input type="date" name="data_baixa">

    <button type="submit" class="btn btn-primary btn-full mt-2">Assignar Treballador</button>

</form>
</div>

</div>
</body>
</html>