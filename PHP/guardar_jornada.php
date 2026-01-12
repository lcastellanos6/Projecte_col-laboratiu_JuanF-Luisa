<?php
$conn = new mysqli("localhost","root","","web");
if ($conn->connect_error) die("Error BD");

$id_treballador = $_POST['id_treballador'];
$inici = $_POST['data_hora_inici'];
$fi = $_POST['data_hora_fi'];
$pausa = $_POST['minuts_pausa'] ?? 0;
$inc = $_POST['incidencies'] ?? null;
$id_tasca = $_POST['id_tasca'] ?: null;

$stmt = $conn->prepare("
INSERT INTO jornada
(id_treballador, data_hora_inici, data_hora_fi, minuts_pausa, incidencies, id_tasca)
VALUES (?,?,?,?,?,?)
");
$stmt->bind_param("issisi",
    $id_treballador,
    $inici,
    $fi,
    $pausa,
    $inc,
    $id_tasca
);
$stmt->execute();

header("Location: registrar_jornada.php");
exit;
