<?php
$conn = new mysqli("localhost","root","","web");
$id = intval($_GET['id']);

$conn->query("DELETE FROM parcela WHERE id_parcela=$id");

header("Location: ../PHP/mapa_parceles.php");
