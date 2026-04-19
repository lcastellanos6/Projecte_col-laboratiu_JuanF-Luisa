<?php
session_start();
require_once __DIR__ . '/db.php';
$conn = db_connect();

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    die("Accés denegat.");
}

$id = $_GET['id'] ?? 0;
$stmt = $conn->prepare("SELECT * FROM treballador WHERE id_treballador = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$t = $stmt->get_result()->fetch_assoc();

if (!$t) {
    die("Treballador no trobat.");
}

$posicions = $conn->query("SELECT id_posicio, nom FROM posicio ORDER BY nom");
$calendaris = $conn->query("SELECT id_calendari_model, nom FROM calendari_model ORDER BY nom");
$horaris = $conn->query("SELECT id_horari_model, nom FROM horari_model ORDER BY nom");
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Editar Treballador</title>
    <link rel="stylesheet" href="../HTML/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<div class="page">
    <div class="page-header">
        <h2><i class="fa-solid fa-user-pen"></i> Editar Treballador: <?= htmlspecialchars($t['nom_complet']) ?></h2>
    </div>

    <div class="panel">
        <form action="actualitzar_treballador.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id_treballador" value="<?= $t['id_treballador'] ?>">
            <div class="form-grid-2">
                <div>
                    <label>Nom complet *</label>
                    <input type="text" name="nom_complet" required value="<?= htmlspecialchars($t['nom_complet']) ?>">
                </div>
                <div>
                    <label>Document d'identitat *</label>
                    <input type="text" name="document_identitat" required value="<?= htmlspecialchars($t['document_identitat']) ?>">
                </div>
                <div>
                    <label>Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($t['email'] ?? '') ?>">
                </div>
                <div>
                    <label>Telèfon</label>
                    <input type="text" name="telefon" value="<?= htmlspecialchars($t['telefon'] ?? '') ?>">
                </div>
                <div>
                    <label>Posició</label>
                    <select name="id_posicio">
                        <?php while($p = $posicions->fetch_assoc()): ?>
                            <option value="<?= $p['id_posicio'] ?>" <?= $t['id_posicio'] == $p['id_posicio'] ? 'selected' : '' ?>><?= htmlspecialchars($p['nom']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>
            <div class="mt-2">
                <button type="submit" class="btn btn-primary btn-full">Actualitzar dades</button>
                <a href="consulta_treballadors.php" class="btn btn-ghost btn-full mt-1">Cancel·lar</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
<?php $conn->close(); ?>
