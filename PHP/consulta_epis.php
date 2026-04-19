<?php
require_once __DIR__ . '/auth.php';
require_login();

$conn = db_connect();

$sql = "SELECT le.*, t.nom_complet as nom_treballador, et.nom as nom_epi
        FROM epi_lliurament le
        JOIN treballador t ON le.id_treballador = t.id_treballador
        JOIN epi_tipus et ON le.id_epi_tipus = et.id_epi_tipus
        ORDER BY le.data_lliurament DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Lliuraments d'EPIs</title>
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
                <h2><i class="fa-solid fa-helmet-safety"></i> Lliuraments d'EPIs</h2>
                <p class="page-subtitle">Control de material de protecció lliurat al personal.</p>
            </div>
            <a href="../HTML/lliurament_epis.php" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Nou Lliurament
            </a>
        </div>
    </div>

    <div class="panel">
        <div class="table-scroll">
        <table class="table">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Treballador</th>
                    <th>EPI</th>
                    <th>Quantitat</th>
                    <th>Talla</th>
                    <th>Caducitat</th>
                    <th>Accions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($row['data_lliurament'])) ?></td>
                        <td>
                            <a href="perfil_treballador.php?id=<?= $row['id_treballador'] ?>" class="text-primary font-bold">
                                <?= htmlspecialchars($row['nom_treballador']) ?>
                            </a>
                        </td>
                        <td><strong><?= htmlspecialchars($row['nom_epi']) ?></strong></td>
                        <td><?= htmlspecialchars($row['quantitat']) ?></td>
                        <td><?= htmlspecialchars($row['talla'] ?? '—') ?></td>
                        <td>
                            <?php if ($row['data_caducitat']): ?>
                                <span class="<?= strtotime($row['data_caducitat']) < time() ? 'badge badge-danger' : 'badge badge-info' ?>">
                                    <?= date('d/m/Y', strtotime($row['data_caducitat'])) ?>
                                </span>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="flex gap-1">
                                <a href="perfil_treballador.php?id=<?= $row['id_treballador'] ?>#epis" class="btn btn-ghost btn-sm" title="Veure al perfil">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="7">No hi ha lliuraments registrats.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>
</body>
</html>
<?php $conn->close(); ?>
