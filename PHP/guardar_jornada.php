<?php
// Mostrar errores (puedes quitarlo en producción)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Conexión BD
$conn = new mysqli("localhost", "root", "", "web");
if ($conn->connect_error) {
    die("Error BD: " . $conn->connect_error);
}

// Recoger datos
$id_treballador = $_POST['id_treballador'];
$data_inici     = $_POST['data_hora_inici'];
$data_fi        = $_POST['data_hora_fi'];
$minuts_pausa   = $_POST['minuts_pausa'] ?? 0;
$incidencies    = $_POST['incidencies'] ?? null;
$id_tasca       = $_POST['id_tasca'] ?: null;

// ================= FECHA =================
$dia = date('Y-m-d', strtotime($data_inici));
$any = date('Y', strtotime($dia));

// ================= FESTIVOS ESPAÑA + CATALUNYA =================
$festius = [
    "$any-01-01", // Año nuevo
    "$any-01-06", // Reyes
    "$any-05-01", // Trabajo
    "$any-08-15", // Asunción
    "$any-10-12", // Hispanidad
    "$any-11-01", // Todos los santos
    "$any-12-06", // Constitución
    "$any-12-08", // Inmaculada
    "$any-12-25", // Navidad

    // Catalunya
    "$any-06-24", // Sant Joan
    "$any-09-11", // Diada
    "$any-12-26"  // Sant Esteve
];

// ================= BLOQUEAR FESTIVOS =================
if (in_array($dia, $festius)) {
    echo "<script>
        alert('❌ No es pot registrar una jornada en un dia festiu');
        history.back();
    </script>";
    exit;
}


// ================= GUARDAR JORNADA =================
$stmt = $conn->prepare("
    INSERT INTO jornada
    (id_treballador, data_hora_inici, data_hora_fi, minuts_pausa, incidencies, id_tasca)
    VALUES (?,?,?,?,?,?)
");

$stmt->bind_param(
    "issisi",
    $id_treballador,
    $data_inici,
    $data_fi,
    $minuts_pausa,
    $incidencies,
    $id_tasca
);

if ($stmt->execute()) {
    echo "<script>
        alert('✅ Jornada registrada correctament');
        window.location.href = 'calendari_jornades.php';
    </script>";
} else {
    echo "<script>
        alert('❌ Error al registrar la jornada');
        history.back();
    </script>";
}
?>

