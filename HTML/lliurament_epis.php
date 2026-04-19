<?php
require_once __DIR__ . '/../PHP/db.php';
$conn = db_connect();

// Carregar treballadors
$treballadors = $conn->query("SELECT id_treballador, nom_complet FROM treballador ORDER BY nom_complet");

// Carregar tipus d'EPIs
$epis = $conn->query("SELECT id_epi_tipus, nom FROM epi_tipus ORDER BY nom");

// Carregar lliuraments recents
$sql_lliuraments = "SELECT l.*, t.nom_complet, et.nom as nom_epi 
                    FROM epi_lliurament l 
                    JOIN treballador t ON l.id_treballador = t.id_treballador 
                    JOIN epi_tipus et ON l.id_epi_tipus = et.id_epi_tipus 
                    ORDER BY l.data_lliurament DESC 
                    LIMIT 10";
$lliuraments = $conn->query($sql_lliuraments);
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Registrar Lliurament EPI</title>
    <link rel="stylesheet" href="styles.css">
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
        <h2><i class="fa-solid fa-hard-hat"></i> Registrar Lliurament EPI</h2>
    </div>

    <div class="panel">
        <form action="../PHP/guardar_epis.php" method="POST">
            <div class="form-grid-2">
                <div>
                    <label for="id_treballador">Treballador</label>
                    <select name="id_treballador" required>
                        <option value="">-- Selecciona un treballador --</option>
                        <?php while($t = $treballadors->fetch_assoc()): ?>
                            <option value="<?= $t['id_treballador'] ?>"><?= htmlspecialchars($t['nom_complet']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div>
                    <label for="id_epi_tipus">Tipus EPI</label>
                    <select name="id_epi_tipus" required>
                        <option value="">-- Selecciona un tipus d'EPI --</option>
                        <?php while($e = $epis->fetch_assoc()): ?>
                            <option value="<?= $e['id_epi_tipus'] ?>"><?= htmlspecialchars($e['nom']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div>
                    <label for="talla">Talla</label>
                    <input type="text" name="talla" placeholder="Ex: L, 42, M...">
                </div>

                <div>
                    <label for="quantitat">Quantitat</label>
                    <input type="number" name="quantitat" value="1" min="1">
                </div>

                <div>
                    <label for="data_lliurament">Data de lliurament</label>
                    <input type="date" name="data_lliurament" required value="<?= date('Y-m-d') ?>">
                </div>

                <div>
                    <label for="data_devolucio">Data de devolució</label>
                    <input type="date" name="data_devolucio">
                </div>

                <div>
                    <label for="data_caducitat">Data de caducitat</label>
                    <input type="date" name="data_caducitat">
                </div>

                <div>
                    <label for="document_signat_url">Document signat (URL)</label>
                    <input type="text" name="document_signat_url" placeholder="https://...">
                </div>
            </div>

            <label for="observacions">Observacions</label>
            <textarea name="observacions" rows="3"></textarea>

            <div class="mt-2">
                <button type="submit" class="btn btn-primary btn-full">
                    <i class="fa-solid fa-save"></i> Guardar Lliurament
                </button>
                <a href="javascript:history.back()" class="btn btn-ghost btn-full mt-1">
                    <i class="fa-solid fa-arrow-left"></i> Tornar
                </a>
            </div>
        </form>
    </div>

    <div class="panel mt-3">
        <h2 class="panel-title"><i class="fa-solid fa-clipboard-list"></i> Lliuraments recents</h2>
        <?php if ($lliuraments->num_rows === 0): ?>
            <p class="page-subtitle">No hi ha lliuraments registrats.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Treballador</th>
                        <th>EPI</th>
                        <th>Quantitat</th>
                        <th>Data</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($l = $lliuraments->fetch_assoc()): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($l['nom_complet']) ?></strong></td>
                        <td><?= htmlspecialchars($l['nom_epi']) ?></td>
                        <td><?= htmlspecialchars($l['quantitat']) ?></td>
                        <td><?= date('d/m/Y', strtotime($l['data_lliurament'])) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
<?php $conn->close(); ?>
