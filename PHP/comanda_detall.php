<?php
session_start();
require_once __DIR__ . '/db.php';

if (!isset($_SESSION['rol'])) {
    header("Location: ../HTML/login.php");
    exit;
}

$conn = db_connect();

$id_comanda = $_GET['id'] ?? 0;

if (!$id_comanda) {
    die("ID de comanda no especificat.");
}

// Obtenir dades de la comanda
$sql_comanda = "SELECT c.*, cl.nom as nom_client 
                FROM comanda c 
                JOIN desti_client cl ON c.id_client = cl.id_client 
                WHERE c.id_comanda = ?";
$stmt = $conn->prepare($sql_comanda);
$stmt->bind_param("i", $id_comanda);
$stmt->execute();
$res_comanda = $stmt->get_result();
$comanda = $res_comanda->fetch_assoc();

if (!$comanda) {
    die("Comanda no trobada.");
}

// Obtenir detalls de la comanda
$sql_detalls = "SELECT d.*, l.codi_lot 
                FROM comanda_detall d 
                JOIN lot_produccio l ON d.id_lot = l.lot_id 
                WHERE d.id_comanda = ?";
$stmt_detalls = $conn->prepare($sql_detalls);
$stmt_detalls->bind_param("i", $id_comanda);
$stmt_detalls->execute();
$res_detalls = $stmt_detalls->get_result();
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Detall de Comanda #<?= $id_comanda ?></title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .container { max-width: 800px; margin: 20px auto; padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f8f9fa; }
        .header-info { display: flex; justify-content: space-between; margin-bottom: 20px; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        .btn-tornar { display: inline-block; margin-top: 20px; padding: 10px 20px; background-color: #6c757d; color: white; text-decoration: none; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Detall de Comanda #<?= $id_comanda ?></h1>
        
        <div class="header-info">
            <div>
                <p><strong>Client:</strong> <?= htmlspecialchars($comanda['nom_client']) ?></p>
                <p><strong>Data:</strong> <?= date('d/m/Y', strtotime($comanda['data_comanda'])) ?></p>
            </div>
            <div>
                <p><strong>Estat:</strong> <?= htmlspecialchars($comanda['estat']) ?></p>
                <p><strong>Total Import:</strong> <?= number_format($comanda['total_import'], 2, ',', '.') ?> €</p>
            </div>
        </div>

        <?php if ($comanda['observacions']): ?>
            <p><strong>Observacions:</strong> <?= nl2br(htmlspecialchars($comanda['observacions'])) ?></p>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>Lot</th>
                    <th>Quantitat</th>
                    <th>Preu Unitari</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($d = $res_detalls->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($d['codi_lot']) ?></td>
                        <td><?= number_format($d['quantitat'], 2, ',', '.') ?></td>
                        <td><?= number_format($d['preu_unitari'], 2, ',', '.') ?> €</td>
                        <td><?= number_format($d['quantitat'] * $d['preu_unitari'], 2, ',', '.') ?> €</td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <a href="../HTML/comanda.php" class="btn-tornar">Tornar al llistat</a>
    </div>
</body>
</html>

<?php
$stmt->close();
$stmt_detalls->close();
$conn->close();
?>
