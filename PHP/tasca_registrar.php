<?php
$conn = new mysqli("localhost","root","","web");
if ($conn->connect_error) die("Error BD");
$conn->set_charset("utf8");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_tasca = intval($_POST['id_tasca']);
    $data_inici_real = isset($_POST['data_inici_real']) ? str_replace('T', ' ', $_POST['data_inici_real']) : null;
    $data_fi_real    = isset($_POST['data_fi_real']) ? str_replace('T', ' ', $_POST['data_fi_real']) : null;
    $notes           = $_POST['notes'] ?? '';

    // Subida de foto
    $foto_path = null;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $uploads_dir = __DIR__ . '/uploads';
        if (!is_dir($uploads_dir)) mkdir($uploads_dir, 0777, true);

        $tmp_name = $_FILES['foto']['tmp_name'];
        $filename = time() . '_' . basename($_FILES['foto']['name']);
        $target = $uploads_dir . '/' . $filename;

        if (move_uploaded_file($tmp_name, $target)) {
            $foto_path = 'uploads/' . $filename;
        } else {
            echo "Error subiendo la foto";
            exit;
        }
    }

    // Actualización de la tarea
    if ($foto_path) {
        $stmt = $conn->prepare("
            UPDATE tasca
            SET data_inici_real = ?, data_fi_real = ?, notes = ?, foto = ?
            WHERE id_tasca = ?
        ");
        $stmt->bind_param('ssssi', $data_inici_real, $data_fi_real, $notes, $foto_path, $id_tasca);
    } else {
        $stmt = $conn->prepare("
            UPDATE tasca
            SET data_inici_real = ?, data_fi_real = ?, notes = ?
            WHERE id_tasca = ?
        ");
        $stmt->bind_param('sssi', $data_inici_real, $data_fi_real, $notes, $id_tasca);
    }

    if ($stmt->execute()) {
        header("Location: calendari_tasques.php");
        exit;
    } else {
        echo "Error al guardar: " . $stmt->error;
    }
} else {
    echo "Acceso no permitido.";
}
