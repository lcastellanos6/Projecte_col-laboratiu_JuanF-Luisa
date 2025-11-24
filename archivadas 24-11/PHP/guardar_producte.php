<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "prueba1";

// Connexió
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("❌ Error de connexió: " . $conn->connect_error);

// Rebre dades del formulari
$tipus = $_POST['tipus'] ?? '';
$nom_comercial = $_POST['nom_comercial'] ?? '';
$materia_activa = $_POST['materia_activa'] ?? null;
$concentracio = $_POST['concentracio'] ?? null;
$espectre_accio = $_POST['espectre_accio'] ?? null;
$cultius_autoritzats = $_POST['cultius_autoritzats'] ?? null;
$dosi_recomendada = $_POST['dosi_recomendada'] ?? null;
$dosi_maxima = $_POST['dosi_maxima'] ?? null;
$termini_seguretat_dies = !empty($_POST['termini_seguretat_dies']) ? intval($_POST['termini_seguretat_dies']) : null;
$classificacio_toxicologica = $_POST['classificacio_toxicologica'] ?? null;
$restriccions_usu = $_POST['restriccions_usu'] ?? null;
$compatible_integrada = isset($_POST['compatible_integrada']) ? intval($_POST['compatible_integrada']) : 1;

// Validació bàsica
if (empty($tipus) || empty($nom_comercial)) {
    echo "<p style='color:red;'>⚠️ Cal omplir els camps obligatoris (Tipus i Nom comercial).</p>";
    exit;
}

// Consulta SQL
$sql = "INSERT INTO producte (
    tipus, nom_comercial, materia_activa, concentracio, espectre_accio, cultius_autoritzats,
    dosi_recomendada, dosi_maxima, termini_seguretat_dies, classificacio_toxicologica,
    restriccions_usu, compatible_integrada
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
if (!$stmt) die("❌ Error en preparar la consulta: " . $conn->error);

// Vincular paràmetres
$stmt->bind_param(
    "ssssssssissi",
    $tipus,
    $nom_comercial,
    $materia_activa,
    $concentracio,
    $espectre_accio,
    $cultius_autoritzats,
    $dosi_recomendada,
    $dosi_maxima,
    $termini_seguretat_dies,
    $classificacio_toxicologica,
    $restriccions_usu,
    $compatible_integrada
);

// Executar
if ($stmt->execute()) {
    echo "<p style='color:green;'>✅ Producte guardat correctament!</p>";
    echo "<p>ID assignat: <b>" . $conn->insert_id . "</b></p>";
} else {
    echo "<p style='color:red;'>❌ Error en guardar el producte: " . $stmt->error . "</p>";
}

$stmt->close();
$conn->close();
?>