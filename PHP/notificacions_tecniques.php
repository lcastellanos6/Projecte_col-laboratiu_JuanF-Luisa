<?php
require_once __DIR__ . '/alertes_logic.php';

$allAlerts = get_technical_alerts();
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Alertes Tècniques i Manteniment</title>
    <link rel="stylesheet" href="../HTML/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .badge-urgent { background-color: #f97316; color: white; }
        .badge-informativa { background-color: #3b82f6; color: white; }
    </style>
</head>
<body>
<div class="page">
    <div class="page-header">
        <h2><i class="fa-solid fa-bell-concierge"></i> Centre de Notificacions Tècniques</h2>
        <p class="page-subtitle">Seguiment de producció, plagues, manteniment i qualitat segons requeriments del projecte.</p>
    </div>

    <!-- Secció Alertes de Producció -->
    <div class="panel mb-3">
        <h3 class="panel-title"><i class="fa-solid fa-chart-line"></i> Alertes de Producció / Rendiment</h3>
        <?php if (empty($allAlerts['produccio'])): ?>
            <div class="badge badge-success mb-2"><i class="fa-solid fa-check"></i> Rendiments dins dels paràmetres normals.</div>
        <?php else: ?>
            <div class="table-scroll">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Sector</th>
                            <th>Incidència detectada</th>
                            <th>Nivell</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($allAlerts['produccio'] as $a): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($a['data'])) ?></td>
                                <td><?= htmlspecialchars($a['sector']) ?></td>
                                <td><?= htmlspecialchars($a['missatge']) ?></td>
                                <td><span class="badge badge-warning"><?= $a['nivell'] ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Secció Alertes de Plagues -->
    <div class="panel mb-3">
        <h3 class="panel-title"><i class="fa-solid fa-bug"></i> Control Fitosanitari (Plagues)</h3>
        <?php if (empty($allAlerts['plagues'])): ?>
            <div class="badge badge-success mb-2"><i class="fa-solid fa-check"></i> No s'han detectat focus crítics de plagues.</div>
        <?php else: ?>
            <div class="table-scroll">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Plaga</th>
                            <th>Sector</th>
                            <th>Captures</th>
                            <th>Acció Recomanada</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($allAlerts['plagues'] as $p): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($p['data'])) ?></td>
                                <td><strong><?= htmlspecialchars($p['plaga']) ?></strong></td>
                                <td><?= htmlspecialchars($p['sector']) ?></td>
                                <td><strong style="color: #dc2626;"><?= $p['captures'] ?></strong></td>
                                <td><span class="badge badge-danger">TRACTAMENT URGENT</span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Secció Alertes de Manteniment -->
    <div class="panel mb-3">
        <h3 class="panel-title"><i class="fa-solid fa-screwdriver-wrench"></i> Gestió de Maquinària i Equips</h3>
        <?php if (empty($allAlerts['manteniment'])): ?>
            <div class="badge badge-success mb-2"><i class="fa-solid fa-check"></i> Tota la maquinària està al dia.</div>
        <?php else: ?>
            <div class="table-scroll">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Data Prevista</th>
                            <th>Equip</th>
                            <th>Descripció de l'alerta</th>
                            <th>Prioritat</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($allAlerts['manteniment'] as $m): 
                            $badgeClass = 'badge-info';
                            if ($m['nivell'] == 'Urgent') $badgeClass = 'badge-urgent';
                            if ($m['nivell'] == 'Crític') $badgeClass = 'badge-danger';
                            if ($m['nivell'] == 'Informativa') $badgeClass = 'badge-informativa';
                        ?>
                            <tr>
                                <td><strong><?= date('d/m/Y', strtotime($m['data'])) ?></strong></td>
                                <td><?= htmlspecialchars($m['equip']) ?></td>
                                <td><?= htmlspecialchars($m['missatge']) ?></td>
                                <td><span class="badge <?= $badgeClass ?>"><?= $m['nivell'] ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Secció Alertes de Qualitat -->
    <div class="panel mb-3">
        <h3 class="panel-title"><i class="fa-solid fa-certificate"></i> Control de Qualitat i Traçabilitat</h3>
        <?php if (empty($allAlerts['qualitat'])): ?>
            <div class="badge badge-success mb-2"><i class="fa-solid fa-check"></i> Tots els lots compleixen els estàndards de qualitat.</div>
        <?php else: ?>
            <div class="table-scroll">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Codi Lot</th>
                            <th>Qualificació</th>
                            <th>Defectes detectats</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($allAlerts['qualitat'] as $q): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($q['data'])) ?></td>
                                <td><strong><?= htmlspecialchars($q['lot']) ?></strong></td>
                                <td><span class="badge badge-danger"><?= htmlspecialchars($q['qualificacio']) ?></span></td>
                                <td><?= htmlspecialchars($q['defectes']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <div class="mt-3">
        <a class="btn btn-ghost" href="../HTML/index.php">
            <i class="fa-solid fa-arrow-left"></i> Tornar al menú principal
        </a>
    </div>
</div>
</body>
</html>
