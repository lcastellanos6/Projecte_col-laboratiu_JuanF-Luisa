<?php
session_start();
require_once __DIR__ . '/db.php';

// Verificar sessió
if (!isset($_SESSION['usuari'])) {
    header("Location: ../HTML/login.php");
    exit;
}

$conn = db_connect();

// 1. Resum d'Estadístiques (KPIs)
$stats = [
    'parceles' => 0,
    'stock_alertes' => 0,
    'comandes_obertes' => 0,
    'treballadors' => 0,
    'tasques_pendents' => 0
];

// Comprovar parcel·les
$res = $conn->query("SELECT COUNT(*) as total FROM parcela");
if ($res) $stats['parceles'] = $res->fetch_assoc()['total'];

// Comprovar stock baix (Usem la lògica de la query única)
require_once __DIR__ . '/producte_stock_alerts.php';
$stockAlertsResult = get_producte_stock_alerts($conn);
$stats['stock_alertes'] = count($stockAlertsResult['alerts']);

// Comprovar comandes obertes
$res = $conn->query("SELECT COUNT(*) as total FROM comanda WHERE estat = 'Pendent' OR estat = 'En procés'");
if ($res) $stats['comandes_obertes'] = $res->fetch_assoc()['total'];

// Comprovar treballadors
$res = $conn->query("SELECT COUNT(*) as total FROM treballador");
if ($res) $stats['treballadors'] = $res->fetch_assoc()['total'];

// Comprovar tasques pendents
$res = $conn->query("SELECT COUNT(*) as total FROM tasca"); // Simplificat
if ($res) $stats['tasques_pendents'] = $res->fetch_assoc()['total'];


// 2. Properes tasques de tractament
$tractaments = [];
$sql_tract = "SELECT p.nom, p.finestra_data_inici, s.nom as sector 
              FROM pla_tractament p 
              JOIN pla_sector ps ON p.id_pla = ps.id_pla
              JOIN sector s ON ps.id_sector = s.id_sector 
              WHERE p.finestra_data_inici >= CURDATE() 
              ORDER BY p.finestra_data_inici ASC LIMIT 5";
$res_tract = $conn->query($sql_tract);
if ($res_tract) {
    while ($row = $res_tract->fetch_assoc()) {
        $tractaments[] = $row;
    }
}

// 3. Últims moviments de collita
$collites = [];
$sql_collites = "SELECT c.data_inici, c.quantitat_total, v.nom_comu 
                 FROM collita c 
                 JOIN plantacio p ON c.plantacio_id = p.id_plantacio
                 JOIN varietat v ON p.id_varietat = v.id_varietat 
                 ORDER BY c.data_inici DESC LIMIT 5";
$res_collites = $conn->query($sql_collites);
if ($res_collites) {
    while ($row = $res_collites->fetch_assoc()) {
        $collites[] = $row;
    }
}

?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - SIGA</title>
    <link rel="stylesheet" href="../HTML/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 0.75rem;
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 1rem;
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1);
        }
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        .stat-icon.primary { background: #e0f2fe; color: #0369a1; }
        .stat-icon.danger { background: #fee2e2; color: #dc2626; }
        .stat-icon.success { background: #dcfce7; color: #15803d; }
        .stat-icon.warning { background: #fef9c3; color: #854d0e; }
        .stat-icon.info { background: #f1f5f9; color: #475569; }

        .stat-content h3 {
            margin: 0;
            font-size: 0.875rem;
            color: #64748b;
            font-weight: 500;
        }
        .stat-value {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
        }
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 1.5rem;
        }
        @media (max-width: 500px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body class="page">

    <div class="page-header">
        <h1>Benvingut, <?= htmlspecialchars($_SESSION['usuari']) ?></h1>
        <p class="page-subtitle">Resum de l'estat de l'explotació per a avui, <?= date('d/m/Y') ?>.</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon primary"><i class="fa-solid fa-map-location-dot"></i></div>
            <div class="stat-content">
                <h3>Parcel·les</h3>
                <p class="stat-value"><?= $stats['parceles'] ?></p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon danger"><i class="fa-solid fa-triangle-exclamation"></i></div>
            <div class="stat-content">
                <h3>Alertes Estoc</h3>
                <p class="stat-value"><?= $stats['stock_alertes'] ?></p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon warning"><i class="fa-solid fa-cart-shopping"></i></div>
            <div class="stat-content">
                <h3>Comandes</h3>
                <p class="stat-value"><?= $stats['comandes_obertes'] ?></p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon success"><i class="fa-solid fa-users"></i></div>
            <div class="stat-content">
                <h3>Treballadors</h3>
                <p class="stat-value"><?= $stats['treballadors'] ?></p>
            </div>
        </div>
    </div>

    <div class="dashboard-grid">
        <!-- Properes Tasques -->
        <div class="panel">
            <div class="panel-header">
                <h3 class="panel-title"><i class="fa-solid fa-calendar-check"></i> Properes tasques de tractament</h3>
                <a href="../PHP/tractament.php" class="btn btn-ghost btn-sm" style="color: #2f7d2f; border-color: #2f7d2f;">Veure totes</a>
            </div>
            <?php if (empty($tractaments)): ?>
                <p class="page-subtitle">No hi ha tractaments programats.</p>
            <?php else: ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Tractament</th>
                            <th>Sector</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tractaments as $t): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($t['finestra_data_inici'])) ?></td>
                                <td><strong><?= htmlspecialchars($t['nom']) ?></strong></td>
                                <td><?= htmlspecialchars($t['sector']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- Últimes Collites -->
        <div class="panel">
            <div class="panel-header">
                <h3 class="panel-title"><i class="fa-solid fa-basket-shopping"></i> Últimes collites registrades</h3>
                <a href="../PHP/produccio.php" class="btn btn-ghost btn-sm" style="color: #2f7d2f; border-color: #2f7d2f;">Veure producció</a>
            </div>
            <?php if (empty($collites)): ?>
                <p class="page-subtitle">No s'han registrat collites recentment.</p>
            <?php else: ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Varietat</th>
                            <th>Quantitat</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($collites as $c): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($c['data_inici'])) ?></td>
                                <td><strong><?= htmlspecialchars($c['nom_comu']) ?></strong></td>
                                <td><?= htmlspecialchars($c['quantitat_total']) ?> kg</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
<?php $conn->close(); ?>
