<?php
session_start();
require_once __DIR__ . '/db.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    die("Accés denegat.");
}

$conn = db_connect();

$id_treballador = $_POST['id_treballador'] ?? 0;
$nom_complet = trim($_POST['nom_complet'] ?? '');
$document_identitat = trim($_POST['document_identitat'] ?? '');
$email = trim($_POST['email'] ?? '');
$telefon = trim($_POST['telefon'] ?? '');
$id_posicio = $_POST['id_posicio'] ?? null;

if (!$id_treballador || !$nom_complet || !$document_identitat) {
    die("Dades incompletes.");
}

$sql = "UPDATE treballador SET nom_complet = ?, document_identitat = ?, email = ?, telefon = ?, id_posicio = ? WHERE id_treballador = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssii", $nom_complet, $document_identitat, $email, $telefon, $id_posicio, $id_treballador);

if ($stmt->execute()) {
    header("Location: consulta_treballadors.php?msg=updated");
} else {
    echo "Error en actualitzar: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
