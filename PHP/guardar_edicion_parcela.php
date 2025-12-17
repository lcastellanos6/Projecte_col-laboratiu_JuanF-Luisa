<?php
$conn = new mysqli("localhost","root","","web");

$id = intval($_POST['id']);
$nom = $_POST['nom'];
$municipi = $_POST['municipi'];
$tipus_sol = $_POST['tipus_sol'];
$pendent = $_POST['pendent'];

$conn->query("
UPDATE parcela SET
  nom='$nom',
  municipi='$municipi',
  tipus_sol='$tipus_sol',
  pendent='$pendent'
WHERE id_parcela=$id
");

header("Location: ../PHP/mapa_parceles.php");
