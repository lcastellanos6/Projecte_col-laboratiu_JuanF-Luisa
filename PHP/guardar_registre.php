<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "web";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Error en la connexió: " . $conn->connect_error);
}

$id_varietat = $_POST['id_varietat'];
$id_plantacio = !empty($_POST['id_plantacio']) ? $_POST['id_plantacio'] : null;
$data_plantacio = $_POST['data_plantacio'];
$data_arrencada = !empty($_POST['data_arrencada']) ? $_POST['data_arrencada'] : null;
$rendiment = !empty($_POST['rendiment']) ? $_POST['rendiment'] : null;
$problemes = !empty($_POST['problemes_fitosanitaris']) ? $_POST['problemes_fitosanitaris'] : null;

$sql = $conn->prepare("
    INSERT INTO registre
    (id_varietat, id_plantacio, data_plantacio, data_arrencada, rendiment, problemes_fitosanitaris)
    VALUES (?, ?, ?, ?, ?, ?)
");

$sql->bind_param("iissds", 
    $id_varietat, 
    $id_plantacio,
    $data_plantacio,
    $data_arrencada,
    $rendiment,
    $problemes
);

if ($sql->execute()) {
    echo "<h3>Registre guardat correctament!</h3>";
    echo "<a href='../HTML/registre.html'>Tornar</a>";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
