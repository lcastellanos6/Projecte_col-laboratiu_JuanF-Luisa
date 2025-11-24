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
$tipus_contracte = $_POST['tipus_contracte'] ?? null;
$durada_contracte = $_POST['durada_contracte'] ?? null;
$categoria_professional = $_POST['categoria_professional'] ?? null;
$lloc_treball = $_POST['lloc_treball'] ?? null;
$data_incorporacio = $_POST['data_incorporacio'] ?? null;
$data_finalitzacio = $_POST['data_finalitzacio'] ?? null;
$historial_laboral = $_POST['historial_laboral'] ?? null;

$sql = "INSERT INTO contracte 
        (id_treballador, tipus_contracte, durada_contracte, categoria_professional, lloc_treball, data_incorporacio, data_finalitzacio, historial_laboral)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "isssssss",
    $id_treballador,
    $tipus_contracte,
    $durada_contracte,
    $categoria_professional,
    $lloc_treball,
    $data_incorporacio,
    $data_finalitzacio,
    $historial_laboral
);

if ($stmt->execute()) {
    echo "<p>Contracte registrat correctament.</p>";
    echo "<a href='contracte.html'>Tornar</a>";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
