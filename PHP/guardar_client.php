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
$cognoms = $_POST['cognoms'];
$nif_cif = $_POST['nif_cif'];
$telefon = $_POST['telefon'];
$email = $_POST['email'];
$adreca = $_POST['adreca'];
$observacions = $_POST['observacions'];

$sql = "INSERT INTO client (nom, cognoms, nif_cif, telefon, email, adreca, observacions)
        VALUES ('$nom', '$cognoms', '$nif_cif', '$telefon', '$email', '$adreca', '$observacions')";

if ($conn->query($sql) === TRUE) {
    echo "Client registrat correctament!";
} else {
    echo "Error en guardar: " . $conn->error;
}

$conn->close();
?>
