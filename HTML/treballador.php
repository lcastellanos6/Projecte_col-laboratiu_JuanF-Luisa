<!DOCTYPE html>
<html lang="ca">
<head>
<meta charset="UTF-8">
<title>Registrar Treballador</title>
<link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="page">
  <div class="page-header">
    <h1>Registrar nou treballador</h1>
    <p class="page-subtitle">Introdueix les dades bàsiques del treballador.</p>
  </div>

  <div class="panel">
<form action="../PHP/guardar_treballador.php" method="post" enctype="multipart/form-data">

    <label>Nom complet:</label>
    <input type="text" name="nom_complet" required>

    <label>Fotografia (opcional):</label>
    <input type="file" name="fotografia" accept="image/*">

    <label>Document d'identitat / Passaport:</label>
    <input type="text" name="document_identitat" required>

    <label>Data de naixement:</label>
    <input type="date" name="data_naixement">

    <label>Lloc de naixement:</label>
    <input type="text" name="lloc_naixement">

    <label>Nacionalitat:</label>
    <input type="text" name="nacionalitat">

    <label>Situacio de Residència:</label>
    <input type="text" name="residencia">

    <label>Telèfon:</label>
    <input type="text" name="telefon">

    <label>Email:</label>
    <input type="email" name="email">

    <label>Adreça:</label>
    <input type="text" name="adreca">

    <label>Contacte d'emergència:</label>
    <input type="text" name="contacte_emergencia">

    <label>Telèfon d'emergència:</label>
    <input type="text" name="telefon_emergencia">

    <label>Compte bancari:</label>
    <input type="text" name="compte_bancari">

    <label>Consentiment RGPD:</label>
    <select name="consentiment_rgpd">
        <option value="1">Sí</option>
        <option value="0" selected>No</option>
    </select>

    <!-- ID Posició -->
    <label>Posició:</label>
    <select name="id_posicio">
        <option value="">Selecciona posició</option>
        <?php
        $conn = new mysqli("localhost","root","","web");
        if ($conn->connect_error) die("Error connexió");

        $res = $conn->query("SELECT id_posicio, nom FROM posicio");
        while($row = $res->fetch_assoc()){
            echo "<option value='{$row['id_posicio']}'>{$row['nom']}</option>";
        }
        ?>
    </select>

    <!-- ID Calendari Model -->
    <label>Calendari Model:</label>
    <select name="id_calendari_model">
        <option value="">Selecciona calendari model</option>
        <?php
        $res2 = $conn->query("SELECT id_calendari_model, nom FROM calendari_model");
        while($row = $res2->fetch_assoc()){
            echo "<option value='{$row['id_calendari_model']}'>{$row['nom']}</option>";
        }
        ?>
    </select>

    <!-- ID Horari Model -->
    <label>Horari Model:</label>
    <select name="id_horari_model">
        <option value="">Selecciona horari model</option>
        <?php
        $res3 = $conn->query("SELECT id_horari_model, nom FROM horari_model");
        while($row = $res3->fetch_assoc()){
            echo "<option value='{$row['id_horari_model']}'>{$row['nom']}</option>";
        }
        $conn->close();
        ?>
    </select>

    <button type="submit" class="btn btn-primary btn-full mt-2">Guardar Treballador</button>
</form>
  </div>
</div>

</body>
</html>
