<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "web";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connexió fallida: " . $conn->connect_error);
}

$id_pla = $_POST['id_pla'];
$id_producte = $_POST['id_producte'];
$dosi = $_POST['dosi'];
$volum_caldo = !empty($_POST['volum_caldo']) ? $_POST['volum_caldo'] : NULL;

$sql = $conn->prepare("INSERT INTO pla_producte (id_pla, id_producte, dosi, volum_caldo) VALUES (?, ?, ?, ?)");
$sql->bind_param("iiss", $id_pla, $id_producte, $dosi, $volum_caldo);

if ($sql->execute()) {
    echo "<h3>Producte assignat correctament al pla de tractament!</h3>";
    echo "<a href='../HTML/pla_producte.html'>Tornar</a>";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
