<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "web";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Error de connexió: " . $conn->connect_error);
}

$nom = $_POST['nom'];
$ubicacio = !empty($_POST['ubicacio']) ? $_POST['ubicacio'] : NULL;

$stmt = $conn->prepare("INSERT INTO magatzem (nom, ubicacio) VALUES (?, ?)");
$stmt->bind_param("ss", $nom, $ubicacio);

if ($stmt->execute()) {
    echo "<h3>Magatzem afegit correctament!</h3>";
    echo "<a href='../HTML/magatzem.html'>Afegir un altre</a>";
} else {
    echo "Error: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
