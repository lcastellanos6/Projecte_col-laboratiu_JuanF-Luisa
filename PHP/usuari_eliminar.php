<?php
require_once __DIR__ . '/auth.php';
require_login();

if ($_SESSION['rol'] !== 'admin') {
    die("Accés denegat.");
}

$conn = db_connect();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id) {
    // No permetem que un admin s'elimini a si mateix per evitar bloquejos
    if ($id === (int)$_SESSION['id_usuari']) {
        die("No pots eliminar el teu propi usuari.");
    }

    $stmt = $conn->prepare("DELETE FROM usuari WHERE id_usuari = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        header("Location: consulta_usuaris.php?msg=eliminat");
    } else {
        die("Error en eliminar l'usuari.");
    }
} else {
    header("Location: consulta_usuaris.php");
}
$conn->close();
?>
