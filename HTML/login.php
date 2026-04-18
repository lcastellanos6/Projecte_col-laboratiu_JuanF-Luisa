<?php
require_once __DIR__ . '/../PHP/auth.php';

ensure_session_started();
if (!ensure_users_table_exists()) {
    header('Location: login.html?error=install');
    exit;
}
ensure_default_admin_exists();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.html');
    exit;
}

$usuari = trim((string)($_POST['usuari'] ?? ''));
$contrasenya = (string)($_POST['contrasenya'] ?? '');

if ($usuari === '' || $contrasenya === '') {
    header('Location: login.html?error=required');
    exit;
}

$conn = db_connect();
$stmt = $conn->prepare("SELECT id_usuari, usuari, contrasenya_hash, rol, actiu FROM usuari WHERE usuari = ? LIMIT 1");
if (!$stmt) {
    $conn->close();
    header('Location: login.html?error=server');
    exit;
}

$stmt->bind_param('s', $usuari);
$stmt->execute();
$res = $stmt->get_result();
$row = $res ? $res->fetch_assoc() : null;
$stmt->close();
$conn->close();

if (!$row || (int)($row['actiu'] ?? 0) !== 1) {
    header('Location: login.html?error=invalid');
    exit;
}

if (!password_verify($contrasenya, (string)($row['contrasenya_hash'] ?? ''))) {
    header('Location: login.html?error=invalid');
    exit;
}

$_SESSION['usuari'] = (string)$row['usuari'];
$_SESSION['rol'] = (string)($row['rol'] ?? 'usuari');
$_SESSION['id_usuari'] = (int)($row['id_usuari'] ?? 0);

header('Location: index.php');
exit;
?>
