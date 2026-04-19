<?php
require_once __DIR__ . '/../PHP/auth.php';

ensure_session_started();

// Si ja està loguejat, redirigim a index.php
if (isset($_SESSION['usuari']) && !isset($_GET['logout'])) {
    header('Location: index.php');
    exit;
}

// Processament del formulari
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!ensure_users_table_exists()) {
        header('Location: login.php?error=install');
        exit;
    }
    ensure_default_admin_exists();

    $usuari = trim((string)($_POST['usuari'] ?? ''));
    $contrasenya = (string)($_POST['contrasenya'] ?? '');

    if ($usuari === '' || $contrasenya === '') {
        header('Location: login.php?error=required');
        exit;
    }

    $conn = db_connect();
    $stmt = $conn->prepare("SELECT id_usuari, usuari, contrasenya_hash, rol, actiu, id_treballador FROM usuari WHERE usuari = ? LIMIT 1");
    if (!$stmt) {
        $conn->close();
        header('Location: login.php?error=server');
        exit;
    }

    $stmt->bind_param('s', $usuari);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res ? $res->fetch_assoc() : null;
    $stmt->close();
    $conn->close();

    if (!$row || (int)($row['actiu'] ?? 0) !== 1) {
        header('Location: login.php?error=invalid');
        exit;
    }

    if (!password_verify($contrasenya, (string)($row['contrasenya_hash'] ?? ''))) {
        header('Location: login.php?error=invalid');
        exit;
    }

    $_SESSION['usuari'] = (string)$row['usuari'];
    $_SESSION['rol'] = (string)($row['rol'] ?? 'usuari');
    $_SESSION['id_usuari'] = (int)($row['id_usuari'] ?? 0);
    $_SESSION['id_treballador'] = $row['id_treballador'] ? (int)$row['id_treballador'] : null;

    header('Location: index.php');
    exit;
}

// Vista del formulari
?>
<!DOCTYPE html>
<html lang="ca">
<head>
  <meta charset="UTF-8">
  <title>Inicia sessió · Gestió de l'Explotació Fruiteres</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body class="login-page">
  <div class="login-layout">
    <div class="login-hero">
      <img class="login-logo" src="logo.png" alt="Logo Explotació">
      <h1>Inicia sessió</h1>
      <p>Accedeix al panell de Gestió de l'Explotació Fruiteres</p>
    </div>
    <div class="login-card">
        <?php
          $error = $_GET['error'] ?? '';
          $msg = '';
          if ($error === 'required') $msg = 'Completa usuari i contrasenya.';
          if ($error === 'invalid') $msg = 'Usuari o contrasenya incorrectes (o usuari inactiu).';
          if ($error === 'server') $msg = 'Error del servidor. Torna-ho a provar.';
          if ($error === 'install') $msg = 'No existeix la taula d\'usuaris a la BD. Importa `BBDD/web.sql`.';
        ?>
        <?php if ($msg !== ''): ?>
          <div style="margin-bottom:12px; padding:10px 12px; border:1px solid #e3b2b2; background:#fff5f5; color:#8a2a2a; border-radius:8px;">
            <?php echo htmlspecialchars($msg, ENT_QUOTES, 'UTF-8'); ?>
          </div>
        <?php endif; ?>
        <form action="login.php" method="post" class="app-login">
          <label for="usuari">Usuari</label>
          <input id="usuari" type="text" name="usuari" placeholder="Usuari" required autofocus>
          <label for="contrasenya">Contrasenya</label>
          <input id="contrasenya" type="password" name="contrasenya" placeholder="Contrasenya" required>
          <button type="submit" class="btn btn-primary btn-full">Entrar</button>
        </form>
    </div>
    <div class="login-back-link">
      <a href="../index.php">&larr; Tornar al panell</a>
    </div>
  </div>
</body>
</html>
