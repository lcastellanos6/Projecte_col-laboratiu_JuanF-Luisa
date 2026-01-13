<?php
$conn = new mysqli("localhost","root","","web");

$id = intval($_POST['id'] ?? 0);
$nom = $_POST['nom'] ?? '';
$municipi = $_POST['municipi'] ?? '';
$tipus_sol = $_POST['tipus_sol'] ?? '';
$pendent = (isset($_POST['pendent']) && $_POST['pendent'] !== '') ? floatval($_POST['pendent']) : null;

$stmt = $conn->prepare("
UPDATE parcela SET
  nom=?,
  municipi=?,
  tipus_sol=?,
  pendent=?
WHERE id_parcela=?
");
$stmt->bind_param("sssdi", $nom, $municipi, $tipus_sol, $pendent, $id);
$stmt->execute();
$stmt->close();

header("Location: consulta_parcela_sector.php");
exit;
