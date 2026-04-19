<?php
require_once __DIR__ . '/auth.php';
require_login();

$conn = db_connect();

$sql = "SELECT a.*, t.nom_complet as nom_treballador
        FROM absencia a
        JOIN treballador t ON a.id_treballador = t.id_treballador
        ORDER BY a.data_inici DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Gestió d'Absències</title>
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
                <h2><i class="fa-solid fa-calendar-times"></i> Gestió d'Absències</h2>
                <p class="page-subtitle">Baixes, vacances i permisos del personal.</p>
            </div>
            <a href="../HTML/absencia.php" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Nova Absència
            </a>
        </div>
    </div>

    <div class="panel">
        <table class="table">
            <thead>
                <tr>
                    <th>Treballador</th>
                    <th>Tipus</th>
                    <th>Inici</th>
                    <th>Fi</th>
                    <th>Estat</th>
                    <th>Observacions</th>
                    <th>Accions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <a href="perfil_treballador.php?id=<?= $row['id_treballador'] ?>" class="text-primary font-bold">
                                <?= htmlspecialchars($row['nom_treballador']) ?>
                            </a>
                        </td>
                        <td><strong><?= htmlspecialchars($row['tipus']) ?></strong></td>
                        <td><?= date('d/m/Y', strtotime($row['data_inici'])) ?></td>
                        <td><?= $row['data_fi'] ? date('d/m/Y', strtotime($row['data_fi'])) : 'Indeterminada' ?></td>
                        <td>
                            <?php
                            $badge = 'badge-info';
                            if ($row['estat'] === 'Aprovada') $badge = 'badge-success';
                            if ($row['estat'] === 'Rebutjada') $badge = 'badge-danger';
                            if ($row['estat'] === 'Pendent') $badge = 'badge-warning';
                            ?>
                            <span class="badge <?= $badge ?>"><?= strtoupper($row['estat']) ?></span>
                        </td>
                        <td><small><?= htmlspecialchars($row['observacions'] ?? '') ?></small></td>
                        <td>
                            <div class="flex gap-1">
                                <a href="perfil_treballador.php?id=<?= $row['id_treballador'] ?>" class="btn btn-ghost btn-sm">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="7">No hi ha absències registrades.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
<?php $conn->close(); ?>
