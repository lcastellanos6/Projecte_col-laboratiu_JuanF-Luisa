<?php
$conn = new mysqli("localhost","root","","web");
if($conn->connect_error) die("Error BD");

$id_t = $_POST['id_treballador'];
$inici = $_POST['inici'];
$fi = $_POST['fi'];
$pausa = $_POST['pausa'] ?? 0;
$id_tasca = $_POST['id_tasca'] ?: null;
$inc = $_POST['incidencies'];

/* Validació bàsica */
if(strtotime($fi) <= strtotime($inici)){
  die("Error: l'hora de fi ha de ser posterior a l'inici");
}

/* Guardar jornada */
$stmt = $conn->prepare("
INSERT INTO jornada
(id_treballador,data_hora_inici,data_hora_fi,minuts_pausa,incidencies,id_tasca)
VALUES (?,?,?,?,?,?)
");
$stmt->bind_param(
  "issisi",
  $id_t,
  $inici,
  $fi,
  $pausa,
  $inc,
  $id_tasca
);
$stmt->execute();

header("Location: ../HTML/registrar_jornada.html");
