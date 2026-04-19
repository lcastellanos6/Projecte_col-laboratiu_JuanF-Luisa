<?php
require_once __DIR__ . '/auth.php';
require_login();

// Només els administradors poden gestionar usuaris
if ($_SESSION['rol'] !== 'admin') {
    die("Accés denegat.");
}

$conn = db_connect();

// Obtenir llistat d'usuaris amb dades del treballador associat
$sql = "SELECT u.*, t.nom_complet as nom_treballador 
        FROM usuari u 
        LEFT JOIN treballador t ON u.id_treballador = t.id_treballador 
        ORDER BY u.usuari ASC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Gestió d'Usuaris</title>
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
        <div class="flex justify-between items-center">
            <div>
                <h2><i class="fa-solid fa-users-gear"></i> Gestió d'Usuaris</h2>
                <p class="page-subtitle">Administra els comptes d'accés al sistema i els seus rols.</p>
            </div>
            <a href="usuari_nou.php" class="btn btn-primary">
                <i class="fa-solid fa-user-plus"></i> Nou Usuari
            </a>
        </div>
    </div>

    <div class="panel">
        <table>
            <thead>
                <tr>
                    <th>Usuari</th>
                    <th>Rol</th>
                    <th>Treballador Associat</th>
                    <th>Estat</th>
                    <th>Data Creació</th>
                    <th>Accions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($u = $result->fetch_assoc()): ?>
                <tr>
                    <td>
                        <a href="usuari_editar.php?id=<?= $u['id_usuari'] ?>" class="text-primary font-bold">
                            <?= htmlspecialchars($u['usuari']) ?>
                        </a>
                    </td>
                    <td>
                        <span class="badge <?= $u['rol'] === 'admin' ? 'badge-danger' : 'badge-info' ?>">
                            <?= strtoupper($u['rol']) ?>
                        </span>
                    </td>
                    <td><?= htmlspecialchars($u['nom_treballador'] ?? '—') ?></td>
                    <td>
                        <span class="badge <?= $u['actiu'] ? 'badge-success' : 'badge-warning' ?>">
                            <?= $u['actiu'] ? 'ACTIU' : 'INACTIU' ?>
                        </span>
                    </td>
                    <td><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
                    <td>
                        <div class="flex gap-1">
                            <a href="usuari_editar.php?id=<?= $u['id_usuari'] ?>" class="btn btn-ghost btn-sm" title="Editar">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <button onclick="confirmarEliminar(<?= $u['id_usuari'] ?>, '<?= htmlspecialchars($u['usuari']) ?>')" class="btn btn-ghost btn-sm text-danger" title="Eliminar">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function confirmarEliminar(id, nom) {
    if (confirm(`Estàs segur que vols eliminar l'usuari "${nom}"? Aquesta acció no es pot desfer.`)) {
        window.location.href = `usuari_eliminar.php?id=${id}`;
    }
}
</script>
</body>
</html>
<?php $conn->close(); ?>
