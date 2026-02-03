<?php
require_once __DIR__ . '/db.php';
$conn = db_connect();

$id_varietat = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id_varietat) {
    $conn->close();
    echo "<!DOCTYPE html>
    <html lang='ca'>
    <head>
        <meta charset='UTF-8'>
        <title>Error de varietat</title>
        <link rel='stylesheet' href='../HTML/styles.css'>
        <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css'>
    </head>
    <body>
      <div style='padding:15px; background:#f4fff4; border-bottom:1px solid #ddd; text-align:right;'>
        <a href='#' class='btn btn-primary' onclick='history.back(); return false;'><i class='fa-solid fa-arrow-left'></i> Tornar</a>
      </div>
      <div class='page'>
        <div class='page-header'>
          <h1>Error de varietat</h1>
        </div>
        <div class='panel'>
          <p>No s'ha pogut eliminar la varietat perquè l'identificador no és vàlid.</p>
          <a class='btn btn-ghost btn-full mt-2' href='consulta_cultius_varietats.php'>Tornar a la consulta</a>
        </div>
      </div>
    </body>
    </html>";
    exit;
}

$stmt = $conn->prepare('SELECT COUNT(*) AS total FROM plantacio WHERE id_varietat = ?');
$stmt->bind_param('i', $id_varietat);
$stmt->execute();
$res = $stmt->get_result();
$te_plantacions = false;
if ($res) {
    $row = $res->fetch_assoc();
    $te_plantacions = (int)($row['total'] ?? 0) > 0;
}
$stmt->close();

$stmt = $conn->prepare('SELECT COUNT(*) AS total FROM sector_varietat WHERE id_varietat = ?');
$stmt->bind_param('i', $id_varietat);
$stmt->execute();
$res = $stmt->get_result();
$te_sectors = false;
if ($res) {
    $row = $res->fetch_assoc();
    $te_sectors = (int)($row['total'] ?? 0) > 0;
}
$stmt->close();

if ($te_plantacions || $te_sectors) {
    $conn->close();
    header('Location: consulta_cultius_varietats.php?err=varietat_eliminar_dependencia');
    exit;
}

$stmt = $conn->prepare('DELETE FROM varietat WHERE id_varietat = ?');
$stmt->bind_param('i', $id_varietat);
if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    header('Location: consulta_cultius_varietats.php?msg=varietat_eliminada');
    exit;
}

$stmt->close();
$conn->close();
header('Location: consulta_cultius_varietats.php?err=varietat_eliminar');
exit;
?>
