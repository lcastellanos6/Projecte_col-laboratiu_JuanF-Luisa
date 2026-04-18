<?php
require_once __DIR__ . '/db.php';

$conn = db_connect();

$nom_complet = trim((string)($_POST['nom_complet'] ?? ''));
$document_identitat = trim((string)($_POST['document_identitat'] ?? ''));
$num_seguretat_social = trim((string)($_POST['num_seguretat_social'] ?? ''));
$data_naixement = trim((string)($_POST['data_naixement'] ?? ''));
$lloc_naixement = trim((string)($_POST['lloc_naixement'] ?? ''));
$nacionalitat = trim((string)($_POST['nacionalitat'] ?? ''));
$residencia = trim((string)($_POST['residencia'] ?? ''));
$telefon = trim((string)($_POST['telefon'] ?? ''));
$email = trim((string)($_POST['email'] ?? ''));
$adreca = trim((string)($_POST['adreca'] ?? ''));
$contacte_emergencia = trim((string)($_POST['contacte_emergencia'] ?? ''));
$telefon_emergencia = trim((string)($_POST['telefon_emergencia'] ?? ''));
$compte_bancari = trim((string)($_POST['compte_bancari'] ?? ''));
$consentiment_rgpd = isset($_POST['consentiment_rgpd']) ? (int)$_POST['consentiment_rgpd'] : 0;
$id_posicio = isset($_POST['id_posicio']) && ctype_digit((string)$_POST['id_posicio']) ? (int)$_POST['id_posicio'] : null;
$id_calendari_model = isset($_POST['id_calendari_model']) && ctype_digit((string)$_POST['id_calendari_model']) ? (int)$_POST['id_calendari_model'] : null;
$id_horari_model = isset($_POST['id_horari_model']) && ctype_digit((string)$_POST['id_horari_model']) ? (int)$_POST['id_horari_model'] : null;

if ($nom_complet === '' || $document_identitat === '') {
    $conn->close();
    echo "<p style='color:red;font-weight:bold;'>❌ Camps obligatoris: nom complet i document d'identitat.</p>";
    echo "<a href='../HTML/treballador.html'>Tornar</a>";
    exit;
}

$num_seguretat_social = $num_seguretat_social !== '' ? $num_seguretat_social : null;
$data_naixement = $data_naixement !== '' ? $data_naixement : null;
$lloc_naixement = $lloc_naixement !== '' ? $lloc_naixement : null;
$nacionalitat = $nacionalitat !== '' ? $nacionalitat : null;
$residencia = $residencia !== '' ? $residencia : null;
$telefon = $telefon !== '' ? $telefon : null;
$email = $email !== '' ? $email : null;
$adreca = $adreca !== '' ? $adreca : null;
$contacte_emergencia = $contacte_emergencia !== '' ? $contacte_emergencia : null;
$telefon_emergencia = $telefon_emergencia !== '' ? $telefon_emergencia : null;
$compte_bancari = $compte_bancari !== '' ? $compte_bancari : null;

// Upload foto
$fotografia = null;
if (isset($_FILES['fotografia']) && $_FILES['fotografia']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = __DIR__ . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR;
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    $fileName = time() . "_" . basename($_FILES['fotografia']['name']);
    $filePath = $uploadDir . $fileName;
    if (move_uploaded_file($_FILES['fotografia']['tmp_name'], $filePath)) {
        $fotografia = 'uploads/' . $fileName;
    }
}

$sql = "INSERT INTO treballador (
    nom_complet, fotografia, document_identitat, num_seguretat_social,
    data_naixement, lloc_naixement, nacionalitat, residencia,
    telefon, email, adreca, contacte_emergencia, telefon_emergencia,
    compte_bancari, consentiment_rgpd, id_posicio, id_calendari_model, id_horari_model
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    $conn->close();
    echo "<p style='color:red;font-weight:bold;'>❌ Error en preparar la consulta.</p>";
    exit;
}

$stmt->bind_param(
    "ssssssssssssssiiii",
    $nom_complet,
    $fotografia,
    $document_identitat,
    $num_seguretat_social,
    $data_naixement,
    $lloc_naixement,
    $nacionalitat,
    $residencia,
    $telefon,
    $email,
    $adreca,
    $contacte_emergencia,
    $telefon_emergencia,
    $compte_bancari,
    $consentiment_rgpd,
    $id_posicio,
    $id_calendari_model,
    $id_horari_model
);

try {
    if ($stmt->execute()) {
        echo "<h2 style='color:green'>✅ Treballador guardat correctament!</h2>";
        echo "<p>ID assignat: " . (int)$conn->insert_id . "</p>";
        if ($fotografia) {
            echo "<p>Foto pujada a: " . htmlspecialchars($fotografia, ENT_QUOTES, 'UTF-8') . "</p>";
        }
        echo "<p><a href='../HTML/treballador.html'>Afegir un altre treballador</a></p>";
        echo "<p><a href='../HTML/index.php'>Tornar al panell</a></p>";
    } else {
        echo "<p style='color:red'>❌ Error en guardar: " . htmlspecialchars($stmt->error, ENT_QUOTES, 'UTF-8') . "</p>";
    }
} catch (mysqli_sql_exception $e) {
    echo "<p style='color:red'>❌ Error en guardar.</p>";
}

$stmt->close();
$conn->close();
?>

