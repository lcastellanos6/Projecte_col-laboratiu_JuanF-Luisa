<?php
session_start();
require_once __DIR__ . '/../PHP/db.php';
$conn = db_connect();

$clients = [];
$sql = "SELECT id_client, nom FROM desti_client ORDER BY nom";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $clients[] = $row;
    }
    $result->free();
}

$lots = [];
$sql = "SELECT lot_id, codi_lot, quantitat, unitat FROM lot_produccio WHERE estat != 'Venut' ORDER BY codi_lot";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $lots[] = $row;
    }
    $result->free();
}

$comandes = $conn->query("SELECT c.*, cl.nom as nom_client 
                          FROM comanda c 
                          JOIN desti_client cl ON c.id_client = cl.id_client 
                          ORDER BY c.data_comanda DESC LIMIT 10");

$conn->close();
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Nova Comanda</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script>
        function afegirLinia() {
            const container = document.getElementById('linies-container');
            const index = container.children.length;
            const div = document.createElement('div');
            div.className = 'panel mb-2';
            div.innerHTML = `
                <div class="grid-3">
                    <div>
                        <label>Lot *</label>
                        <select name="lots[${index}][id_lot]" required>
                            <option value="">Selecciona lot...</option>
                            <?php foreach ($lots as $lot): ?>
                                <option value="<?= $lot['lot_id'] ?>"><?= htmlspecialchars($lot['codi_lot']) ?> (Disp: <?= $lot['quantitat'] ?> <?= $lot['unitat'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label>Quantitat *</label>
                        <input type="number" step="0.01" name="lots[${index}][quantitat]" required>
                    </div>
                    <div>
                        <label>Preu Unitari (€) *</label>
                        <input type="number" step="0.01" name="lots[${index}][preu_unitari]" required>
                    </div>
                </div>
            `;
            container.appendChild(div);
        }
    </script>
</head>
<body>
<div class="page">
    <div class="mb-2">
        <a href="javascript:history.back()" class="btn btn-ghost">
            <i class="fa-solid fa-arrow-left"></i> Tornar
        </a>
    </div>
    <div class="page-header">
        <h2>Registrar Nova Comanda / Venda</h2>
        <p class="page-subtitle">Gestió de vendes i sortida de producte.</p>
    </div>

    <form action="../PHP/guardar_comanda.php" method="post">
        <div class="panel mb-2">
            <div class="grid-2 mb-2">
                <div>
                    <label>Client *</label>
                    <select name="id_client" required>
                        <option value="">Selecciona client...</option>
                        <?php foreach ($clients as $client): ?>
                            <option value="<?= $client['id_client'] ?>"><?= htmlspecialchars($client['nom']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label>Data de la Comanda *</label>
                    <input type="date" name="data_comanda" required value="<?= date('Y-m-d') ?>">
                </div>
            </div>

            <label>Observacions</label>
            <textarea name="observacions" rows="2" placeholder="Informació addicional sobre la comanda..."></textarea>
        </div>

        <h3>Detall de la Comanda</h3>
        <div id="linies-container">
            <!-- Les línies s'afegiran aquí -->
        </div>
        
        <div class="mt-2">
            <button type="button" class="btn btn-ghost" onclick="afegirLinia()">
                <i class="fa-solid fa-plus"></i> Afegir Producte/Lot
            </button>
        </div>

        <button type="submit" class="btn btn-primary btn-full mt-2">Registrar Comanda i Venda</button>
    </form>

    <div class="panel mt-3">
        <h2 class="panel-title"><i class="fa-solid fa-cart-flatbed"></i> Darreres comandes</h2>
        <?php if ($comandes->num_rows === 0): ?>
            <p class="page-subtitle">No hi ha comandes registrades.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Client</th>
                        <th>Total</th>
                        <th>Accions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($c = $comandes->fetch_assoc()): ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($c['data_comanda'])) ?></td>
                        <td><strong><?= htmlspecialchars($c['nom_client']) ?></strong></td>
                        <td><?= number_format($c['import_total'], 2) ?> €</td>
                        <td>
                            <a href="../PHP/comanda_detall.php?id=<?= $c['id_comanda'] ?>" class="btn btn-ghost btn-sm">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<script>
    // Afegir la primera línia automàticament
    window.onload = afegirLinia;
</script>
</body>
</html>
