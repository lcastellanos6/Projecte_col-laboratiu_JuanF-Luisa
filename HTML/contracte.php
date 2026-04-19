<?php
require_once __DIR__ . '/../PHP/db.php';
$conn = db_connect();

// Carregar treballadors
$treballadors = $conn->query("SELECT id_treballador, nom_complet FROM treballador ORDER BY nom_complet");

// Carregar contractes recents
$sql_contractes = "SELECT c.*, t.nom_complet 
                   FROM contracte c 
                   JOIN treballador t ON c.id_treballador = t.id_treballador 
                   ORDER BY c.data_incorporacio DESC 
                   LIMIT 10";
$contractes = $conn->query($sql_contractes);
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Registrar Contracte</title>
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
        <h2><i class="fa-solid fa-file-signature"></i> Registrar Contracte</h2>
    </div>

    <div class="panel">
        <form action="../PHP/guardar_contracte.php" method="POST">
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
                    <label for="tipus_contracte">Tipus de contracte</label>
                    <select name="tipus_contracte">
                        <option value="Fix">Fix</option>
                        <option value="Temporal">Temporal</option>
                        <option value="Autonom">Autonom</option>
                        <option value="Altres">Altres</option>
                    </select>
                </div>

                <div>
                    <label for="durada_contracte">Durada del contracte</label>
                    <input type="text" name="durada_contracte" placeholder="Exemple: 6 mesos">
                </div>

                <div>
                    <label for="categoria_professional">Categoria professional</label>
                    <input type="text" name="categoria_professional" placeholder="Ex: Operari de camp">
                </div>

                <div>
                    <label for="lloc_treball">Lloc de treball</label>
                    <input type="text" name="lloc_treball" placeholder="Ex: Lleida">
                </div>

                <div>
                    <label for="data_incorporacio">Data d'incorporació</label>
                    <input type="date" name="data_incorporacio" value="<?= date('Y-m-d') ?>">
                </div>

                <div>
                    <label for="data_finalitzacio">Data de finalització</label>
                    <input type="date" name="data_finalitzacio">
                </div>
            </div>

            <label for="historial_laboral">Historial laboral</label>
            <textarea name="historial_laboral" rows="3"></textarea>

            <div class="mt-2">
                <button type="submit" class="btn btn-primary btn-full">
                    <i class="fa-solid fa-save"></i> Guardar Contracte
                </button>
                <a href="javascript:history.back()" class="btn btn-ghost btn-full mt-1">
                    <i class="fa-solid fa-arrow-left"></i> Tornar
                </a>
            </div>
        </form>
    </div>

    <div class="panel mt-3">
        <h2 class="panel-title"><i class="fa-solid fa-list-ul"></i> Contractes recents</h2>
        <?php if ($contractes->num_rows === 0): ?>
            <p class="page-subtitle">No hi ha contractes registrats.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Treballador</th>
                        <th>Tipus</th>
                        <th>Categoria</th>
                        <th>Inici</th>
                        <th>Fi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($c = $contractes->fetch_assoc()): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($c['nom_complet']) ?></strong></td>
                        <td><?= htmlspecialchars($c['tipus_contracte']) ?></td>
                        <td><?= htmlspecialchars($c['categoria_professional'] ?? '—') ?></td>
                        <td><?= date('d/m/Y', strtotime($c['data_incorporacio'])) ?></td>
                        <td><?= $c['data_finalitzacio'] ? date('d/m/Y', strtotime($c['data_finalitzacio'])) : 'Indefinit' ?></td>
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
