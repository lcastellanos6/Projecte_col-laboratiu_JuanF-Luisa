<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "web";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Error de connexió: " . $conn->connect_error);
}

$id_parcela = $_POST['id_parcela'];
$id_varietat = $_POST['id_varietat'];
$campanya_any = $_POST['campanya_any'];
$estimacio_produccio = $_POST['estimacio_produccio'];
$unitat = $_POST['unitat'];
$data_estimada_collita = $_POST['data_estimada_collita'];
$model_predictiu = $_POST['model_predictiu'];

$sql = "INSERT INTO previsio_produccio 
        (id_parcela, id_varietat, campanya_any, estimacio_produccio, unitat, data_estimada_collita, model_predictiu)
        VALUES 
        ('$id_parcela', '$id_varietat', '$campanya_any', '$estimacio_produccio', '$unitat', '$data_estimada_collita', '$model_predictiu')";

if ($conn->query($sql) === TRUE) {
    echo "Previsió de producció registrada correctament!";
} else {
    echo "Error en guardar: " . $conn->error;
}

$conn->close();
?>
