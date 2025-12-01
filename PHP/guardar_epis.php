<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "web";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Error de connexió: " . $conn->connect_error);
}

$id_treballador = $_POST['id_treballador'];
$id_epi_tipus = $_POST['id_epi_tipus'];
$talla = $_POST['talla'] ?? null;
$quantitat = !empty($_POST['quantitat']) ? intval($_POST['quantitat']) : 1;
$data_lliurament = $_POST['data_lliurament'];
$data_devolucio = $_POST['data_devolucio'] ?? null;
$data_caducitat = $_POST['data_caducitat'] ?? null;
$document_signat_url = $_POST['document_signat_url'] ?? null;
$observacions = $_POST['observacions'] ?? null;

$sql = "INSERT INTO lliurament
(id_treballador, id_epi_tipus, talla, quantitat, data_lliurament, data_devolucio, data_caducitat, document_signat_url, observacions)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "iisisssss",
    $id_treballador,
    $id_epi_tipus,
    $talla,
    $quantitat,
    $data_lliurament,
    $data_devolucio,
    $data_caducitat,
    $document_signat_url,
    $observacions
);

if ($stmt->execute()) {
    echo "<p>Lliurament registrat correctament.</p>";
    echo "<a href='lliurament_epis.html'>Tornar</a>";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
