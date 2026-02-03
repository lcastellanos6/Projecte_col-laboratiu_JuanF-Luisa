<?php
require_once __DIR__ . '/db.php';
$conn = db_connect();

$id_especie = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id_especie) {
    $conn->close();
    echo "<!DOCTYPE html>
    <html lang='ca'>
    <head>
        <meta charset='UTF-8'>
        <title>Error d'espècie</title>
        <link rel='stylesheet' href='../HTML/styles.css'>
        <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css'>
    </head>
    <body>
      <div style='padding:15px; background:#f4fff4; border-bottom:1px solid #ddd; text-align:right;'>
        <a href='#' class='btn btn-primary' onclick='history.back(); return false;'><i class='fa-solid fa-arrow-left'></i> Tornar</a>
      </div>
      <div class='page'>
        <div class='page-header'>
          <h1>Error d'espècie</h1>
        </div>
        <div class='panel'>
          <p>No s'ha pogut eliminar l'espècie perquè l'identificador no és vàlid.</p>
          <a class='btn btn-ghost btn-full mt-2' href='consulta_cultius_varietats.php'>Tornar a la consulta</a>
        </div>
      </div>
    </body>
    </html>";
    exit;
}

$stmt = $conn->prepare('SELECT COUNT(*) AS total FROM varietat WHERE id_especie = ?');
$stmt->bind_param('i', $id_especie);
$stmt->execute();
$res = $stmt->get_result();
$te_varietats = false;
if ($res) {
    $row = $res->fetch_assoc();
    $te_varietats = (int)($row['total'] ?? 0) > 0;
}
$stmt->close();

if ($te_varietats) {
    $conn->close();
    header('Location: consulta_cultius_varietats.php?err=especie_eliminar_dependencia');
    exit;
}

$stmt = $conn->prepare('DELETE FROM especie WHERE id_especie = ?');
$stmt->bind_param('i', $id_especie);
if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    header('Location: consulta_cultius_varietats.php?msg=especie_eliminada');
    exit;
}

$stmt->close();
$conn->close();
header('Location: consulta_cultius_varietats.php?err=especie_eliminar');
exit;
?>
