<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "web";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("❌ Error de connexió: " . $conn->connect_error);
}

$nom = $_POST['nom_document'] ?? $_POST['nom'] ?? null;
$tipus_document = isset($_POST['tipus_document']) ? trim($_POST['tipus_document']) : null;
$data_emissio = !empty($_POST['data_emissio']) ? $_POST['data_emissio'] : null;
$data_caducitat = !empty($_POST['data_caducitat']) ? $_POST['data_caducitat'] : null;
$observacions= $_POST['observacions'] ?? $_POST['observacio'] ?? null;
$dni = isset($_POST['dni']) ? trim($_POST['dni']) : null;

// Validació d'entrada
if (!$nom) {
    http_response_code(400);
    echo "<p style='color:red;'>⚠️ Falta 'nom_document'.</p>";
    $conn->close();
    exit;
}
if (!$dni) {
    http_response_code(400);
    echo "Falta 'dni'.";
    $conn->close();
    exit;
}
// Validar tipus_document contra l'ENUM permès
$enumPermesos = [
    'DNI',
    'Contracte laboral',
    'Permís de treball',
    'Certificacio',
    'Reconeixement medic',
    'Formacio',
    'Document EPI',
    'Altres'
];
if ($tipus_document && !in_array($tipus_document, $enumPermesos, true)) {
    http_response_code(400);
    echo "Valor de 'tipus_document' no vàlid.";
    $conn->close();
    exit;
}

// Cercar id_treballador pel DNI
$sqlLookup = "SELECT id_treballador FROM treballador WHERE document_identitat = ?";
$stmt = $conn->prepare($sqlLookup);
if (!$stmt) {
    http_response_code(500);
    echo "<p style='color:red;'>❌ Error preparant consulta de cerca: </p>" . $conn->error;
    $conn->close();
    exit;
}
$stmt->bind_param("s", $dni);
if (!$stmt->execute()) {
    http_response_code(500);
    echo "<p style='color:red;'>❌ Error preparant consulta de cerca: </p>" . $stmt->error;
    $stmt->close();
    $conn->close();
    exit;
}
$result = $stmt->get_result();
if ($result === false || $result->num_rows === 0) {
    http_response_code(404);
    echo "<p style='color:red;'>❌No s'ha trobat cap treballador amb el DNI indicat.</p>";
    $stmt->close();
    $conn->close();
    exit;
}
$row = $result->fetch_assoc();
$id_treballador = (int)$row['id_treballador'];
$stmt->close();

// Gestionar pujada d'arxius (PDF o imatge)
$ruta_url_db = null;
if (isset($_FILES['ruta_url']) && $_FILES['ruta_url']['error'] !== UPLOAD_ERR_NO_FILE) {
    if ($_FILES['ruta_url']['error'] === UPLOAD_ERR_OK) {
        $tmp = $_FILES['ruta_url']['tmp_name'];
        $name = basename($_FILES['ruta_url']['name']);
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $allowed = ['pdf','jpg','jpeg','png','gif','webp'];
        if (!in_array($ext, $allowed, true)) {
            http_response_code(400);
            echo "Format de fitxer no permès.";
            $conn->close();
            exit;
        }
        // Carpeta de pujada relative al projecte
        $uploadDir = __DIR__ . '/../uploads';
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0775, true);
        }
        // Nom únic per evitar conflictes
        $unique = uniqid('doc_', true) . '.' . $ext;
        $dest = $uploadDir . '/' . $unique;
        if (!move_uploaded_file($tmp, $dest)) {
            http_response_code(500);
            echo "No s'ha pogut guardar l'arxiu pujat.";
            $conn->close();
            exit;
        }
        // Guardarem ruta relativa per servir-la després
        $ruta_url_db = 'uploads/' . $unique;
    } else {
        http_response_code(400);
        echo "Error en la pujada del fitxer.";
        $conn->close();
        exit;
    }
}

// Inserir registre amb la FK i camps extra
$sqlInsert = "INSERT INTO registre_document (id_treballador, tipus_document, nom_document, ruta_url, data_emissio, data_caducitat, observacions) VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sqlInsert);
if (!$stmt) {
    http_response_code(500);
    echo "<p style='color:red;'>❌Error preparant l'insert: </p>" . $conn->error;
    $conn->close();
    exit;
}
$stmt->bind_param(
    "issssss",
    $id_treballador,
    $tipus_document,
    $nom,
    $ruta_url_db,
    $data_emissio,
    $data_caducitat,
    $observacions
);

if ($stmt->execute()) {
    echo "<p style='color:green'>✅Tipus de documentació registrat correctament.</p>";
    echo "<a href='../HTML/documentacio.html'>Tornar</a>";
} else {
    echo "<p style='color:red;'>❌Error en l'insert: </p>" . $stmt->error;
}

$stmt->close();
$conn->close();
?>
