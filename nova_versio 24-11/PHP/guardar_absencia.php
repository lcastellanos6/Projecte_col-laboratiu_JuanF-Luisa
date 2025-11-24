<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "web";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Error de connexió: " . $conn->connect_error);
}

$id_treballador = $_POST['id_treballador'];
$tipus = $_POST['tipus'];
$data_inici = $_POST['data_inici'];
$data_fi = $_POST['data_fi'];
$observacions = $_POST['observacions'] ?? null;

$justificant_path = null;

if (!empty($_FILES['justificacio_url']['name'])) {
    $upload_dir = "uploads/";
    if (!is_dir($upload_dir)) mkdir($upload_dir);

    $file_name = time() . "_" . basename($_FILES["justificacio_url"]["name"]);
    $file_path = $upload_dir . $file_name;

    if (move_uploaded_file($_FILES["justificacio_url"]["tmp_name"], $file_path)) {
        $justificant_path = $file_path;
    }
}

$sql = "INSERT INTO absencia 
        (id_treballador, tipus, data_inici, data_fi, justificacio_url, observacions)
        VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("isssss", $id_treballador, $tipus, $data_inici, $data_fi, $justificant_path, $observacions);

if ($stmt->execute()) {
    echo "<p>Absència registrada correctament.</p>";
    echo "<a href='absencia.html'>Tornar</a>";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
