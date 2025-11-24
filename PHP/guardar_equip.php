<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "web";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) die("Error de connexió: " . $conn->connect_error);

$nom = $_POST['nom'];
$id_departament = !empty($_POST['id_departament']) ? intval($_POST['id_departament']) : null;
$descripcio = $_POST['descripcio'] ?? null;

$sql = "INSERT INTO equip (nom, id_departament, descripcio) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sis", $nom, $id_departament, $descripcio);

if ($stmt->execute()) {
    echo "<p>Equip registrat correctament.</p>";
    echo "<a href='equip.html'>Tornar</a>";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
