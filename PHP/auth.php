<?php
require_once __DIR__ . '/db.php';

function ensure_session_started(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
}

function redirect_to_login(): void
{
    header('Location: ../HTML/login.html');
    exit;
}

function require_login(): void
{
    ensure_session_started();
    if (empty($_SESSION['usuari'])) {
        redirect_to_login();
    }
}

function ensure_users_table_exists(): bool
{
    $conn = db_connect();
    $res = $conn->query("SHOW TABLES LIKE 'usuari'");
    $exists = $res && $res->num_rows > 0;
    if ($res) {
        $res->free();
    }

    if ($exists) {
        $conn->close();
        return true;
    }

    $sql = "
CREATE TABLE IF NOT EXISTS `usuari` (
  `id_usuari` int(11) NOT NULL AUTO_INCREMENT,
  `usuari` varchar(80) NOT NULL,
  `contrasenya_hash` varchar(255) NOT NULL,
  `rol` enum('admin','usuari') NOT NULL DEFAULT 'usuari',
  `actiu` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_usuari`),
  UNIQUE KEY `uq_usuari_usuari` (`usuari`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
";
    $ok = (bool)$conn->query($sql);
    $conn->close();
    return $ok;
}

function ensure_default_admin_exists(): void
{
    // Best-effort: create table if missing, then seed admin.
    if (!ensure_users_table_exists()) {
        return;
    }

    $conn = db_connect();

    $countRes = $conn->query("SELECT COUNT(*) FROM usuari");
    $row = $countRes ? $countRes->fetch_row() : null;
    $count = (int) ($row[0] ?? 0);
    if ($countRes) {
        $countRes->free();
    }

    if ($count === 0) {
        $hash = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO usuari (usuari, contrasenya_hash, rol, actiu) VALUES ('admin', ?, 'admin', 1)");
        if ($stmt) {
            $stmt->bind_param('s', $hash);
            $stmt->execute();
            $stmt->close();
        }
    }

    $conn->close();
}
?>
