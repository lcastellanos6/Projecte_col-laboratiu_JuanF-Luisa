<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "web";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Error de connexió: " . $conn->connect_error);
}

$id_producte = $_POST['id_producte'];
$numero_lot = $_POST['numero_lot'];
$data_caducitat = !empty($_POST['data_caducitat']) ? $_POST['data_caducitat'] : NULL;
$id_magatzem = !empty($_POST['id_magatzem']) ? $_POST['id_magatzem'] : NULL;
$quantitat_disponible = $_POST['quantitat_disponible'];
$unitat = $_POST['unitat'];
$fabricant = !empty($_POST['fabricant']) ? $_POST['fabricant'] : NULL;
$proveidor = !empty($_POST['proveidor']) ? $_POST['proveidor'] : NULL;
$data_compra = !empty($_POST['data_compra']) ? $_POST['data_compra'] : NULL;
$preu_unitari = !empty($_POST['preu_unitari']) ? $_POST['preu_unitari'] : NULL;

$stmt = $conn->prepare("
    INSERT INTO producte_lot (
        id_producte, numero_lot, data_caducitat, id_magatzem,
        quantitat_disponible, unitat, fabricant, proveidor,
        data_compra, preu_unitari
    ) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "isssdssssd",
    $id_producte, $numero_lot, $data_caducitat, $id_magatzem,
    $quantitat_disponible, $unitat, $fabricant, $proveidor,
    $data_compra, $preu_unitari
);

if ($stmt->execute()) {
    echo "<h3>Lot afegit correctament!</h3>";
    echo "<a href='../HTML/producte_lot.html'>Afegir un altre</a>";
} else {
    echo "Error: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
