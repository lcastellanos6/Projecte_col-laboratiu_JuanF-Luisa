<?php
require_once __DIR__ . '/auth.php';
require_login();

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    die("Accés denegat.");
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id) {
    $conn = db_connect();
    $stmt = $conn->prepare("DELETE FROM absencia WHERE id_absencia = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $conn->close();
}

header("Location: consulta_absencies.php");
exit;
