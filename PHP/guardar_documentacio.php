<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "web";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("❌ Error de connexió: " . $conn->connect_error);
}

// Recollir dades del formulari
$nom = trim($_POST['nom_document'] ?? '');
$tipus_document = trim($_POST['tipus_document'] ?? '');
$data_emissio = $_POST['data_emissio'] ?? '';
$data_caducitat = $_POST['data_caducitat'] ?? '';
$observacions = trim($_POST['observacions'] ?? '');
$dni = trim($_POST['dni'] ?? '');

// Validació bàsica
if (!$nom || !$dni || !$data_emissio || !$data_caducitat) {
    die("<p style='color:red;'>⚠️ Falten camps obligatoris.</p>");
}

// Validar dates
if (strtotime($data_caducitat) <= strtotime($data_emissio)) {
    die("<p style='color:red;'>⚠️ La data de caducitat ha de ser posterior a la data d’emissió.</p>");
}

// Validar tipus_document
$enumPermesos = ['DNI','Contracte laboral','Permís de treball','Certificacio','Reconeixement medic','Formacio','Document EPI','Altres'];
if ($tipus_document && !in_array($tipus_document, $enumPermesos, true)) {
    die("<p style='color:red;'>Valor de 'tipus_document' no vàlid.</p>");
}

// Cercar id_treballador pel DNI
$sqlLookup = "SELECT id_treballador, nom, email FROM treballador WHERE document_identitat = ?";
$stmt = $conn->prepare($sqlLookup);
if (!$stmt) die("❌ Error en prepare(): " . $conn->error);

$stmt->bind_param("s", $dni);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("<p style='color:red;'>❌ No s'ha trobat cap treballador amb el DNI indicat.</p>");
}

$row = $result->fetch_assoc();
$id_treballador = (int)$row['id_treballador'];
$treballador_nom = $row['nom'];
$treballador_email = $row['email'];
$stmt->close();

// Pujada d'arxius
$ruta_url_db = null;
if (isset($_FILES['ruta_url']) && $_FILES['ruta_url']['error'] !== UPLOAD_ERR_NO_FILE) {
    if ($_FILES['ruta_url']['error'] === UPLOAD_ERR_OK) {
        $tmp = $_FILES['ruta_url']['tmp_name'];
        $name = basename($_FILES['ruta_url']['name']);
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $allowed = ['pdf','jpg','jpeg','png','gif','webp'];

        if (!in_array($ext, $allowed, true)) die("<p style='color:red;'>Format de fitxer no permès.</p>");

        $uploadDir = __DIR__ . '/../uploads';
        if (!is_dir($uploadDir)) @mkdir($uploadDir, 0775, true);

        $unique = uniqid('doc_', true) . '.' . $ext;
        $dest = $uploadDir . '/' . $unique;

        if (!move_uploaded_file($tmp, $dest)) die("<p style='color:red;'>No s'ha pogut guardar l'arxiu pujat.</p>");

        $ruta_url_db = 'uploads/' . $unique;
    } else {
        die("<p style='color:red;'>Error en la pujada del fitxer.</p>");
    }
}

// Inserir registre
$sqlInsert = "INSERT INTO registre_document (id_treballador, tipus_document, nom_document, ruta_url, data_emissio, data_caducitat, observacions) VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sqlInsert);
if (!$stmt) die("❌ Error en prepare(): " . $conn->error);

// Substituir nulls per string buit
$ruta_url_db = $ruta_url_db ?? '';
$observacions = $observacions ?? '';

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
    echo "<p style='color:green;'>✅ Tipus de documentació registrat correctament.</p>";
    echo "<a href='../HTML/documentacio.html'>Tornar</a>";

    // Alertar RRHH si el document caduca en menys de 30 dies
    if (strtotime($data_caducitat) - time() <= 30*24*60*60) {
        $to = "rrhh@empresa.com";
        $subject = "Alerta: Document proper a caducar";
        $message = "El document '$nom' del treballador $treballador_nom (DNI: $dni) caducarà el $data_caducitat.\nSi us plau, revisar i renovar si cal.";
        mail($to, $subject, $message);
    }

} else {
    die("<p style='color:red;'>❌ Error en l'insert: " . $stmt->error . "</p>");
}

$stmt->close();
$conn->close();
?>


