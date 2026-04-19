<?php
require_once __DIR__ . '/auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clau = $_POST['nova_clau'];
    $clau_rep = $_POST['nova_clau_rep'];

    if (empty($clau) || $clau !== $clau_rep) {
        die("Les contrasenyes no coincideixen o estan buides.");
    }

    $conn = db_connect();
    $hash = password_hash($clau, PASSWORD_DEFAULT);
    $id_usuari = $_SESSION['id_usuari'];

    $stmt = $conn->prepare("UPDATE usuari SET contrasenya_hash = ? WHERE id_usuari = ?");
    $stmt->bind_param("si", $hash, $id_usuari);
    
    if ($stmt->execute()) {
        header("Location: perfil_treballador.php?msg=clau_ok");
    } else {
        die("Error en actualitzar la contrasenya.");
    }
    $conn->close();
} else {
    header("Location: perfil_treballador.php");
}
?>
