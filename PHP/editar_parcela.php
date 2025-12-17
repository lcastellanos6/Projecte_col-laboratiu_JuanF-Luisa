<?php
$conn = new mysqli("localhost","root","","web");
$id = intval($_GET['id']);
$res = $conn->query("SELECT * FROM parcela WHERE id_parcela=$id");
$p = $res->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="ca">
<head>
<meta charset="utf-8">
<title>Editar parcel·la</title>
</head>
<style>
body { font-family: Arial, sans-serif; background:#f5fff5; padding:20px; }
form { background:white; padding:20px; border-radius:8px; max-width:500px; margin:auto;
       box-shadow:0 2px 6px rgba(0,0,0,0.1); }
label { font-weight:bold; margin-top:10px; display:block; }
input, textarea, button { width:100%; padding:8px; margin-top:5px; border-radius:4px; border:1px solid #ccc; }
button { margin-top:15px; background:#2f7d2f; color:white; padding:10px; border:none;
         border-radius:5px; cursor:pointer; width:100%; }
button:hover { background:#3d9b3d; }
</style>
<body>

<h2>Editar parcel·la</h2>

<form method="post" action="../PHP/guardar_edicion_parcela.php">
<input type="hidden" name="id" value="<?= $p['id_parcela'] ?>">

<label>Nom</label><br>
<input type="text" name="nom" value="<?= htmlspecialchars($p['nom']) ?>"><br>

<label>Municipi</label><br>
<input type="text" name="municipi" value="<?= htmlspecialchars($p['municipi']) ?>"><br>

<label>Tipus sòl</label><br>
<input type="text" name="tipus_sol" value="<?= htmlspecialchars($p['tipus_sol']) ?>"><br>

<label>Pendent</label><br>
<input type="number" step="0.01" name="pendent" value="<?= $p['pendent'] ?>"><br><br>

<button type="submit">Guardar</button>
<a href="../PHP/mapa_parceles.php"><button type="button">Cancel·lar</button></a>
</form>

</body>
</html>
