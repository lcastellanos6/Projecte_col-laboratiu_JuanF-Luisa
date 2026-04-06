<?php
require_once __DIR__ . '/producte_stock_alerts.php';

$stockAlertsResult = get_producte_stock_alerts();
$stockAlerts = $stockAlertsResult['alerts'];
$stockAlertsAvailable = (bool) $stockAlertsResult['available'];
$stockAlertsError = (string) $stockAlertsResult['error'];
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Notificacions de stock</title>
    <link rel="stylesheet" href="../HTML/styles.css">
</head>
<body>
<div class="page">
    <div class="page-header">
        <h1>Notificacions de stock minim</h1>
        <p class="page-subtitle">Detall de productes amb stock actual igual o inferior al stock minim.</p>
    </div>

    <div class="panel">
        <?php if (!$stockAlertsAvailable): ?>
            <p class="alert err">
                Alertes no disponibles: manca la columna <strong>producte.stock_minim</strong>.
                Executa la migracio SQL abans d'activar aquesta funcionalitat.
            </p>
            <p class="page-subtitle"><?php echo htmlspecialchars($stockAlertsError); ?></p>
        <?php elseif (empty($stockAlerts)): ?>
            <p class="alert ok">No hi ha productes amb stock per sota del minim.</p>
        <?php else: ?>
            <div class="table-scroll">
                <table class="table">
                    <thead>
                    <tr>
                        <th>ID producte</th>
                        <th>Nom comercial</th>
                        <th>Stock actual</th>
                        <th>Stock minim</th>
                        <th>Accions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($stockAlerts as $alert): ?>
                        <tr>
                            <td><?php echo (int) $alert['id_producte']; ?></td>
                            <td><?php echo htmlspecialchars($alert['nom_comercial']); ?></td>
                            <td><?php echo htmlspecialchars((string) $alert['stock_actual']); ?></td>
                            <td><?php echo (int) $alert['stock_minim']; ?></td>
                            <td>
                                <a href="producte_detall.php?id_producte=<?php echo (int) $alert['id_producte']; ?>">
                                    Veure detall
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <a class="btn btn-ghost mt-2" href="../HTML/index.php">Tornar al dashboard</a>
    </div>
</div>
</body>
</html>

