<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "web";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Error de connexió: " . $conn->connect_error);
}

$id_aplicacio = $_POST['id_aplicacio'];
$id_producte = $_POST['id_producte'];
$quantitat = $_POST['quantitat'];
$unitat = $_POST['unitat'];
$concentracio = !empty($_POST['concentracio']) ? $_POST['concentracio'] : NULL;
$lot = !empty($_POST['lot_referencia']) ? $_POST['lot_referencia'] : NULL;

$stmt = $conn->prepare("INSERT INTO aplicacio_producte 
(id_aplicacio, id_producte, quantitat, unitat, concentracio, lot_referencia)
VALUES (?, ?, ?, ?, ?, ?)");

$stmt->bind_param("iidsss", 
    $id_aplicacio,
    $id_producte,
    $quantitat,
    $unitat,
    $concentracio,
    $lot
);

if ($stmt->execute()) {
    echo "<h3>Producte afegit correctament!</h3>";
    echo "<a href='../HTML/aplicacio_productes.html'>Afegir un altre</a>";
} else {
    echo "Error: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
