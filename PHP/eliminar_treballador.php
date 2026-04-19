<?php
session_start();
require_once __DIR__ . '/db.php';
$conn = db_connect();

// Restricció de rol: només administradors
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    die("Accés denegat.");
}

$id = $_GET['id'] ?? 0;

if ($id) {
    // Primer comprovem si té dades vinculades que puguin donar errors de FK
    // En un sistema real faríem un soft-delete o demanaríem confirmació extra
    $id = (int)$id;
    
    // Intentem eliminar (les FK haurien d'estar en ON DELETE SET NULL o RESTRICT)
    $stmt = $conn->prepare("DELETE FROM treballador WHERE id_treballador = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        header("Location: consulta_treballadors.php?msg=eliminat");
    } else {
        die("Error en eliminar el treballador. És possible que tingui dades vinculades (contractes, absències, etc.).");
    }
} else {
    header("Location: consulta_treballadors.php");
}
$conn->close();
?>
