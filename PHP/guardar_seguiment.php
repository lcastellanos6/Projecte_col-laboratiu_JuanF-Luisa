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
$id_plantacio = $_POST['id_plantacio'] ?? null;
$data_registre = $_POST['data_registre'] ?? '';
$estat_fenologic = $_POST['estat_fenologic'] ?? '';
$creixement_vegetatiu = $_POST['creixement_vegetatiu'] ?? '';
$incidencies_detectades = $_POST['incidencies_detectades'] ?? '';
$intervencions_realitzades = $_POST['intervencions_realitzades'] ?? '';
$estimacio = $_POST['estimacio_actualizada_collita'] ?? null;

// Validación básica
if (empty($id_sector) || empty($data_registre)) {
    echo "<p style='color:red;'>⚠️ Cal indicar el sector i la data de registre.</p>";
    exit;
}

// Preparar la inserción
$sql = "INSERT INTO Seguiment 
    (id_sector, data_registre, estat_fenologic, creixement_vegetatiu, incidencies_detectades, intervencions_realitzades, estimacio_actualizada_collita, id_plantacio)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
if (!$stmt) die("❌ Error en preparar la consulta: " . $conn->error);

// Bind de parámetros
$stmt->bind_param(
    "isssssdi",
    $id_sector,
    $data_registre,
    $estat_fenologic,
    $creixement_vegetatiu,
    $incidencies_detectades,
    $intervencions_realitzades,
    $estimacio,
    $id_plantacio
);

// Ejecutar
if ($stmt->execute()) {
    echo "<p style='color:green;'>✅ Seguiment guardat correctament!</p>";
    echo "<p>ID assignat: <b>" . $conn->insert_id . "</b></p>";
} else {
    echo "<p style='color:red;'>❌ Error en guardar el seguiment: " . $stmt->error . "</p>";
}

$stmt->close();
$conn->close();
?>