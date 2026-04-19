<?php
require_once __DIR__ . '/auth.php';
require_login();

if ($_SESSION['rol'] !== 'admin') {
    die("Accés denegat.");
}

$conn = db_connect();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuari = trim($_POST['usuari']);
    $contrasenya = $_POST['contrasenya'];
    $rol = $_POST['rol'];
    $id_treballador = !empty($_POST['id_treballador']) ? (int)$_POST['id_treballador'] : null;

    if (empty($usuari) || empty($contrasenya)) {
        $error = "L'usuari i la contrasenya són obligatoris.";
    } else {
        // Comprovar si l'usuari ja existeix
        $stmt = $conn->prepare("SELECT id_usuari FROM usuari WHERE usuari = ?");
        $stmt->bind_param("s", $usuari);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $error = "Aquest nom d'usuari ja està en ús.";
        } else {
            $hash = password_hash($contrasenya, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO usuari (usuari, contrasenya_hash, rol, id_treballador) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sssi", $usuari, $hash, $rol, $id_treballador);
            
            if ($stmt->execute()) {
                header("Location: consulta_usuaris.php?msg=creat");
                exit;
            } else {
                $error = "Error en crear l'usuari: " . $conn->error;
            }
        }
    }
}

$treballadors = $conn->query("SELECT id_treballador, nom_complet FROM treballador ORDER BY nom_complet");
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Nou Usuari</title>
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
        <h2><i class="fa-solid fa-user-plus"></i> Crear Nou Usuari</h2>
        <p class="page-subtitle">Assigna credencials d'accés a un treballador.</p>
    </div>

    <?php if ($error): ?>
        <div class="badge badge-danger mb-2 w-full"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="panel">
        <form action="" method="post">
            <div class="form-grid-2">
                <div>
                    <label>Nom d'usuari *</label>
                    <input type="text" name="usuari" required placeholder="Ex: joan.garcia">
                </div>
                <div>
                    <label>Contrasenya *</label>
                    <input type="password" name="contrasenya" required>
                </div>
                <div>
                    <label>Rol</label>
                    <select name="rol">
                        <option value="usuari">Usuari (Consulta i dades)</option>
                        <option value="admin">Administrador (Control total)</option>
                    </select>
                </div>
                <div>
                    <label>Vincular a Treballador</label>
                    <select name="id_treballador">
                        <option value="">-- No vincular --</option>
                        <?php while ($t = $treballadors->fetch_assoc()): ?>
                            <option value="<?= $t['id_treballador'] ?>"><?= htmlspecialchars($t['nom_complet']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-full mt-2">Crear Usuari</button>
        </form>
    </div>
</div>
</body>
</html>
<?php $conn->close(); ?>
