<?php
// Connexió a la base de dades
$servername = "localhost";
$username   = "root";       // Canvia-ho si tens usuari diferent
$password   = "";           // Canvia-ho si uses contrasenya
$dbname     = "web";        // Nom de la BBDD

$conn = new mysqli($servername, $username, $password, $dbname);

// Comprovar connexió
if ($conn->connect_error) {
    die("Error de connexió: " . $conn->connect_error);
}

// Recollir dades del formulari
$nom = $_POST['nom'];
$descripcio = isset($_POST['descripcio']) ? $_POST['descripcio'] : NULL;

// Preparar INSERT
$sql = "INSERT INTO departament (nom, descripcio) VALUES (?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $nom, $descripcio);

if ($stmt->execute()) {
    echo "<h2>Departament registrat correctament.</h2>";
    echo "<a href='departament.html'>Tornar</a>";
} else {
    echo "<h2>Error: " . $stmt->error . "</h2>";
}

$stmt->close();
$conn->close();
?>
