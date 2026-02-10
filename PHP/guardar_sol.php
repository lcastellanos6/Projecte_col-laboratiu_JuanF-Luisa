<?php
// Connexió a la base de dades
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "web";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar connexió
if ($conn->connect_error) {
    die("Error de connexió: " . $conn->connect_error);
}

// Recollir dades del formulari
$tipus = $_POST['tipus'];
$ph = !empty($_POST['ph']) ? $_POST['ph'] : null;
$materia_org = !empty($_POST['materia_organica']) ? $_POST['materia_organica'] : null;
$observacions = !empty($_POST['observacions']) ? $_POST['observacions'] : null;

function normalitza_tipus(string $valor): string {
    $valor = mb_strtolower($valor, 'UTF-8');
    $valor = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $valor);
    $valor = preg_replace('/[^a-z0-9 ]/', '', $valor ?? '');
    $valor = preg_replace('/\s+/', ' ', trim($valor));
    return $valor;
}

$tipus_norm = normalitza_tipus($tipus);
$duplicat = false;
$tipus_trobat = '';

$stmt = $conn->prepare("SELECT tipus FROM sol");
$stmt->execute();
$res = $stmt->get_result();
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $row_norm = normalitza_tipus($row['tipus'] ?? '');
        if ($row_norm !== '' && $row_norm === $tipus_norm) {
            $duplicat = true;
            $tipus_trobat = $row['tipus'] ?? '';
            break;
        }
    }
    $res->free();
}
$stmt->close();

if ($duplicat) {
    echo "<h3>Aquest tipus de sòl ja existeix.</h3>";
    if (!empty($tipus_trobat)) {
        $tipus_seg = htmlspecialchars($tipus_trobat, ENT_QUOTES, 'UTF-8');
        echo "<p>Ja registrat com a: <strong>{$tipus_seg}</strong></p>";
    }
    echo "<a href='../HTML/nou_sol.html'>Tornar</a>";
    $conn->close();
    exit;
}

// PREPARAR SQL
$sql = $conn->prepare("INSERT INTO sol (tipus, ph, materia_organica, observacions) VALUES (?, ?, ?, ?)");
$sql->bind_param("sdds", $tipus, $ph, $materia_org, $observacions);

// EXECUTAR
if ($sql->execute()) {
    echo "<h3>Sòl registrat correctament!</h3>";
    echo "<a href='../HTML/nou_sol.html'>Tornar</a>";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
