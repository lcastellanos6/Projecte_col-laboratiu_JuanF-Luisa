<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "prueba1";

// Conexión
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("❌ Error de connexió: " . $conn->connect_error);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger datos del formulario
    $id_especie = intval($_POST['id_especie'] ?? 0);
    $nom_cientific = trim($_POST['nom_cientific'] ?? '');
    $nom_comu = trim($_POST['nom_comu'] ?? '');
    $caracteristiques_agronomiques = $_POST['caracteristiques_agronomiques'] ?? null;
    $cicle_vegetatiu = $_POST['cicle_vegetatiu'] ?? null;
    $requisits_pollinitzacio = $_POST['requisits_pollinitzacio'] ?? null;
    $productivitat_mitjana = !empty($_POST['productivitat_mitjana']) ? floatval($_POST['productivitat_mitjana']) : null;
    $qualitats_organoleptiques = $_POST['qualitats_organoleptiques'] ?? null;

    // Validación mínima
    if ($id_especie <= 0 || empty($nom_cientific) || empty($nom_comu)) {
        die("<p style='color:red; font-weight:bold;'>⚠️ ID de espècie, Nom científic i Nom comú són obligatoris.</p>");
    }

    // Validar que el ID de especie existe
    $stmt = $conn->prepare("SELECT id_especie FROM Especie WHERE id_especie = ?");
    $stmt->bind_param("i", $id_especie);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        die("<p style='color:red; font-weight:bold;'>⚠️ L'ID de espècie introduït no existeix a la taula Especie.</p>");
    }
    $stmt->close();

    // Insertar la variedad
    $sql = "INSERT INTO Varietat (
        id_especie, nom_cientific, nom_comu, caracteristiques_agronomiques, cicle_vegetatiu, requisits_pollinitzacio, productivitat_mitjana, qualitats_organoleptiques
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) die("❌ Error en preparar la consulta: " . $conn->error);

    // Todos los TEXT y DECIMAL se vinculan correctamente
    $stmt->bind_param(
        "issssdds",
        $id_especie,
        $nom_cientific,
        $nom_comu,
        $caracteristiques_agronomiques,
        $cicle_vegetatiu,
        $requisits_pollinitzacio,
        $productivitat_mitjana,
        $qualitats_organoleptiques
    );

    if ($stmt->execute()) {
        echo "<p style='color:green; font-weight:bold;'>✅ Varietat guardada correctament!</p>";
        echo "<p>ID assignat: <b>" . $conn->insert_id . "</b></p>";
    } else {
        echo "<p style='color:red;'>❌ Error en guardar la varietat: " . $stmt->error . "</p>";
    }

    $stmt->close();
    $conn->close();
}
?>