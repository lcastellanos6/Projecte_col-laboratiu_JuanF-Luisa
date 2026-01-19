<?php
$conn = new mysqli("localhost", "root", "", "web");
if ($conn->connect_error) die("Error BD");

$res = $conn->query("
    SELECT a.*, t.nom_complet
    FROM absencia a
    JOIN treballador t ON t.id_treballador = a.id_treballador
    ORDER BY a.data_inici
");
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
