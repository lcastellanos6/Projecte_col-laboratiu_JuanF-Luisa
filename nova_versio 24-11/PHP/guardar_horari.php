<?php
// Connexió a la base de dades
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "web";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("❌ Error de connexió: " . $conn->connect_error);

// Recollir valors del formulari
$nom = $_POST['nom'] ?? '';
$descripcio = $_POST['descripcio'] ?? null;
$hora_inici = !empty($_POST['hora_inici']) ? $_POST['hora_inici'] : null;
$hora_fi = !empty($_POST['hora_fi']) ? $_POST['hora_fi'] : null;
$pausa_minuts = isset($_POST['pausa_minuts']) ? intval($_POST['pausa_minuts']) : 0;

// Validació bàsica de coherència
if($hora_inici && $hora_fi && $hora_fi < $hora_inici){
    die("<p style='color:red'>❌ L'hora de fi no pot ser anterior a l'inici.</p>");
}

// Preparar consulta
$sql = "INSERT INTO Horari_model (nom, descripcio, hora_inici, hora_fi, pausa_minuts) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
if (!$stmt) die("❌ Error en preparar la consulta: " . $conn->error);

$stmt->bind_param("ssssi", $nom, $descripcio, $hora_inici, $hora_fi, $pausa_minuts);

if($stmt->execute()){
    echo "<p style='color:green'>✅ Horari guardat correctament!</p>";
    echo "<p>ID assignat: " . $conn->insert_id . "</p>";
    echo "<p><a href='horari.html'>Afegir un altre horari</a></p>";
}else{
    echo "<p style='color:red'>❌ Error: ".$stmt->error."</p>";
}

$stmt->close();
$conn->close();
?>
