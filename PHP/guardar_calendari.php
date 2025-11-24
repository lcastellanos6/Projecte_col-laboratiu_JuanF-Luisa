<?php
// Connexió a la base de dades
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "web";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("❌ Error de connexió: " . $conn->connect_error);

$nom = $_POST['nom'] ?? '';
$descripcio = $_POST['descripcio'] ?? null;

// Consulta preparada
$sql = "INSERT INTO calendari_model (nom, descripcio) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
if (!$stmt) die("❌ Error en preparar la consulta: " . $conn->error);

$stmt->bind_param("ss", $nom, $descripcio);

if($stmt->execute()){
    echo "<p style='color:green'>✅ Calendari guardat correctament!</p>";
    echo "<p>ID assignat: " . $conn->insert_id . "</p>";
    echo "<p><a href='calendari.html'>Afegir un altre calendari</a></p>";
}else{
    echo "<p style='color:red'>❌ Error: ".$stmt->error."</p>";
}

$stmt->close();
$conn->close();
?>
