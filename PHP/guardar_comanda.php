<?php
require_once __DIR__ . '/db.php';
$conn = db_connect();

$id_client = $_POST['id_client'];
$data_comanda = $_POST['data_comanda'];
$observacions = !empty($_POST['observacions']) ? $_POST['observacions'] : null;
$lots_input = $_POST['lots'] ?? [];

$conn->begin_transaction();

try {
    // 1. Inserir capçalera de la comanda
    $stmt = $conn->prepare("INSERT INTO comanda (id_client, data_comanda, observacions, estat) VALUES (?, ?, ?, 'Pendent')");
    $stmt->bind_param("iss", $id_client, $data_comanda, $observacions);
    $stmt->execute();
    $id_comanda = $conn->insert_id;
    $stmt->close();

    $total_import = 0;

    // 2. Inserir detalls
    $stmt_detall = $conn->prepare("INSERT INTO comanda_detall (id_comanda, id_lot, quantitat, preu_unitari) VALUES (?, ?, ?, ?)");
    $stmt_update_lot = $conn->prepare("UPDATE lot_produccio SET quantitat = quantitat - ?, estat = IF(quantitat - ? <= 0, 'Venut', estat) WHERE lot_id = ?");

    foreach ($lots_input as $line) {
        $id_lot = $line['id_lot'];
        $quantitat = $line['quantitat'];
        $preu_unitari = $line['preu_unitari'];
        $total_import += ($quantitat * $preu_unitari);

        $stmt_detall->bind_param("iidd", $id_comanda, $id_lot, $quantitat, $preu_unitari);
        $stmt_detall->execute();

        // Actualitzar estoc del lot
        $stmt_update_lot->bind_param("ddi", $quantitat, $quantitat, $id_lot);
        $stmt_update_lot->execute();
    }

    // 3. Actualitzar import total
    $stmt_update_total = $conn->prepare("UPDATE comanda SET total_import = ? WHERE id_comanda = ?");
    $stmt_update_total->bind_param("di", $total_import, $id_comanda);
    $stmt_update_total->execute();

    $conn->commit();
    echo "<h3>Comanda registrada correctament!</h3>";
    echo "<p>Import total: <strong>" . number_format($total_import, 2, ',', '.') . " €</strong></p>";
    echo "<a href='../HTML/comanda.php'>Tornar</a>";

} catch (Exception $e) {
    $conn->rollback();
    echo "Error: " . $e->getMessage();
}

$conn->close();
?>
