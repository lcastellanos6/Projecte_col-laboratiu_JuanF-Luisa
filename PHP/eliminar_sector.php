<?php
$conn = new mysqli("localhost","root","","web");
$id = intval($_GET['id'] ?? 0);

$stmtRel = $conn->prepare("DELETE FROM sector_parcela WHERE id_sector=?");
$stmtRel->bind_param("i", $id);
$stmtRel->execute();
$stmtRel->close();

$stmt = $conn->prepare("DELETE FROM sector WHERE id_sector=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

header("Location: consulta_parcela_sector.php");
exit;
?>
