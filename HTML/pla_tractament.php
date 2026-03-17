<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "web";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connexió fallida: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="ca">
<head>
<meta charset="UTF-8">
<title>Afegir Pla de Tractament</title>
<link rel="stylesheet" href="styles.css">
</head>

<body>
<div class="page">

<div class="page-header">
  <h2>Afegir Pla de Tractament</h2>
</div>

<div class="panel">
<form action="../PHP/guardar_pla.php" method="post">

    <!-- NOM -->
    <label>Nom *</label>
    <input type="text" name="nom" required>

    <!-- TIPUS -->
    <label>Tipus *</label>
    <select name="tipus" required>
        <option value="">-- Selecciona --</option>
        <option value="Preventiu">Preventiu</option>
        <option value="Curatiu">Curatiu</option>
    </select>

    <!-- ESTAT INICI -->
    <label>Estat Fenològic Inici</label>
    <select name="id_estat_inici">
        <option value="">-- Selecciona estat --</option>
        <?php
        $resultEstat = $conn->query("SELECT id_estat, codi, nom FROM estat_fenologic ORDER BY codi");
        while($row = $resultEstat->fetch_assoc()){
            echo "<option value='".$row['id_estat']."'>";
            echo $row['codi']." - ".$row['nom'];
            echo "</option>";
        }
        ?>
    </select>

    <!-- ESTAT FI -->
    <label>Estat Fenològic Fi</label>
    <select name="id_estat_fi">
        <option value="">-- Selecciona estat --</option>
        <?php
        $resultEstat2 = $conn->query("SELECT id_estat, codi, nom FROM estat_fenologic ORDER BY codi");
        while($row = $resultEstat2->fetch_assoc()){
            echo "<option value='".$row['id_estat']."'>";
            echo $row['codi']." - ".$row['nom'];
            echo "</option>";
        }
        ?>
    </select>

    <!-- ESPÈCIE CORREGIDA -->
    <label>Varietat</label>
    <select name="id_especie">
        <option value="">-- Selecciona espècie --</option>
        <?php
        $resultEspecie = $conn->query("SELECT id_especie, nom_cientific, nom_comu FROM especie ORDER BY nom_comu");
        while($row = $resultEspecie->fetch_assoc()){
            echo "<option value='".$row['id_especie']."'>";
            echo $row['nom_comu']." (".$row['nom_cientific'].")";
            echo "</option>";
        }
        ?>
    </select>

    <!-- FINESTRA DATA INICI -->
    <label>Finestra Data Inici</label>
    <input type="date" name="finestra_data_inici">

    <!-- FINESTRA DATA FI -->
    <label>Finestra Data Fi</label>
    <input type="date" name="finestra_data_fi">

    <!-- PLAGA / MALALTIA -->
    <label>Plaga / Malaltia Objectiu</label>
    <input type="text" name="plaga_malaltia_objectiu">

    <!-- OBSERVACIONS -->
    <label>Observacions</label>
    <textarea name="observacions"></textarea>

    <button type="submit" class="btn btn-primary btn-full mt-2">
        Guardar Pla de Tractament
    </button>

</form>
</div>

</div>
</body>
</html>

<?php
$conn->close();
?>


