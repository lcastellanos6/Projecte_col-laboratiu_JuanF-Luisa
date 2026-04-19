<?php
session_start();
require_once __DIR__ . '/db.php';
$conn = db_connect();

$id_treballador = $_SESSION['id_treballador'] ?? 0;
$rol_usuari = $_SESSION['rol'] ?? 'usuari';

$stmt = $conn->prepare("
INSERT INTO tasca
(nom_tasca, tipus_tasca, data_inici, data_final, durada_estimada,
 personal_requerit, equipament_necessari, instruccions, dependencies, estat)
VALUES (?,?,?,?,?,?,?,?,?,?)
");

$stmt->bind_param(
  "sssssissss",
  $_POST['nom_tasca'],
  $_POST['tipus_tasca'],
  $_POST['data_inici'],
  $_POST['data_final'],
  $_POST['durada_estimada'],
  $_POST['personal_requerit'],
  $_POST['equipament_necessari'],
  $_POST['instruccions'],
  $_POST['dependencies'],
  $_POST['estat']
);

$stmt->execute();
$id_tasca = $stmt->insert_id;

// Si el creador es un trabajador, se la auto-asigna
if ($rol_usuari !== 'admin' && $id_treballador) {
    $data_avui = date('Y-m-d');
    $estat = $_POST['estat'];
    $stmt_as = $conn->prepare("INSERT INTO treballador_tasca (id_treballador, id_tasca, data_assignacio, estat) VALUES (?, ?, ?, ?)");
    $stmt_as->bind_param("iiss", $id_treballador, $id_tasca, $data_avui, $estat);
    $stmt_as->execute();
}

header("Location: tasca.php");
