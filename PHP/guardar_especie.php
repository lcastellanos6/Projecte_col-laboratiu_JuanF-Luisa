<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "prueba1";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("❌ Error de connexió: " . $conn->connect_error);

// Rebre dades del formulari
$nom_cientific = $_POST['nom_cientific'] ?? '';
$nom_comu = $_POST['nom_comu'] ?? '';

// Consulta preparada
$sql = "INSERT INTO Especie (nom_cientific, nom_comu) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
if (!$stmt) die("❌ Error en preparar la consulta: " . $conn->error);

// Vincular paràmetres i executar
$stmt->bind_param("ss", $nom_cientific, $nom_comu);

if ($stmt->execute()) {
    echo "<p style='color:green; font-weight:bold;'>✅ Varietat guardada correctament!</p>";
    echo "<p>ID assignat: <b>" . $conn->insert_id . "</b></p>";
} else {
    echo "<p style='color:red;'>❌ Error en guardar la varietat: " . $stmt->error . "</p>";
}

$stmt->close();
$conn->close();
?>