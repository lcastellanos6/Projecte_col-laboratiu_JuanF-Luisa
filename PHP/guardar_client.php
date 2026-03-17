<?php
require_once __DIR__ . '/db.php';

$conn = db_connect();

$nom = trim($_POST['nom'] ?? '');
$cognoms_raw = trim($_POST['cognoms'] ?? '');
$nif_cif_raw = trim($_POST['nif_cif'] ?? '');
$telefon_raw = trim($_POST['telefon'] ?? '');
$email_raw = trim($_POST['email'] ?? '');
$adreca_raw = trim($_POST['adreca'] ?? '');
$observacions_raw = trim($_POST['observacions'] ?? '');

if ($nom === '') {
    $conn->close();
    echo "<p style='color:red; font-weight:bold;'>El nom és obligatori.</p>";
    exit;
}

$cognoms = $cognoms_raw !== '' ? $cognoms_raw : null;
$nif_cif = $nif_cif_raw !== '' ? $nif_cif_raw : null;
$telefon = $telefon_raw !== '' ? $telefon_raw : null;
$email = $email_raw !== '' ? $email_raw : null;
$adreca = $adreca_raw !== '' ? $adreca_raw : null;
$observacions = $observacions_raw !== '' ? $observacions_raw : null;

$stmt = $conn->prepare("
    INSERT INTO desti_client (nom, cognoms, nif_cif, telefon, email, adreca, observacions)
    VALUES (?, ?, ?, ?, ?, ?, ?)
");

if ($stmt) {
    $stmt->bind_param(
        'sssssss',
        $nom,
        $cognoms,
        $nif_cif,
        $telefon,
        $email,
        $adreca,
        $observacions
    );

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        echo "<h3>Client registrat correctament!</h3>";
        echo "<a href='../HTML/client.html'>Afegir un altre</a>";
        exit;
    }

    $error = $stmt->error;
    $stmt->close();
    $conn->close();
    echo "Error en guardar: " . htmlspecialchars($error);
    exit;
}

$conn->close();
echo "<p style='color:red; font-weight:bold;'>No s'ha pogut preparar la inserció.</p>";
?>
