<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "web";

// Crear connexió
$conn = new mysqli($servername, $username, $password, $dbname);

// Comprovar connexió
if ($conn->connect_error) {
    die("Error en la connexió: " . $conn->connect_error);
}

$codi = $_POST['codi'];
$nom = $_POST['nom'];

// Preparar i executar la consulta
$sql = $conn->prepare("INSERT INTO estat_fenologic (codi, nom) VALUES (?, ?)");
$sql->bind_param("ss", $codi, $nom);

if ($sql->execute()) {
    echo "<h3>Estat fenològic guardat correctament!</h3>";
    echo "<a href='../HTML/estat_fenologic.html'>Tornar</a>";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
