<?php

$conn = new mysqli("localhost", "root", "", "web");

if ($conn->connect_error) {
    die("Error de connexió: " . $conn->connect_error);
}

$conn->set_charset("utf8");

// DATOS
$id_pla = !empty($_POST['id_pla']) ? $_POST['id_pla'] : NULL;
$data = $_POST['data'];

$hora_inici = !empty($_POST['hora_inici']) ? $_POST['hora_inici'] : NULL;
$hora_fi = !empty($_POST['hora_fi']) ? $_POST['hora_fi'] : NULL;

$metode = !empty($_POST['metode']) ? $_POST['metode'] : NULL;
$condicions = !empty($_POST['condicions_ambientals']) ? $_POST['condicions_ambientals'] : NULL;

$id_operari = !empty($_POST['id_operari']) ? $_POST['id_operari'] : NULL;
$id_equip = !empty($_POST['id_equip']) ? $_POST['id_equip'] : NULL;

$observacions = !empty($_POST['observacions']) ? $_POST['observacions'] : NULL;

// VALIDACIÓN
if (empty($data)) {
    die("❌ La data és obligatòria");
}

// INSERT
$sql = "INSERT INTO aplicacio 
(id_pla, data, hora_inici, hora_fi, metode, condicions_ambientals, id_operari, id_equip, observacions)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Error SQL: " . $conn->error);
}

$stmt->bind_param(
    "isssssiss",
    $id_pla,
    $data,
    $hora_inici,
    $hora_fi,
    $metode,
    $condicions,
    $id_operari,
    $id_equip,
    $observacions
);

// EJECUTAR
if ($stmt->execute()) {
    echo "<h3 style='color:green;'>✅ Aplicació guardada correctament!</h3>";
    echo "<p>ID: <b>" . $conn->insert_id . "</b></p>";
    echo "<p><a href='consulta_aplicacions.php' style='display:inline-block;padding:10px 16px;background:#2f7d2f;color:#fff;text-decoration:none;border-radius:6px;font-weight:600;'>Veure aplicacions</a></p>";
    echo "<p><a href='../HTML/aplicacio.php' style='display:inline-block;padding:10px 16px;background:#e8f5e8;color:#2f4f2f;text-decoration:none;border:1px solid #c3d9c3;border-radius:6px;font-weight:600;'>🔙 Tornar</a></p>";
} else {

    echo "<p style='color:red;'>❌ Error: " . $stmt->error . "</p>";

}

$stmt->close();
$conn->close();
?>
