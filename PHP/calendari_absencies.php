<?php
session_start();
require_once __DIR__ . '/db.php';
$conn = db_connect();

$id_treballador = $_SESSION['id_treballador'] ?? 0;
$rol_usuari = $_SESSION['rol'] ?? 'usuari';

if ($rol_usuari === 'admin') {
    $sql = "SELECT a.*, t.nom_complet
            FROM absencia a
            JOIN treballador t ON t.id_treballador = a.id_treballador
            ORDER BY a.data_inici";
} else {
    $sql = "SELECT a.*, t.nom_complet
            FROM absencia a
            JOIN treballador t ON t.id_treballador = a.id_treballador
            WHERE a.id_treballador = $id_treballador
            ORDER BY a.data_inici";
}
$res = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="ca">
<head>
<meta charset="UTF-8">
<title>Calendari d'Absències</title>
<link rel="stylesheet" href="styles.css">
<style>
.day {
    border: 1px solid #ccc;
    padding: 10px;
    margin-bottom: 15px;
}
.vacances { color: green; }
.baixa { color: red; }
.permis { color: orange; }
</style>
</head>
<body>

<h1>📅 Calendari d'Absències</h1>

<?php
$dia_actual = "";

while ($a = $res->fetch_assoc()):
    $dia = $a['data_inici'];

    if ($dia !== $dia_actual):
        if ($dia_actual) echo "</div>";
        echo "<div class='day'><h3>$dia</h3>";
        $dia_actual = $dia;
    endif;

    $classe = strtolower($a['tipus']);
?>
<p class="<?= $classe ?>">
    <strong><?= htmlspecialchars($a['nom_complet']) ?></strong> —
    <?= htmlspecialchars($a['tipus']) ?>
    (<?= $a['data_inici'] ?> → <?= $a['data_fi'] ?>)
</p>
<?php endwhile; if ($dia_actual) echo "</div>"; ?>

</body>
</html>
