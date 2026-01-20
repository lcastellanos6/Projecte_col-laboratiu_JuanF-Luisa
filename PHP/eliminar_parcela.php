<?php
$conn = new mysqli("localhost","root","","web");
$id = intval($_GET['id']);

$stmt = $conn->prepare("DELETE FROM parcela WHERE id_parcela=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

header("Location: consulta_parcela_sector.php");
exit;
