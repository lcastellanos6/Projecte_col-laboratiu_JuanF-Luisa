<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "web";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Error de connexió: " . $conn->connect_error);
}

$data_inici = $_POST['data_inici'];
$data_fi = !empty($_POST['data_fi']) ? $_POST['data_fi'] : NULL;
$plantacio_id = $_POST['plantacio_id'];
$quantitat_total = !empty($_POST['quantitat_total']) ? $_POST['quantitat_total'] : NULL;
$unitat = $_POST['unitat'];
$condicions_ambientals = !empty($_POST['condicions_ambientals']) ? $_POST['condicions_ambientals'] : NULL;
$id_estat = !empty($_POST['id_estat']) ? $_POST['id_estat'] : NULL;
$maduresa = !empty($_POST['maduresa']) ? $_POST['maduresa'] : NULL;
$incidencies = !empty($_POST['incidencies']) ? $_POST['incidencies'] : NULL;
$id_operari = !empty($_POST['id_operari']) ? $_POST['id_operari'] : NULL;

/* ✔️ Convertim id_maquinaria del HTML → id_equip de la base de dades */
$id_equip = !empty($_POST['id_maquinaria']) ? $_POST['id_maquinaria'] : NULL;

$stmt = $conn->prepare("
    INSERT INTO collita (
        data_inici, data_fi, plantacio_id, quantitat_total, unitat,
        condicions_ambientals, id_estat, maduresa, incidencies,
        id_operari, id_equip
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "sssdsisssii",
    $data_inici, $data_fi, $plantacio_id, $quantitat_total, $unitat,
    $condicions_ambientals, $id_estat, $maduresa, $incidencies,
    $id_operari, $id_equip
);

if ($stmt->execute()) {
    echo "<h3>Collita registrada correctament!</h3>";
    echo "<a href='collita.html'>Afegir una altra</a>";
} else {
    echo "Error: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
