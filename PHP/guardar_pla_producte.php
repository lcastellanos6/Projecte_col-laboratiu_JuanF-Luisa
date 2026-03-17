<?php
$conn = new mysqli("localhost", "root", "", "web");

if ($conn->connect_error) {
    die("Connexió fallida: " . $conn->connect_error);
}

$conn->set_charset("utf8");

// DATOS
$id_pla = $_POST['id_pla'];
$id_producte = $_POST['id_producte'];
$dosi = $_POST['dosi'];
$volum_caldo = !empty($_POST['volum_caldo']) ? $_POST['volum_caldo'] : NULL;

// VALIDACIÓN
if (empty($id_pla) || empty($id_producte) || empty($dosi)) {
    die("❌ Falten camps obligatoris");
}

// INSERT
$stmt = $conn->prepare("INSERT INTO pla_producte (id_pla, id_producte, dosi, volum_caldo) VALUES (?, ?, ?, ?)");

if (!$stmt) {
    die("Error SQL: " . $conn->error);
}

$stmt->bind_param("iiss", $id_pla, $id_producte, $dosi, $volum_caldo);

// EXECUTAR
if ($stmt->execute()) {
    echo "<h3 style='color:green;'>✅ Producte assignat correctament al pla!</h3>";
    echo "<a href='../HTML/pla_producte.php'>Tornar</a>";
} else {
    echo "<p style='color:red;'>❌ Error: " . $stmt->error . "</p>";
}

$stmt->close();
$conn->close();
?>
