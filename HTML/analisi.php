<?php
session_start();
require_once __DIR__ . '/../PHP/db.php';
$conn = db_connect();

$sectores = [];
$sql = "SELECT id_sector, nom FROM sector ORDER BY nom";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $sectores[] = $row;
    }
    $result->free();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Registrar Anàlisi Agronòmic</title>
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
        <h2><i class="fa-solid fa-microscope"></i> Registrar Nou Anàlisi Agronòmic</h2>
        <p class="page-subtitle">Sòl, Aigua o Foliar.</p>
    </div>

    <div class="panel">
        <form action="../PHP/guardar_analisi.php" method="post">
            <div class="grid-2 mb-2">
                <div>
                    <label>Sector *</label>
                    <select name="id_sector" required>
                        <option value="">Selecciona un sector...</option>
                        <?php foreach ($sectores as $sector): ?>
                            <option value="<?= $sector['id_sector'] ?>"><?= htmlspecialchars($sector['nom']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label>Data de l'Anàlisi *</label>
                    <input type="date" name="data_analisi" required value="<?= date('Y-m-d') ?>">
                </div>
            </div>

            <label>Tipus d'Anàlisi *</label>
            <select name="tipus" required class="mb-2">
                <option value="Sòl">Sòl</option>
                <option value="Aigua">Aigua</option>
                <option value="Foliar">Foliar</option>
            </select>

            <div class="grid-2 mb-2">
                <div>
                    <label>pH</label>
                    <input type="number" step="0.01" name="ph" placeholder="Ex: 7.2">
                </div>
                <div>
                    <label>Conductivitat (mS/cm)</label>
                    <input type="number" step="0.01" name="conductivitat" placeholder="Ex: 1.5">
                </div>
            </div>

            <div class="grid-2 mb-2">
                <div>
                    <label>Matèria Orgànica (%)</label>
                    <input type="number" step="0.01" name="materia_organica" placeholder="Ex: 2.5">
                </div>
                <div>
                    <label>Nitrogen (%)</label>
                    <input type="number" step="0.01" name="nitrogen" placeholder="Ex: 0.15">
                </div>
            </div>

            <div class="grid-2 mb-2">
                <div>
                    <label>Fòsfor (ppm)</label>
                    <input type="number" step="0.01" name="fosfor" placeholder="Ex: 45">
                </div>
                <div>
                    <label>Potassi (ppm)</label>
                    <input type="number" step="0.01" name="potassi" placeholder="Ex: 250">
                </div>
            </div>

            <label>URL del Document (PDF)</label>
            <input type="text" name="document_url" placeholder="https://..." class="mb-2">

            <label>Observacions</label>
            <textarea name="observacions" rows="3" placeholder="Notes de l'anàlisi..."></textarea>

            <button type="submit" class="btn btn-primary btn-full mt-3">
                <i class="fa-solid fa-save"></i> Guardar Anàlisi
            </button>
        </form>
    </div>
</div>
</body>
</html>
