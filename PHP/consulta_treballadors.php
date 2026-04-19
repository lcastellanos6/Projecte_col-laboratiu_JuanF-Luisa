<?php
session_start();
require_once __DIR__ . '/db.php';
$conn = db_connect();

// Restricció de rol: només administradors poden veure el llistat complet
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    die("Accés denegat. Només els administradors poden veure el llistat de treballadors.");
}

// Cerca si s'ha passat algun paràmetre de filtrat
$search = $_GET['search'] ?? '';
$where = "";
if ($search) {
    $search_safe = $conn->real_escape_string($search);
    $where = "WHERE t.nom_complet LIKE '%$search_safe%' OR t.document_identitat LIKE '%$search_safe%' OR p.nom LIKE '%$search_safe%'";
}

$sql = "SELECT t.*, p.nom as nom_posicio 
        FROM treballador t 
        LEFT JOIN posicio p ON t.id_posicio = p.id_posicio 
        $where
        ORDER BY t.nom_complet ASC";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Llistat de Treballadors</title>
    <link rel="stylesheet" href="../HTML/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<div class="page">
    <div class="mb-2">
        <a href="javascript:history.back()" class="btn btn-ghost">
            <i class="fa-solid fa-arrow-left"></i> Tornar
        </a>
    </div>
    <div class="page-header">
        <h2><i class="fa-solid fa-users"></i> Llistat de Treballadors</h2>
        <p class="page-subtitle">Gestió centralitzada de tot el personal de l'explotació.</p>
    </div>

    <div class="panel mb-2">
        <div class="flex justify-between items-center">
            <form action="" method="get" class="flex gap-1" style="flex: 1;">
                <input type="text" name="search" placeholder="Cerca per nom, DNI o posició..." value="<?= htmlspecialchars($search) ?>" style="flex: 1;">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
                <?php if ($search): ?>
                    <a href="consulta_treballadors.php" class="btn btn-ghost" title="Netejar cerca">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                <?php endif; ?>
            </form>
            <div class="ml-2">
                <a href="../HTML/treballador.php" class="btn btn-primary">
                    <i class="fa-solid fa-user-plus"></i> Nou Treballador
                </a>
            </div>
        </div>
    </div>

    <div class="panel">
        <?php if ($result->num_rows === 0): ?>
            <div style="padding: 3rem; text-align: center; color: #64748b;">
                <i class="fa-solid fa-users-slash" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.2;"></i>
                <p>No s'han trobat treballadors amb aquests filtres.</p>
                <a href="consulta_treballadors.php" class="text-primary">Netejar cerca</a>
            </div>
        <?php else: ?>
            <div class="table-scroll">
            <table class="table">
                <thead>
                    <tr>
                        <th>Treballador</th>
                        <th>DNI/NIE</th>
                        <th>Posició</th>
                        <th>Contacte</th>
                        <th>Estat</th>
                        <th>Accions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <div class="flex items-center gap-1">
                                <a href="perfil_treballador.php?id=<?= $row['id_treballador'] ?>">
                                    <img src="<?= $row['fotografia'] ? htmlspecialchars($row['fotografia']) : '../HTML/default-user.png' ?>" 
                                         alt="Foto" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid #fff; box-shadow: 0 0 0 1px #e2e8f0;">
                                </a>
                                <div>
                                    <a href="perfil_treballador.php?id=<?= $row['id_treballador'] ?>" class="text-primary font-bold">
                                        <?= htmlspecialchars($row['nom_complet']) ?>
                                    </a>
                                    <div style="font-size: 0.7rem; color: #64748b;">ID: #<?= $row['id_treballador'] ?></div>
                                </div>
                            </div>
                        </td>
                        <td><code style="font-size: 0.85rem;"><?= htmlspecialchars($row['document_identitat']) ?></code></td>
                        <td>
                            <span class="badge badge-info"><?= htmlspecialchars($row['nom_posicio'] ?? 'Sense definir') ?></span>
                        </td>
                        <td>
                            <div style="font-size: 0.85rem;">
                                <div><i class="fa-solid fa-phone fa-xs text-muted"></i> <?= htmlspecialchars($row['telefon'] ?? '—') ?></div>
                                <div style="color: #64748b;"><i class="fa-solid fa-envelope fa-xs text-muted"></i> <?= htmlspecialchars($row['email'] ?? '—') ?></div>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-success">ACTIU</span>
                        </td>
                        <td>
                            <div class="flex gap-1">
                                <a href="perfil_treballador.php?id=<?= $row['id_treballador'] ?>" class="btn btn-ghost btn-sm" title="Veure perfil">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="editar_treballador.php?id=<?= $row['id_treballador'] ?>" class="btn btn-ghost btn-sm" title="Editar">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <button onclick="confirmarEliminar(<?= $row['id_treballador'] ?>)" class="btn btn-ghost btn-sm text-danger" title="Eliminar">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function confirmarEliminar(id) {
    if (confirm('Estàs segur que vols eliminar aquest treballador? Aquesta acció no es pot desfer.')) {
        window.location.href = 'eliminar_treballador.php?id=' + id;
    }
}
</script>
</body>
</html>
<?php $conn->close(); ?>
