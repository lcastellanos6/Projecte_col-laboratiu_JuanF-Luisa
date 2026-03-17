<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "web";

// Conexión a la base de datos
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connexió fallida: " . $conn->connect_error);
}

$nom = $_POST['nom'] ?? '';
$tipus = $_POST['tipus'] ?? '';

$id_estat_inici = !empty($_POST['id_estat_inici']) ? (int)$_POST['id_estat_inici'] : null;
$id_estat_fi = !empty($_POST['id_estat_fi']) ? (int)$_POST['id_estat_fi'] : null;
$id_varietat = !empty($_POST['id_varietat']) ? (int)$_POST['id_varietat'] : null;

$finestra_data_inici = !empty($_POST['finestra_data_inici']) ? $_POST['finestra_data_inici'] : null;
$finestra_data_fi = !empty($_POST['finestra_data_fi']) ? $_POST['finestra_data_fi'] : null;

$plaga_malaltia_objectiu = !empty($_POST['plaga_malaltia_objectiu']) ? $_POST['plaga_malaltia_objectiu'] : null;
$observacions = !empty($_POST['observacions']) ? $_POST['observacions'] : null;

// Preparar la consulta usando placeholders
$stmt = $conn->prepare("INSERT INTO pla_tractament 
    (nom, tipus, id_estat_inici, id_estat_fi, id_varietat, finestra_data_inici, finestra_data_fi, plaga_malaltia_objectiu, observacions)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

// Verificar si prepare falló
if (!$stmt) {
    die("Error en preparar consulta: " . $conn->error);
}

// Bind de parámetros
$stmt->bind_param(
    "ssiiissss",
    $nom,
    $tipus,
    $id_estat_inici,
    $id_estat_fi,
    $id_varietat,
    $finestra_data_inici,
    $finestra_data_fi,
    $plaga_malaltia_objectiu,
    $observacions
);

// Ejecutar la consulta
if ($stmt->execute()) {
    echo "<h3>Pla de tractament guardat correctament!</h3>";
    echo "<a href='../HTML/pla_tractament.php'>Tornar</a>";
} else {
    echo "Error al guardar: " . $stmt->error;
}

// Cerrar conexión
$stmt->close();
$conn->close();
?>



