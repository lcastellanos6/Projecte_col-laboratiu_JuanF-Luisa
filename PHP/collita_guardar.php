<?php
$conn = new mysqli("localhost","root","","web");

$stmt = $conn->prepare("
INSERT INTO collita
(data_inici, data_fi, id_parcela, varietat, quantitat, unitat, equip_recollidors, incidencies)
VALUES (?,?,?,?,?,?,?,?)
");

$stmt->bind_param(
 "ssisdsss",
 $_POST['data_inici'],
 $_POST['data_fi'],
 $_POST['id_parcela'],
 $_POST['varietat'],
 $_POST['quantitat'],
 $_POST['unitat'],
 $_POST['equip_recollidors'],
 $_POST['incidencies']
);

$stmt->execute();
header("Location: collites_llista.php");
?>