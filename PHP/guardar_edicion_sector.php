<?php
$conn = new mysqli("localhost","root","","web");

$id = intval($_POST['id'] ?? 0);
$nom = $_POST['nom'] ?? '';
$superficie = !empty($_POST['superficie']) ? floatval($_POST['superficie']) : null;
$geometria_kml = $_POST['geometria_kml'] ?? '';
$foto_url = $_POST['foto_url'] ?? '';
$estat_productiu = $_POST['estat_productiu'] ?? 'Plantat';

if (empty(trim($geometria_kml))) {
    echo "<p style='color:red; font-weight:bold;'>⚠️ Cal indicar el KML del sector.</p>";
    echo "<p><a href='editar_sector.php?id=$id'>Tornar al formulari</a></p>";
    exit;
}

$stmt = $conn->prepare("UPDATE sector SET nom=?, superficie=?, geometria_kml=?, foto_url=?, estat_productiu=? WHERE id_sector=?");
$stmt->bind_param("sdsssi", $nom, $superficie, $geometria_kml, $foto_url, $estat_productiu, $id);
$stmt->execute();
$stmt->close();

header("Location: consulta_parcela_sector.php");
exit;
?>
