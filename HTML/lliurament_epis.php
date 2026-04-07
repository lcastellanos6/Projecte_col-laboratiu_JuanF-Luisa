<!DOCTYPE html>
<html lang="ca">
<head>
<meta charset="UTF-8">
<title>Registrar Lliurament EPI</title>
<link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="page">

<div class="page-header">
  <h2>Registrar Lliurament EPI</h2>
</div>

<div class="panel">
<form action="../PHP/guardar_epis.php" method="POST" enctype="multipart/form-data">

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
        ?>
    </select>

    <!-- Tipus EPI: desplegable -->
    <label for="id_epi_tipus">Tipus EPI</label>
    <select name="id_epi_tipus" required>
        <option value="">Selecciona un tipus d'EPI</option>
        <?php
        $res = $conn->query("SELECT id_epi_tipus, nom FROM epi_tipus");
        while($row = $res->fetch_assoc()){
            echo "<option value='{$row['id_epi_tipus']}'>{$row['nom']}</option>";
        }

        $conn->close();
        ?>
    </select>

    <label for="talla">Talla</label>
    <input type="text" name="talla">

    <label for="quantitat">Quantitat</label>
    <input type="number" name="quantitat" value="1">

    <label for="data_lliurament">Data de lliurament</label>
    <input type="date" name="data_lliurament" required>

    <label for="data_devolucio">Data de devolució</label>
    <input type="date" name="data_devolucio">

    <label for="data_caducitat">Data de caducitat</label>
    <input type="date" name="data_caducitat">

    <label for="document_signat_url">Document signat (URL)</label>
    <input type="text" name="document_signat_url" placeholder="https://...">

    <label for="observacions">Observacions</label>
    <textarea name="observacions"></textarea>

    <button type="submit" class="btn btn-primary btn-full mt-2">Guardar Lliurament</button>

</form>
</div>

</div>
</body>
</html>
