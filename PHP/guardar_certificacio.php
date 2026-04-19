<?php
session_start();
require_once __DIR__ . '/db.php';
$conn = db_connect();

// Verificar sessió
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    die("Accés denegat.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_treballador = (int)$_POST['id_treballador'];
    $id_formacio_cert = (int)$_POST['id_formacio_cert'];
    $data_inici = $_POST['data_inici'];
    $data_caducitat = !empty($_POST['data_caducitat']) ? $_POST['data_caducitat'] : null;
    $hores = !empty($_POST['hores']) ? (float)$_POST['hores'] : null;
    $observacions = $_POST['observacions'] ?? '';

    // Gestionar fitxer si n'hi ha
    $document_url = null;
    if (isset($_FILES['document_url']) && $_FILES['document_url']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/certificats/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $filename = time() . '_' . basename($_FILES['document_url']['name']);
        $target_file = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['document_url']['tmp_name'], $target_file)) {
            $document_url = '../PHP/' . $target_file;
        }
    }

    $sql = "INSERT INTO treballador_formacio_cert (id_treballador, id_formacio_cert, data_inici, data_caducitat, hores, document_url, observacions) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iissdss", $id_treballador, $id_formacio_cert, $data_inici, $data_caducitat, $hores, $document_url, $observacions);

    if ($stmt->execute()) {
        header("Location: perfil_treballador.php?id=" . $id_treballador);
    } else {
        echo "Error al guardar la certificació: " . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>
