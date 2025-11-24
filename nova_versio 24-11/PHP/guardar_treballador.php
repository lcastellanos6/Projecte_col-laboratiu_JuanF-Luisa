<?php
// Connexió a la base de dades
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "web";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("❌ Error de connexió: " . $conn->connect_error);

// Recollir valors del formulari
$nom_complet = $_POST['nom_complet'] ?? '';
$document_identitat = $_POST['document_identitat'] ?? '';
$data_naixement = $_POST['data_naixement'] ?: null;
$lloc_naixement = $_POST['lloc_naixement'] ?? null;
$nacionalitat = $_POST['nacionalitat'] ?? null;
$residencia = $_POST['residencia'] ?? null;
$telefon = $_POST['telefon'] ?? null;
$email = $_POST['email'] ?? null;
$adreca = $_POST['adreca'] ?? null;
$contacte_emergencia = $_POST['contacte_emergencia'] ?? null;
$telefon_emergencia = $_POST['telefon_emergencia'] ?? null;
$compte_bancari = $_POST['compte_bancari'] ?? null;
$consentiment_rgpd = isset($_POST['consentiment_rgpd']) ? intval($_POST['consentiment_rgpd']) : 0;
$id_posicio = !empty($_POST['id_posicio']) ? intval($_POST['id_posicio']) : null;
$id_calendari_model = !empty($_POST['id_calendari_model']) ? intval($_POST['id_calendari_model']) : null;
$id_horari_model = !empty($_POST['id_horari_model']) ? intval($_POST['id_horari_model']) : null;

// --------------------------
// Gestió pujada foto
// --------------------------
$fotografia = null;
if (!empty($_FILES['fotografia']['name'])) {
    $target_dir = "uploads/";
    if(!is_dir($target_dir)) mkdir($target_dir, 0755, true);
    $target_file = $target_dir . basename($_FILES["fotografia"]["name"]);
    if (move_uploaded_file($_FILES["fotografia"]["tmp_name"], $target_file)) {
        $fotografia = $target_file;
    }
}

// --------------------------
// Consulta preparada
// --------------------------
$sql = "INSERT INTO Treballador (
    nom_complet, fotografia, document_identitat, data_naixement, lloc_naixement, nacionalitat,
    residencia, telefon, email, adreca, contacte_emergencia, telefon_emergencia,
    compte_bancari, consentiment_rgpd, id_posicio, id_calendari_model, id_horari_model
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
if (!$stmt) die("❌ Error en preparar la consulta: " . $conn->error);

$stmt->bind_param(
    "ssssssssssssssiii",
    $nom_complet, $fotografia, $document_identitat, $data_naixement, $lloc_naixement, $nacionalitat,
    $residencia, $telefon, $email, $adreca, $contacte_emergencia, $telefon_emergencia,
    $compte_bancari, $consentiment_rgpd, $id_posicio, $id_calendari_model, $id_horari_model
);

if ($stmt->execute()) {
    echo "<h2 style='color:green'>✅ Treballador guardat correctament!</h2>";
    echo "<p>ID assignat: " . $conn->insert_id . "</p>";
    if($fotografia) echo "<p>Foto pujada a: $fotografia</p>";
    echo "<p><a href='treballador.html'>Afegir un altre treballador</a></p>";
} else {
    echo "<p style='color:red'>❌ Error en guardar: " . $stmt->error . "</p>";
}

$stmt->close();
$conn->close();
?>
