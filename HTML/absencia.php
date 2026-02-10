<?php
$conn = new mysqli("localhost", "root", "", "web");
if ($conn->connect_error) {
    die("Error de connexió: " . $conn->connect_error);
}

$treballadors = $conn->query("
    SELECT id_treballador, nom_complet
    FROM treballador
    ORDER BY nom_complet
");
?>
<!DOCTYPE html>
<html lang="ca">
<head>
<meta charset="UTF-8">
<title>Gestió d'Absències</title>
<link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="page">
<div class="page-header">
    <h1>Registrar absència</h1>
    <p class="page-subtitle">Controla vacances, baixes i altres absències.</p>
  <div class="page-header-actions">
      <a class="btn btn-primary" href="../PHP/calendari_mensual.php">Calendari absències</a>
    </div>

</div>

<div class="panel">
<form action="../PHP/guardar_absencia.php" method="POST" enctype="multipart/form-data">

    <label>Treballador</label>
    <select name="id_treballador" required>
        <option value="">-- Selecciona un treballador --</option>
        <?php while ($t = $treballadors->fetch_assoc()): ?>
            <option value="<?= $t['id_treballador'] ?>">
                <?= htmlspecialchars($t['nom_complet']) ?>
            </option>
        <?php endwhile; ?>
    </select>

    <label>Tipus d'absència</label>
    <select name="tipus" required>
        <option value="Vacances">Vacances</option>
        <option value="Permís">Permís</option>
        <option value="Baixa">Baixa</option>
        <option value="Altres">Altres</option>
    </select>

    <label>Data d'inici</label>
    <input type="date" name="data_inici" required>

    <label>Data de finalització</label>
    <input type="date" name="data_fi" required>

    <label>Justificant (PDF o imatge)</label>
    <input type="file" name="justificacio_url">

    <label>Observacions</label>
    <textarea name="observacions"></textarea>

    <button type="submit" class="btn btn-primary btn-full mt-2">
        Guardar Absència
    </button>

</form>
</div>
</div>

</body>
</html>
