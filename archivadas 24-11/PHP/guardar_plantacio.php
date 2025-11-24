<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "prueba1";

// Conexión
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("❌ Error de connexió: " . $conn->connect_error);

// Recoger datos del formulario
$id_sector = $_POST['id_sector'] ?? '';
$id_varietat = $_POST['id_varietat'] ?? '';
$data_plantacio = $_POST['data_plantacio'] ?? '';
$data_inici = $_POST['data_inici'] ?? '';
$data_fi = $_POST['data_fi'] ?? null;
$marc_files = $_POST['marc_plantacio_files'] ?? null;
$marc_arbres = $_POST['marc_plantacio_arbres'] ?? null;
$num_arbres = $_POST['num_arbres_total'] ?? null;
$origen_material = $_POST['origen_material'] ?? '';
$certificacions = $_POST['certificacions'] ?? '';
$sistema_formacio = $_POST['sistema_formacio'] ?? '';
$inversio_inicial = $_POST['inversio_inicial'] ?? null;
$entrada_produccio = $_POST['entrada_produccio_prevista'] ?? null;
$sistema_reg = $_POST['sistema_reg'] ?? 'Goteig';
$observacions = $_POST['observacions'] ?? '';

// Validación básica
if (empty($id_sector) || empty($id_varietat) || empty($data_plantacio) || empty($data_inici)) {
    echo "<p style='color:red;'>⚠️ Cal indicar sector, varietat, data de plantació i data d'inici.</p>";
    exit;
}

// Preparar la inserción
$sql = "INSERT INTO Plantacio 
    (id_sector, id_varietat, data_plantacio, data_inici, data_fi, marc_plantacio_files, marc_plantacio_arbres, num_arbres_total, origen_material, certificacions, sistema_formacio, inversio_inicial, entrada_produccio_prevista, sistema_reg, observacions)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
if (!$stmt) die("❌ Error en preparar la consulta: " . $conn->error);

// Bind de parámetros
$stmt->bind_param(
    "iissddiiissdiss",
    $id_sector,
    $id_varietat,
    $data_plantacio,
    $data_inici,
    $data_fi,
    $marc_files,
    $marc_arbres,
    $num_arbres,
    $origen_material,
    $certificacions,
    $sistema_formacio,
    $inversio_inicial,
    $entrada_produccio,
    $sistema_reg,
    $observacions
);

// Ejecutar
if ($stmt->execute()) {
    echo "<p style='color:green;'>✅ Plantació guardada correctament!</p>";
    echo "<p>ID assignat: <b>" . $conn->insert_id . "</b></p>";
} else {
    echo "<p style='color:red;'>❌ Error en guardar la plantació: " . $stmt->error . "</p>";
}

$stmt->close();
$conn->close();
?>