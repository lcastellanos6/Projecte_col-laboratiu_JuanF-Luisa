<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "web";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Error de connexió: " . $conn->connect_error);
}

$nom = $_POST['nom'];
$descripcio = $_POST['descripcio'] ?? null;

$sql = "INSERT INTO documentacio (nom, descripcio) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $nom, $descripcio);

if ($stmt->execute()) {
    echo "<p>Tipus de documentació registrat correctament.</p>";
    echo "<a href='documentacio.html'>Tornar</a>";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
