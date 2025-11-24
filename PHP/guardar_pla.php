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
$tipus = $_POST['tipus'];
$id_estat_inici = !empty($_POST['id_estat_inici']) ? $_POST['id_estat_inici'] : NULL;
$id_estat_fi = !empty($_POST['id_estat_fi']) ? $_POST['id_estat_fi'] : NULL;
$id_especie = !empty($_POST['id_especie']) ? $_POST['id_especie'] : NULL;
$finestra_data_inici = !empty($_POST['finestra_data_inici']) ? $_POST['finestra_data_inici'] : NULL;
$finestra_data_fi = !empty($_POST['finestra_data_fi']) ? $_POST['finestra_data_fi'] : NULL;
$plaga_malaltia_objectiu = !empty($_POST['plaga_malaltia_objectiu']) ? $_POST['plaga_malaltia_objectiu'] : NULL;
$observacions = !empty($_POST['observacions']) ? $_POST['observacions'] : NULL;

$sql = $conn->prepare("INSERT INTO pla_tractament 
    (nom, tipus, id_estat_inici, id_estat_fi, id_especie, finestra_data_inici, finestra_data_fi, plaga_malaltia_objectiu, observacions) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
$sql->bind_param("ssiiiisss", $nom, $tipus, $id_estat_inici, $id_estat_fi, $id_especie, $finestra_data_inici, $finestra_data_fi, $plaga_malaltia_objectiu, $observacions);

if ($sql->execute()) {
    echo "<h3>Pla de tractament guardat correctament!</h3>";
    echo "<a href='pla_tractament.html'>Tornar</a>";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
