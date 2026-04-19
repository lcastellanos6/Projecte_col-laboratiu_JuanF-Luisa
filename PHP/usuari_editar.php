<?php
require_once __DIR__ . '/auth.php';
require_login();

if ($_SESSION['rol'] !== 'admin') {
    die("Accés denegat.");
}

$conn = db_connect();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$res = $conn->query("SELECT * FROM usuari WHERE id_usuari = $id");
$u = $res->fetch_assoc();

if (!$u) {
    die("Usuari no trobat.");
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuari = trim($_POST['usuari']);
    $rol = $_POST['rol'];
    $id_treballador = !empty($_POST['id_treballador']) ? (int)$_POST['id_treballador'] : null;
    $actiu = isset($_POST['actiu']) ? 1 : 0;
    $nova_contrasenya = $_POST['nova_contrasenya'];

    if (empty($usuari)) {
        $error = "El nom d'usuari no pot estar buit.";
    } else {
        if (!empty($nova_contrasenya)) {
            $hash = password_hash($nova_contrasenya, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE usuari SET usuari=?, contrasenya_hash=?, rol=?, id_treballador=?, actiu=? WHERE id_usuari=?");
            $stmt->bind_param("sssiii", $usuari, $hash, $rol, $id_treballador, $actiu, $id);
        } else {
            $stmt = $conn->prepare("UPDATE usuari SET usuari=?, rol=?, id_treballador=?, actiu=? WHERE id_usuari=?");
            $stmt->bind_param("ssiii", $usuari, $rol, $id_treballador, $actiu, $id);
        }

        if ($stmt->execute()) {
            header("Location: consulta_usuaris.php?msg=editat");
            exit;
        } else {
            $error = "Error en actualitzar: " . $conn->error;
        }
    }
}

$treballadors = $conn->query("SELECT id_treballador, nom_complet FROM treballador ORDER BY nom_complet");
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuari</title>
    <link rel="stylesheet" href="../HTML/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<div class="page">
    <div class="mb-2">
        <a href="consulta_usuaris.php" class="btn btn-ghost">
            <i class="fa-solid fa-arrow-left"></i> Tornar al llistat
        </a>
    </div>

    <div class="page-header">
        <h2><i class="fa-solid fa-user-pen"></i> Editar Usuari: <?= htmlspecialchars($u['usuari']) ?></h2>
        <p class="page-subtitle">Modifica els permisos o la contrasenya de l'usuari.</p>
    </div>

    <?php if ($error): ?>
        <div class="badge badge-danger mb-2 w-full"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="panel">
        <form action="" method="post">
            <div class="form-grid-2">
                <div>
                    <label>Nom d'usuari *</label>
                    <input type="text" name="usuari" value="<?= htmlspecialchars($u['usuari']) ?>" required>
                </div>
                <div>
                    <label>Rol</label>
                    <select name="rol">
                        <option value="usuari" <?= $u['rol'] === 'usuari' ? 'selected' : '' ?>>Usuari (Consulta i dades)</option>
                        <option value="admin" <?= $u['rol'] === 'admin' ? 'selected' : '' ?>>Administrador (Control total)</option>
                    </select>
                </div>
                <div>
                    <label>Vincular a Treballador</label>
                    <select name="id_treballador">
                        <option value="">-- No vincular --</option>
                        <?php while ($t = $treballadors->fetch_assoc()): ?>
                            <option value="<?= $t['id_treballador'] ?>" <?= $u['id_treballador'] == $t['id_treballador'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($t['nom_complet']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="flex items-center gap-1" style="margin-top: 1.5rem;">
                    <input type="checkbox" name="actiu" id="actiu" style="width: auto;" <?= $u['actiu'] ? 'checked' : '' ?>>
                    <label for="actiu" style="margin: 0;">Usuari Actiu</label>
                </div>
            </div>

            <div class="mt-2">
                <label>Nova Contrasenya (deixa en blanc per mantenir l'actual)</label>
                <input type="password" name="nova_contrasenya">
            </div>

            <button type="submit" class="btn btn-primary btn-full mt-2">Actualitzar Usuari</button>
        </form>
    </div>
</div>
</body>
</html>
<?php $conn->close(); ?>
