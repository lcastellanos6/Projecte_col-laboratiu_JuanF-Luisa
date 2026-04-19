<?php
require_once __DIR__ . '/auth.php';
require_login();

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    die("Accés denegat.");
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id) {
    $conn = db_connect();
    // Eliminar línea de comanda primer si existeix
    $conn->query("DELETE FROM comanda_linia WHERE id_comanda = $id");
    // Eliminar comanda
    $stmt = $conn->prepare("DELETE FROM comanda WHERE id_comanda = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $conn->close();
}

header("Location: consulta_comandes.php");
exit;
