<?php
require_once __DIR__ . '/db.php';
$conn = db_connect();

function ruta_retorn_segura(string $ruta): bool {
    if ($ruta === '') {
        return false;
    }
    $parts = parse_url($ruta);
    if ($parts === false) {
        return false;
    }
    return empty($parts['scheme']) && empty($parts['host']);
}

$id_sol = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id_sol) {
    $conn->close();
    echo "<p style='color:red; font-weight:bold;'>ID de sòl no vàlid.</p>";
    exit;
}

$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM sector_sol WHERE id_sol = ?");
$stmt->bind_param('i', $id_sol);
$stmt->execute();
$res = $stmt->get_result();
$total = 0;
if ($res) {
    $row = $res->fetch_assoc();
    $total = (int)($row['total'] ?? 0);
    $res->free();
}
$stmt->close();

if ($total > 0) {
    $conn->close();
    echo "<h3>No es pot eliminar aquest sòl perquè està assignat a sectors.</h3>";
    echo "<a href='#' onclick='history.back(); return false;'>Tornar</a>";
    exit;
}

$stmt = $conn->prepare("DELETE FROM sol WHERE id_sol = ?");
$stmt->bind_param('i', $id_sol);
$ok = $stmt->execute();
$stmt->close();
$conn->close();

if ($ok) {
    header("Location: consulta_sol.php");
    exit;
}

echo "<p style='color:red; font-weight:bold;'>No s'ha pogut eliminar el sòl.</p>";
?>
