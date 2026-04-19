<?php
require_once __DIR__ . '/auth.php';
require_login();

$conn = db_connect();

$sql = "SELECT c.*, cl.nom as nom_client
        FROM comanda c
        JOIN desti_client cl ON c.id_client = cl.id_client
        ORDER BY c.data_comanda DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Gestió de Comandes i Vendes</title>
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
                <h2><i class="fa-solid fa-cart-shopping"></i> Gestió de Comandes</h2>
                <p class="page-subtitle">Historial de vendes i sortides de producte.</p>
            </div>
            <a href="../HTML/comanda.php" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Nova Venda
            </a>
        </div>
    </div>

    <div class="panel">
        <table class="table">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Client</th>
                    <th>Estat</th>
                    <th>Import Total</th>
                    <th>Observacions</th>
                    <th>Accions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($row['data_comanda'])) ?></td>
                        <td><strong><?= htmlspecialchars($row['nom_client']) ?></strong></td>
                        <td>
                            <?php
                            $badge = 'badge-info';
                            if ($row['estat'] === 'Lliurat') $badge = 'badge-success';
                            if ($row['estat'] === 'Cancel·lat') $badge = 'badge-danger';
                            if ($row['estat'] === 'Pendent') $badge = 'badge-warning';
                            ?>
                            <span class="badge <?= $badge ?>"><?= strtoupper($row['estat']) ?></span>
                        </td>
                        <td><?= number_format($row['total_import'], 2, ',', '.') ?> €</td>
                        <td><small><?= htmlspecialchars($row['observacions'] ?? '') ?></small></td>
                        <td>
                            <div class="flex gap-1">
                                <a href="comanda_detall.php?id=<?= $row['id_comanda'] ?>" class="btn btn-ghost btn-sm">
                                    <i class="fa-solid fa-list-check"></i> Veure detall
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6">No hi ha comandes registrades.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
<?php $conn->close(); ?>
