<?php
session_start();
require_once __DIR__ . '/db.php';
$conn = db_connect();

// 1) Dades per a la gràfica d'evolució de vendes (per mes)
$sqlVendes = "
    SELECT 
        DATE_FORMAT(data_comanda, '%Y-%m') as mes,
        SUM(total_import) as total
    FROM comanda
    WHERE estat != 'Cancel·lada'
    GROUP BY mes
    ORDER BY mes ASC
";
$resVendes = $conn->query($sqlVendes);
$labels = [];
$valors = [];
while ($row = $resVendes->fetch_assoc()) {
    $labels[] = $row['mes'];
    $valors[] = (float)$row['total'];
}

// 2) Top clients
$sqlClients = "
    SELECT c.nom, SUM(co.total_import) as total
    FROM comanda co
    JOIN desti_client c ON co.id_client = c.id_client
    WHERE co.estat != 'Cancel·lada'
    GROUP BY c.id_client
    ORDER BY total DESC
    LIMIT 5
";
$resClients = $conn->query($sqlClients);
$topClients = [];
while ($row = $resClients->fetch_assoc()) {
    $topClients[] = $row;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Evolució de Vendes</title>
    <link rel="stylesheet" href="../HTML/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="page">
    <div class="page-header">
        <h1><i class="fa-solid fa-chart-line"></i> Evolució de Vendes</h1>
    </div>

    <div class="grid-2">
        <div class="panel">
            <h3 class="panel-title">Ingressos Mensuals (€)</h3>
            <canvas id="graficaVendes"></canvas>
        </div>

        <div class="panel">
            <h3 class="panel-title">Top 5 Clients (Volum de Vendes)</h3>
            <table>
                <thead>
                    <tr><th>Client</th><th>Total Invertit</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($topClients as $c): ?>
                    <tr>
                        <td><?= htmlspecialchars($c['nom']) ?></td>
                        <td><strong><?= number_format($c['total'], 2, ',', '.') ?> €</strong></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
const labels = <?= json_encode($labels) ?>;
const dades = <?= json_encode($valors) ?>;

new Chart(document.getElementById('graficaVendes'), {
    type: 'line',
    data: {
        labels: labels,
        datasets: [{
            label: 'Ingressos (€)',
            data: dades,
            borderColor: '#2e7d32',
            backgroundColor: 'rgba(46, 125, 50, 0.1)',
            fill: true,
            tension: 0.3
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: { beginAtZero: true }
        }
    }
});
</script>
</body>
</html>
