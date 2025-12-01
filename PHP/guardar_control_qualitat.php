<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "web";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Error de connexió: " . $conn->connect_error);
}

$lot_id = $_POST['lot_id'];
$data_control = $_POST['data_control'];
$calibre = $_POST['calibre'];
$color = $_POST['color'];
$fermesa = $_POST['fermesa'];
$defectes = $_POST['defectes'];
$sabor = $_POST['sabor'];
$aroma = $_POST['aroma'];
$observacions = $_POST['observacions'];
$qualificacio_final = $_POST['qualificacio_final'];

$sql = "INSERT INTO control_qualitat 
        (lot_id, data_control, calibre, color, fermesa, defectes, sabor, aroma, observacions, qualificacio_final)
        VALUES 
        ('$lot_id', '$data_control', '$calibre', '$color', '$fermesa', '$defectes', '$sabor', '$aroma', '$observacions', '$qualificacio_final')";

if ($conn->query($sql) === TRUE) {
    echo "Control de qualitat registrat correctament!";
} else {
    echo "Error en guardar: " . $conn->error;
}

$conn->close();
?>
