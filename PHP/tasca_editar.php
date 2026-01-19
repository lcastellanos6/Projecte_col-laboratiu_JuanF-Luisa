<?php
$conn = new mysqli("localhost","root","","web");
if ($conn->connect_error) die("Error BD");

$id = (int)$_GET['id'];
$res = $conn->query("SELECT * FROM tasca WHERE id_tasca=$id");
$t = $res->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="ca">
<head>
<meta charset="UTF-8">
<title>Editar tasca</title>
<link rel="stylesheet" href="../HTML/styles.css">
<style>
body{font-family:Arial;padding:20px}
label{display:block;margin-top:10px}
input,select,textarea{width:100%;padding:6px}
button{margin-top:15px;padding:10px;background:#2f7d2f;color:#fff;border:none}
</style>
</head>
<body>

<h2>✏️ Editar tasca</h2>

<form action="tasca_actualitzar.php" method="post">
<input type="hidden" name="id_tasca" value="<?= $t['id_tasca'] ?>">

<label>Nom de la tasca</label>
<input type="text" name="nom_tasca" value="<?= htmlspecialchars($t['nom_tasca']) ?>" required>

<label>Tipus de tasca</label>
<input type="text" name="tipus_tasca" value="<?= htmlspecialchars($t['tipus_tasca']) ?>">

<label>Data inici</label>
<input type="date" name="data_inici" value="<?= $t['data_inici'] ?>">

<label>Data final</label>
<input type="date" name="data_final" value="<?= $t['data_final'] ?>">

<label>Durada estimada</label>
<input type="text" name="durada_estimada" value="<?= $t['durada_estimada'] ?>">

<label>Personal requerit</label>
<input type="number" name="personal_requerit" value="<?= $t['personal_requerit'] ?>">

<label>Equipament necessari</label>
<textarea name="equipament_necessari"><?= $t['equipament_necessari'] ?></textarea>

<label>Instruccions</label>
<textarea name="instruccions"><?= $t['instruccions'] ?></textarea>

<label>Dependències</label>
<textarea name="dependencies"><?= $t['dependencies'] ?></textarea>

<label>Estat</label>
<select name="estat">
<?php
$estats = ['Planificada','En curs','Feta','Cancel·lada'];
foreach($estats as $e){
  $sel = $t['estat']==$e?'selected':'';
  echo "<option $sel>$e</option>";
}
?>
</select>

<button>Guardar canvis</button>
</form>

</body>
</html>
