<?php
$conn = new mysqli("localhost","root","","web");
if ($conn->connect_error) die("Error BD");

$id = (int)$_GET['id'];
$conn->query("DELETE FROM tasca WHERE id_tasca = $id");

header("Location: tasca.php");
