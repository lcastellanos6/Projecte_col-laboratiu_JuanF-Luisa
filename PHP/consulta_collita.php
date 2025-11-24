<?php
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "web";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Error de connexió: " . $conn->connect_error);
}

// --- CARREGAR LLISTES PER ALS SELECT (varietats i clients) ---
$varietats = [];
$resVar = $conn->query("SELECT id_varietat, nom_comu FROM varietat ORDER BY nom_comu");
if ($resVar) {
    while ($row = $resVar->fetch_assoc()) {
        $varietats[] = $row;
    }
}

$clients = [];
$resCli = $conn->query("SELECT id_client, nom FROM desti_client ORDER BY nom");
if ($resCli) {
    while ($row = $resCli->fetch_assoc()) {
        $clients[] = $row;
    }
}

// --- FILTRES ---
$any          = $_GET['any']          ?? '';
$id_varietat  = $_GET['id_varietat']  ?? '';
$id_client    = $_GET['id_client']    ?? '';
$estat_lot    = $_GET['estat_lot']    ?? '';

// --- CONSULTA PRINCIPAL DE COLLITES ---
$sql = "
SELECT 
    c.collita_id,
    c.data_inici,
    c.data_fi,
    c.quantitat_total,
    c.unitat,
    v.nom_comu    AS varietat,
    s.nom         AS sector,
    p.nom         AS parcela,
    lp.codi_lot,
    lp.estat      AS estat_lot,
    dc.nom        AS client
FROM collita c
JOIN plantacio pl        ON pl.id_plantacio = c.plantacio_id
JOIN sector s            ON s.id_sector     = pl.id_sector
JOIN sector_parcela sp   ON sp.id_sector    = s.id_sector
JOIN parcela p           ON p.id_parcela    = sp.id_parcela
JOIN varietat v          ON v.id_varietat   = pl.id_varietat
LEFT JOIN lot_produccio lp ON lp.collita_id = c.collita_id
LEFT JOIN desti_client dc  ON dc.id_client  = lp.id_client
";

$condicions = [];

// Any de la campanya (a partir de la data d'inici)
if ($any !== '') {
    $any_int     = intval($any);
    $condicions[] = "YEAR(c.data_inici) = $any_int";
}

// Varietat
if ($id_varietat !== '') {
    $id_varietat = intval($id_varietat);
    $condicions[] = "v.id_varietat = $id_varietat";
}

// Client
if ($id_client !== '') {
    $id_client   = intval($id_client);
    $condicions[] = "dc.id_client = $id_client";
}

// Estat del lot
if ($estat_lot !== '') {
    $estat_esc   = $conn->real_escape_string($estat_lot);
    $condicions[] = "lp.estat = '$estat_esc'";
}

if (!empty($condicions)) {
    $sql .= " WHERE " . implode(" AND ", $condicions);
}

$sql .= " ORDER BY c.data_inici DESC";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Consulta de collites</title>
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

<h2>Consulta de collites</h2>

<form method="get">
    <label>Any de campanya</label>
    <input type="number" name="any" value="<?php echo htmlspecialchars($any); ?>">

    <label>Varietat</label>
    <select name="id_varietat">
        <option value="">(Qualsevol)</option>
        <?php foreach ($varietats as $v): 
            $sel = ($v['id_varietat'] == $id_varietat) ? 'selected' : '';
        ?>
            <option value="<?php echo $v['id_varietat']; ?>" <?php echo $sel; ?>>
                <?php echo htmlspecialchars($v['nom_comu']); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Client / destí</label>
    <select name="id_client">
        <option value="">(Qualsevol)</option>
        <?php foreach ($clients as $c): 
            $sel = ($c['id_client'] == $id_client) ? 'selected' : '';
        ?>
            <option value="<?php echo $c['id_client']; ?>" <?php echo $sel; ?>>
                <?php echo htmlspecialchars($c['nom']); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Estat del lot</label>
    <select name="estat_lot">
        <option value="">(Qualsevol)</option>
        <?php
        $estatsLot = ['Emmagatzemat','En transport','Venut'];
        foreach ($estatsLot as $e) {
            $sel = ($e === $estat_lot) ? 'selected' : '';
            echo "<option value=\"$e\" $sel>$e</option>";
        }
        ?>
    </select>

    <button type="submit">Filtrar</button>
</form>

<?php if ($result && $result->num_rows > 0): ?>
    <table>
        <tr>
            <th>ID collita</th>
            <th>Data inici</th>
            <th>Data fi</th>
            <th>Varietat</th>
            <th>Parcel·la</th>
            <th>Sector</th>
            <th>Quantitat</th>
            <th>Unitat</th>
            <th>Codi lot</th>
            <th>Estat lot</th>
            <th>Client</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['collita_id']; ?></td>
            <td><?php echo $row['data_inici']; ?></td>
            <td><?php echo $row['data_fi']; ?></td>
            <td><?php echo htmlspecialchars($row['varietat']); ?></td>
            <td><?php echo htmlspecialchars($row['parcela']); ?></td>
            <td><?php echo htmlspecialchars($row['sector']); ?></td>
            <td><?php echo htmlspecialchars($row['quantitat_total']); ?></td>
            <td><?php echo htmlspecialchars($row['unitat']); ?></td>
            <td><?php echo htmlspecialchars($row['codi_lot']); ?></td>
            <td><?php echo htmlspecialchars($row['estat_lot']); ?></td>
            <td><?php echo htmlspecialchars($row['client']); ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p>No s'han trobat collites amb aquests filtres.</p>
<?php endif;

$conn->close();
?>

</body>
</html>
