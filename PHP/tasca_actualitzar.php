<?php
$conn = new mysqli("localhost","root","","web");
if ($conn->connect_error) die("Error BD");

$stmt = $conn->prepare("
UPDATE tasca SET
nom_tasca=?, tipus_tasca=?, data_inici=?, data_final=?, durada_estimada=?,
personal_requerit=?, equipament_necessari=?, instruccions=?, dependencies=?, estat=?
WHERE id_tasca=?
");

$stmt->bind_param(
  "sssssissssi",
  $_POST['nom_tasca'],
  $_POST['tipus_tasca'],
  $_POST['data_inici'],
  $_POST['data_final'],
  $_POST['durada_estimada'],
  $_POST['personal_requerit'],
  $_POST['equipament_necessari'],
  $_POST['instruccions'],
  $_POST['dependencies'],
  $_POST['estat'],
  $_POST['id_tasca']
);

$stmt->execute();
header("Location: tasca.php");
