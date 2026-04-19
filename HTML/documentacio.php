<?php
session_start();
if (!isset($_SESSION['rol'])) {
    die("Accés denegat.");
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
<meta charset="UTF-8">
<title>Registrar Tipus de Documentació</title>
<link rel="stylesheet" href="styles.css">
<style>
  .error { color: red; font-size: 0.9em; }
</style>
</head>
<body>
<div class="page">
    <div class="mb-2">
        <a href="javascript:history.back()" class="btn btn-ghost">
            <i class="fa-solid fa-arrow-left"></i> Tornar
        </a>
    </div>

  <div class="page-header">
    <h2>Registrar Tipus de Documentació</h2>
  </div>

  <div class="panel">
    <form id="documentForm" action="../PHP/guardar_documentacio.php" method="POST" enctype="multipart/form-data">

        <label for="nom_document">Nom del document</label>
        <input type="text" name="nom_document" id="nom_document" required>

        <label for="tipus_document">Tipus de document</label>
        <select name="tipus_document" id="tipus_document" required>
            <option value="DNI">DNI</option>
            <option value="Contracte laboral">Contracte laboral</option>
            <option value="Permís de treball">Permís de treball</option>
            <option value="Certificacio">Certificació</option>
            <option value="Reconeixement medic">Reconeixement mèdic</option>
            <option value="Formacio">Formació</option>
            <option value="Document EPI">Document EPI</option>
            <option value="Altres" selected>Altres</option>
        </select>

        <label for="ruta_url">Adjuntar document (PDF o imatge)</label>
        <input type="file" name="ruta_url" id="ruta_url" accept=".pdf,image/*" required>

        <label for="data_emissio">Data d'emissió</label>
        <input type="date" name="data_emissio" id="data_emissio" required>

        <label for="data_caducitat">Data de caducitat</label>
        <input type="date" name="data_caducitat" id="data_caducitat" required>

        <label for="observacions">Descripció (opcional)</label>
        <textarea name="observacions" id="observacions"></textarea>

        <label for="dni">DNI del treballador</label>
        <input type="text" name="dni" id="dni" required pattern="\d{8}[A-Za-z]">

        <div class="error" id="errorMsg"></div>

        <button type="submit" class="btn btn-primary btn-full mt-2">Guardar Tipus de Document</button>

    </form>
  </div>

</div>

<script>
// Validació simple del formulari
document.getElementById('documentForm').addEventListener('submit', function(e) {
    const dataEmissio = new Date(document.getElementById('data_emissio').value);
    const dataCaducitat = new Date(document.getElementById('data_caducitat').value);
    const errorMsg = document.getElementById('errorMsg');
    errorMsg.textContent = '';

    if (dataCaducitat <= dataEmissio) {
        e.preventDefault();
        errorMsg.textContent = 'La data de caducitat ha de ser posterior a la data d’emissió.';
        return false;
    }

    const dni = document.getElementById('dni').value.trim();
    const dniRegex = /^\d{8}[A-Za-z]$/;
    if (!dniRegex.test(dni)) {
        e.preventDefault();
        errorMsg.textContent = 'El DNI ha de tenir 8 números i una lletra final.';
        return false;
    }
});
</script>

</body>
</html>
