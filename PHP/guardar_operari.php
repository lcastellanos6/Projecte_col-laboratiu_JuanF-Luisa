<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "web";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connexió fallida: " . $conn->connect_error);
}

$nom = $_POST['nom'];
$carnet_aplicador = !empty($_POST['carnet_aplicador']) ? $_POST['carnet_aplicador'] : NULL;
$dni_treballador = !empty($_POST['dni_treballador']) ? $_POST['dni_treballador'] : NULL;

$id_treballador = NULL;
if ($dni_treballador) {
    $stmt = $conn->prepare("SELECT id_treballador FROM Treballador WHERE document_identitat = ?");
    $stmt->bind_param("s", $dni_treballador);
    $stmt->execute();
    $stmt->bind_result($id_treballador);
    $stmt->fetch();
    $stmt->close();
}

$sql = $conn->prepare("INSERT INTO operari (nom, carnet_aplicador, id_treballador) VALUES (?, ?, ?)");
$sql->bind_param("ssi", $nom, $carnet_aplicador, $id_treballador);

if ($sql->execute()) {
    echo "<h3>Operari afegit correctament!</h3>";
    echo "<a href='../HTML/operari.html'>Tornar</a>";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
