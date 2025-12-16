<?php
include("connexio.php"); // connexió PDO o mysqli

$nom_tasca = $_POST['nom_tasca'];
$tipus_tasca = $_POST['tipus_tasca'] ?? null;
$id_sector = $_POST['id_sector'] ?? null;
$data_inici = $_POST['data_inici'] ?? null;
$data_final = $_POST['data_final'] ?? null;
$durada_estimada = $_POST['durada_estimada'] ?? null;
$personal_requerit = $_POST['personal_requerit'] ?? null;
$equipament_necessari = $_POST['equipament_necessari'] ?? null;
$instruccions = $_POST['instruccions'] ?? null;
$dependencies = $_POST['dependencies'] ?? null;
$estat = $_POST['estat'] ?? 'Planificada';
$id_aplicacio = $_POST['id_aplicacio'] ?? null;
$collita_id = $_POST['collita_id'] ?? null;

$sql = "INSERT INTO tasca (
    nom_tasca, tipus_tasca, id_sector, data_inici, data_final,
    durada_estimada, personal_requerit, equipament_necessari,
    instruccions, dependencies, estat, id_aplicacio, collita_id
) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)";

$stmt = $conn->prepare($sql);
$stmt->execute([
    $nom_tasca,
    $tipus_tasca,
    $id_sector,
    $data_inici,
    $data_final,
    $durada_estimada,
    $personal_requerit,
    $equipament_necessari,
    $instruccions,
    $dependencies,
    $estat,
    $id_aplicacio,
    $collita_id
]);

header("Location: ../tasca.html?ok=1");
exit;
?>
