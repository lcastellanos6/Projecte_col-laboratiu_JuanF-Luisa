<?php
$conn = new mysqli("localhost", "root", "", "web");

if ($conn->connect_error) {
    die("Error de connexió: " . $conn->connect_error);
}

$conn->set_charset("utf8");

// DATOS
$id_aplicacio = $_POST['id_aplicacio'];
$id_producte = $_POST['id_producte'];
$quantitat = $_POST['quantitat'];
$unitat = $_POST['unitat'];

$concentracio = !empty($_POST['concentracio']) ? $_POST['concentracio'] : NULL;
$lot = !empty($_POST['lot_referencia']) ? $_POST['lot_referencia'] : NULL;

// VALIDACIÓN
if (empty($id_aplicacio) || empty($id_producte) || empty($quantitat)) {
    die("❌ Falten camps obligatoris");
}

// INSERT
$stmt = $conn->prepare("INSERT INTO aplicacio_producte 
(id_aplicacio, id_producte, quantitat, unitat, concentracio, lot_referencia)
VALUES (?, ?, ?, ?, ?, ?)");

if (!$stmt) {
    die("Error SQL: " . $conn->error);
}

$stmt->bind_param(
    "iidsss",
    $id_aplicacio,
    $id_producte,
    $quantitat,
    $unitat,
    $concentracio,
    $lot
);

// EXECUTAR
if ($stmt->execute()) {
    echo "<h3 style='color:green;'>✅ Producte afegit correctament!</h3>";
    echo "<a href='../HTML/aplicacio_productes.php'>Afegir un altre</a>";
} else {
    echo "<p style='color:red;'>❌ Error: " . $stmt->error . "</p>";
}

$stmt->close();
$conn->close();
?>
