<?php
require_once __DIR__ . '/auth.php';
require_login();

$conn = db_connect();

$sql = "SELECT c.*, t.nom_complet as nom_treballador
        FROM contracte c
        JOIN treballador t ON c.id_treballador = t.id_treballador
        ORDER BY c.data_incorporacio DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Gestió de Contractes</title>
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
                <h2><i class="fa-solid fa-file-signature"></i> Gestió de Contractes</h2>
                <p class="page-subtitle">Control de la relació laboral amb el personal.</p>
            </div>
            <a href="../HTML/contracte.php" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Nou Contracte
            </a>
        </div>
    </div>

    <div class="panel">
        <table class="table">
            <thead>
                <tr>
                    <th>Treballador</th>
                    <th>Tipus Contracte</th>
                    <th>Incorporació</th>
                    <th>Finalització</th>
                    <th>Salari</th>
                    <th>Estat</th>
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
                        <td><strong><?= htmlspecialchars($row['tipus_contracte']) ?></strong></td>
                        <td><?= date('d/m/Y', strtotime($row['data_incorporacio'])) ?></td>
                        <td>
                            <?php if ($row['data_finalitzacio']): ?>
                                <span class="<?= strtotime($row['data_finalitzacio']) < time() ? 'badge badge-danger' : 'badge badge-info' ?>">
                                    <?= date('d/m/Y', strtotime($row['data_finalitzacio'])) ?>
                                </span>
                            <?php else: ?>
                                <span class="badge badge-success">Indefinit</span>
                            <?php endif; ?>
                        </td>
                        <td><?= number_format($row['salari_base'] ?? 0, 2, ',', '.') ?> €</td>
                        <td>
                            <?php if ($row['data_finalitzacio'] && strtotime($row['data_finalitzacio']) < time()): ?>
                                <span class="badge badge-danger">CADUCAT</span>
                            <?php else: ?>
                                <span class="badge badge-success">VIGENT</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="flex gap-1">
                                <a href="perfil_treballador.php?id=<?= $row['id_treballador'] ?>#laboral" class="btn btn-ghost btn-sm">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="7">No hi ha contractes registrats.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
<?php $conn->close(); ?>
