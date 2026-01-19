<?php
$conn = new mysqli("localhost","root","","web");
if ($conn->connect_error) die("Error BD");

$stmt = $conn->prepare("
INSERT INTO tasca
(nom_tasca, tipus_tasca, data_inici, data_final, durada_estimada,
 personal_requerit, equipament_necessari, instruccions, dependencies, estat)
VALUES (?,?,?,?,?,?,?,?,?,?)
");

$stmt->bind_param(
  "sssssissss",
  $_POST['nom_tasca'],
  $_POST['tipus_tasca'],
  $_POST['data_inici'],
  $_POST['data_final'],
  $_POST['durada_estimada'],
  $_POST['personal_requerit'],
  $_POST['equipament_necessari'],
  $_POST['instruccions'],
  $_POST['dependencies'],
  $_POST['estat']
);

$stmt->execute();

header("Location: tasca.php");
