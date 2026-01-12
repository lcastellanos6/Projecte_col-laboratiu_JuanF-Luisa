<?php
$conn = new mysqli("localhost","root","","web");
if($conn->connect_error) die("Error BD");

$id_t = $_POST['id_treballador'];
$id_c = $_POST['id_calendari'];
$di   = $_POST['data_inici'];
$df   = $_POST['data_fi'] ?: null;

$stmt = $conn->prepare("
INSERT INTO treballador_calendari 
(id_treballador,id_calendari_model,data_inici,data_fi)
VALUES (?,?,?,?)
");
$stmt->bind_param("iiss",$id_t,$id_c,$di,$df);
$stmt->execute();

header("Location: ../HTML/assignar_calendari.html");
