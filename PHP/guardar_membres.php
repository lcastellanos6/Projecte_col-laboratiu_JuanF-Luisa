<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "web";

$conn = new mysqli($host,$user,$pass,$dbname);
if($conn->connect_error) die("Error de connexió: ".$conn->connect_error);

$id_equip = intval($_POST['id_equip']);
$id_treballador = intval($_POST['id_treballador']);
$rol_equip = $_POST['rol_equip'] ?? null;
$data_alta = $_POST['data_alta'] ?? null;
$data_baixa = $_POST['data_baixa'] ?? null;

$sql = "INSERT INTO membres_del_equip (id_equip,id_treballador,rol_equip,data_alta,data_baixa) VALUES (?,?,?,?,?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iisss",$id_equip,$id_treballador,$rol_equip,$data_alta,$data_baixa);

if($stmt->execute()){
    echo "<p>Treballador assignat correctament a l'equip.</p>";
    echo "<a href='membres_equip.html'>Tornar</a>";
}else{
    echo "Error: ".$stmt->error;
}

$stmt->close();
$conn->close();
?>
