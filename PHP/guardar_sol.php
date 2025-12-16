<?php
// Connexió a la base de dades
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "web";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar connexió
if ($conn->connect_error) {
    die("Error de connexió: " . $conn->connect_error);
}

// Recollir dades del formulari
$tipus = $_POST['tipus'];
$ph = !empty($_POST['ph']) ? $_POST['ph'] : null;
$materia_org = !empty($_POST['materia_organica']) ? $_POST['materia_organica'] : null;
$observacions = !empty($_POST['observacions']) ? $_POST['observacions'] : null;

// PREPARAR SQL
$sql = $conn->prepare("INSERT INTO sol (tipus, ph, materia_organica, observacions) VALUES (?, ?, ?, ?)");
$sql->bind_param("sdds", $tipus, $ph, $materia_org, $observacions);

// EXECUTAR
if ($sql->execute()) {
    echo "<h3>Sòl registrat correctament!</h3>";
    echo "<a href='./PROJECTE_/HTML/sol.html'>Tornar</a>";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>