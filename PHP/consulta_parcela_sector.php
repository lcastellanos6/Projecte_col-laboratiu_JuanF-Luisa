<?php
// Connexió a la base de dades
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "web";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Error de connexió: " . $conn->connect_error);
}

// --- LLEGIR FILTRES DEL FORMULARI (GET) ---
$municipi        = $_GET['municipi']        ?? '';
$orientacio      = $_GET['orientacio']      ?? '';
$estat_productiu = $_GET['estat_productiu'] ?? '';
$superficie_min  = $_GET['superficie_min']  ?? '';
$superficie_max  = $_GET['superficie_max']  ?? '';

// --- CONSTRUIR LA CONSULTA DINÀMICA ---
$sql = "
SELECT 
    p.id_parcela,
    p.ref_cadastral,
    p.nom        AS nom_parcela,
    p.municipi,
    p.superficie AS sup_parcela,
    p.orientacio,
    p.tipus_sol,
    s.id_sector,
    s.nom        AS nom_sector,
    s.superficie AS sup_sector,
    s.estat_productiu
FROM parcela p
LEFT JOIN sector_parcela sp ON sp.id_parcela = p.id_parcela
LEFT JOIN sector s          ON s.id_sector   = sp.id_sector
";

$condicions = [];

// Municipi (LIKE, per poder escriure només part del nom)
if ($municipi !== '') {
    $municipi_esc = $conn->real_escape_string($municipi);
    $condicions[] = "p.municipi LIKE '%$municipi_esc%'";
}

// Orientació exacta
if ($orientacio !== '') {
    $orientacio_esc = $conn->real_escape_string($orientacio);
    $condicions[]   = "p.orientacio = '$orientacio_esc'";
}

// Estat productiu del sector
if ($estat_productiu !== '') {
    $estat_esc    = $conn->real_escape_string($estat_productiu);
    $condicions[] = "s.estat_productiu = '$estat_esc'";
}

// Rangs de superfície
if ($superficie_min !== '') {
    $superficie_min = floatval($superficie_min);
    $condicions[]   = "p.superficie >= $superficie_min";
}
if ($superficie_max !== '') {
    $superficie_max = floatval($superficie_max);
    $condicions[]   = "p.superficie <= $superficie_max";
}

// Afegim el WHERE si hi ha alguna condició
if (!empty($condicions)) {
    $sql .= " WHERE " . implode(" AND ", $condicions);
}

// Ordre per tenir-ho bonic
$sql .= " ORDER BY p.municipi, p.nom, s.nom";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Consulta de parcel·les i sectors</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 16px; }
        form { margin-bottom: 20px; }
        label { display: block; margin-top: 8px; font-weight: bold; }
        input, select { width: 100%; padding: 6px; }
        button { margin-top: 10px; padding: 8px 12px; }
        table { border-collapse: collapse; width: 100%; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        th { background: #eee; }
    </style>
</head>
<body>

<h2>Consulta de parcel·les i sectors</h2>

<form method="get">
    <label>Municipi</label>
    <input type="text" name="municipi" value="<?php echo htmlspecialchars($municipi); ?>">

    <label>Orientació</label>
    <select name="orientacio">
        <option value="">(Qualsevol)</option>
        <?php
        $opcions = ['N','S','E','O','NE','NO','SE','SO'];
        foreach ($opcions as $op) {
            $sel = ($op === $orientacio) ? 'selected' : '';
            echo "<option value=\"$op\" $sel>$op</option>";
        }
        ?>
    </select>

    <label>Estat productiu del sector</label>
    <select name="estat_productiu">
        <option value="">(Qualsevol)</option>
        <?php
        $estats = ['Repos','Plantat','Productiu','Reconvertit','Abandonat'];
        foreach ($estats as $e) {
            $sel = ($e === $estat_productiu) ? 'selected' : '';
            echo "<option value=\"$e\" $sel>$e</option>";
        }
        ?>
    </select>

    <label>Superfície mínima (ha)</label>
    <input type="number" step="0.01" name="superficie_min" value="<?php echo htmlspecialchars($superficie_min); ?>">

    <label>Superfície màxima (ha)</label>
    <input type="number" step="0.01" name="superficie_max" value="<?php echo htmlspecialchars($superficie_max); ?>">

    <button type="submit">Filtrar</button>
</form>

<?php
if ($result && $result->num_rows > 0): ?>
    <table>
        <tr>
            <th>Parcel·la</th>
            <th>Ref. cadastral</th>
            <th>Municipi</th>
            <th>Sup. parcel·la</th>
            <th>Orientació</th>
            <th>Tipus de sòl</th>
            <th>Sector</th>
            <th>Sup. sector</th>
            <th>Estat productiu</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['nom_parcela']); ?></td>
            <td><?php echo htmlspecialchars($row['ref_cadastral']); ?></td>
            <td><?php echo htmlspecialchars($row['municipi']); ?></td>
            <td><?php echo htmlspecialchars($row['sup_parcela']); ?></td>
            <td><?php echo htmlspecialchars($row['orientacio']); ?></td>
            <td><?php echo htmlspecialchars($row['tipus_sol']); ?></td>
            <td><?php echo htmlspecialchars($row['nom_sector']); ?></td>
            <td><?php echo htmlspecialchars($row['sup_sector']); ?></td>
            <td><?php echo htmlspecialchars($row['estat_productiu']); ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p>No hi ha resultats amb aquests filtres.</p>
<?php endif;

$conn->close();
?>

</body>
</html>
