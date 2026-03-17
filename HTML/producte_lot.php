<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Gestió de Lots</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="page">

    <div class="page-header">
        <h2>Afegir Lot de Producte</h2>
    </div>

    <div class="panel">
        <form action="" method="post">

            <label>ID Producte *</label>
            <select name="id_producte" required>
                <option value="">Selecciona un producte</option>
                <?php
                $conn = new mysqli("localhost", "root", "", "web");
                if ($conn->connect_error) {
                    die("Error de connexió: " . $conn->connect_error);
                }

                // Desplegable productes amb nom_comercial
                $result = $conn->query("SELECT id_producte, nom_comercial FROM producte");

                if (!$result) {
                    echo "<option value=''>Error a la consulta: " . $conn->error . "</option>";
                } elseif ($result->num_rows == 0) {
                    echo "<option value=''>No hi ha productes registrats</option>";
                } else {
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . $row['id_producte'] . "'>" . $row['nom_comercial'] . "</option>";
                    }
                }
                ?>
            </select>

            <label>Número de Lot *</label>
            <input type="text" name="numero_lot" required>

            <label>Data de Caducitat</label>
            <input type="date" name="data_caducitat">

            <label>ID Magatzem *</label>
            <select name="id_magatzem" required>
                <option value="">Selecciona un magatzem</option>
                <?php
                $result2 = $conn->query("SELECT id_magatzem, nom FROM magatzem");

                if (!$result2) {
                    echo "<option value=''>Error a la consulta: " . $conn->error . "</option>";
                } elseif ($result2->num_rows == 0) {
                    echo "<option value=''>No hi ha magatzems registrats</option>";
                } else {
                    while ($row2 = $result2->fetch_assoc()) {
                        echo "<option value='" . $row2['id_magatzem'] . "'>" . $row2['nom'] . "</option>";
                    }
                }
                ?>
            </select>

            <label>Quantitat Disponible *</label>
            <input type="number" step="0.001" name="quantitat_disponible" required>

            <label>Unitat *</label>
            <select name="unitat" required>
                <option value="L">L</option>
                <option value="mL">mL</option>
                <option value="kg">kg</option>
                <option value="g">g</option>
            </select>

            <label>Fabricant</label>
            <input type="text" name="fabricant">

            <label>Proveïdor</label>
            <input type="text" name="proveidor">

            <label>Data de Compra</label>
            <input type="date" name="data_compra">

            <label>Preu Unitari</label>
            <input type="number" step="0.0001" name="preu_unitari">

            <button type="submit" class="btn btn-primary btn-full mt-2">Guardar</button>

        </form>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $id_producte = $_POST['id_producte'];
            $numero_lot = $_POST['numero_lot'];
            $data_caducitat = !empty($_POST['data_caducitat']) ? $_POST['data_caducitat'] : NULL;
            $id_magatzem = $_POST['id_magatzem'];
            $quantitat_disponible = $_POST['quantitat_disponible'];
            $unitat = $_POST['unitat'];
            $fabricant = !empty($_POST['fabricant']) ? $_POST['fabricant'] : NULL;
            $proveidor = !empty($_POST['proveidor']) ? $_POST['proveidor'] : NULL;
            $data_compra = !empty($_POST['data_compra']) ? $_POST['data_compra'] : NULL;
            $preu_unitari = !empty($_POST['preu_unitari']) ? $_POST['preu_unitari'] : NULL;

            $stmt = $conn->prepare("
                INSERT INTO lot (
                    id_producte, numero_lot, data_caducitat, id_magatzem,
                    quantitat_disponible, unitat, fabricant, proveidor,
                    data_compra, preu_unitari
                ) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->bind_param(
                "issidssssd",
                $id_producte,
                $numero_lot,
                $data_caducitat,
                $id_magatzem,
                $quantitat_disponible,
                $unitat,
                $fabricant,
                $proveidor,
                $data_compra,
                $preu_unitari
            );

            if ($stmt->execute()) {
                echo "<h3>Lot afegit correctament!</h3>";
                echo "<a href=''>Afegir un altre</a>";
            } else {
                echo "Error: " . $conn->error;
            }

            $stmt->close();
        }

        $conn->close();
        ?>
    </div>

</div>
</body>
</html>