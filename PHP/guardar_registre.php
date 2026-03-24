<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "web";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Error en la connexió: " . $conn->connect_error);
}

// Dades
$id_varietat = $_POST['id_varietat'] ?? null;
$id_plantacio = !empty($_POST['id_plantacio']) ? $_POST['id_plantacio'] : null;
$id_parcela = !empty($_POST['id_parcela']) ? $_POST['id_parcela'] : null;
$data_plantacio = $_POST['data_plantacio'] ?? null;
$data_arrencada = !empty($_POST['data_arrencada']) ? $_POST['data_arrencada'] : null;
$rendiment = !empty($_POST['rendiment']) ? floatval($_POST['rendiment']) : null;
$problemes = !empty($_POST['problemes_fitosanitaris']) ? $_POST['problemes_fitosanitaris'] : null;

// Validació
if (!$id_varietat || !$data_plantacio) {
    echo "Falten camps obligatoris";
    exit;
}

// SQL amb id_parcela
$stmt = $conn->prepare("
    INSERT INTO registre
    (id_varietat, id_plantacio, data_plantacio, data_arrencada, rendiment, problemes_fitosanitaris, id_parcela)
    VALUES (?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "iissdsi",
    $id_varietat,
    $id_plantacio,
    $data_plantacio,
    $data_arrencada,
    $rendiment,
    $problemes,
    $id_parcela
);

if ($stmt->execute()) {
    echo "<h3>Registre guardat correctament!</h3>";
    echo "<a href='../HTML/registre.php'>Tornar</a>";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>