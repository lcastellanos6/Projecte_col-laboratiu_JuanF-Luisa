<?php
$conn = new mysqli("localhost","root","","web");
if ($conn->connect_error) die("Error BD");

$res = $conn->query("
SELECT 
  id_parcela,
  ref_cadastral,
  nom,
  municipi,
  superficie,
  tipus_sol,
  pendent,
  orientacio,
  ST_AsGeoJSON(geometria) AS geo
FROM parcela
");

$rows = [];
$features = [];

while($r = $res->fetch_assoc()){
  $rows[$r['id_parcela']] = $r;

  if($r['geo']){
    $features[] = [
      "type"=>"Feature",
      "geometry"=>json_decode($r['geo']),
      "properties"=>[
        "id"=>$r['id_parcela'],
        "nom"=>$r['nom'],
        "ref"=>$r['ref_cadastral'],
        "municipi"=>$r['municipi'],
        "superficie"=>$r['superficie'],
        "tipus_sol"=>$r['tipus_sol'],
        "pendent"=>$r['pendent'],
        "orientacio"=>$r['orientacio']
      ]
    ];
  }
}
$geojson = json_encode(["type"=>"FeatureCollection","features"=>$features]);
?>

<!DOCTYPE html>
<html lang="ca">
<head>
<meta charset="utf-8">
<title>Parcel·les</title>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<style>
body{
  margin:0;
  font-family:Arial;
  background:#f4fff4;
}

.container{
  display:flex;
  height:100vh;
}

/* LATERAL */
.left{
  width:40%;
  padding:20px;
  overflow:auto;
}

h2{
  color:#2f7d2f;
  margin-top:0;
}

/* LISTA */
ul{
  list-style:none;
  padding:0;
}

li{
  padding:10px;
  background:white;
  margin-bottom:8px;
  cursor:pointer;
  border-radius:6px;
  box-shadow:0 2px 4px rgba(0,0,0,0.1);
}

li:hover{
  background:#e6f4e6;
}

/* TABLA */
table{
  width:100%;
  border-collapse:collapse;
  margin-top:15px;
  background:white;
  display:none;
}

th,td{
  padding:10px;
  border-bottom:1px solid #ddd;
}

th{
  background:#e6f4e6;
}

/* BOTONES */
button{
  padding:5px 8px;
  border:none;
  border-radius:4px;
  cursor:pointer;
}

.btn-edit{background:#2f7d2f;color:white}
.btn-del{background:#b33;color:white}

/* MAPA */
.right{
  width:60%;
}

#map{
  width:100%;
  height:100%;
}
</style>
</head>

<body>

<div class="container">

  <!-- IZQUIERDA -->
  <div class="left">
    <h2>Parcel·les</h2>

    <ul>
      <?php foreach($rows as $id=>$r): ?>
        <li onclick="selectParcela(<?= $id ?>)">
          <?= htmlspecialchars($r['nom']) ?>
        </li>
      <?php endforeach; ?>
    </ul>

    <!-- TABLA (OCULTA AL INICIO) -->
    <table id="infoTable">
      <tr><th>Camp</th><th>Valor</th></tr>
      <tbody id="infoBody"></tbody>
      <tr>
        <td colspan="2" style="text-align:center">
          <a id="editLink"><button class="btn-edit">Editar</button></a>
          <a id="deleteLink" onclick="return confirm('Eliminar parcel·la?')">
            <button class="btn-del">Eliminar</button>
          </a>
        </td>
      </tr>
    </table>
  </div>

  <!-- MAPA -->
  <div class="right">
    <div id="map"></div>
  </div>

</div>

<script>
const data = <?= json_encode($rows) ?>;
const geojson = <?= $geojson ?>;

const map = L.map('map').setView([41.6,2.4],12);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

const index = {};

const layer = L.geoJSON(geojson,{
  style:{color:"#2f7d2f",weight:2,fillOpacity:0.3},
  onEachFeature:(f,l)=>{
    index[f.properties.id]=l;
    l.bindPopup(`<b>${f.properties.nom}</b>`);
  }
}).addTo(map);

function selectParcela(id){
  const p = data[id];
  if(!p) return;

  // Mostrar tabla
  document.getElementById("infoTable").style.display="table";

  // Rellenar tabla
  const body = document.getElementById("infoBody");
  body.innerHTML = `
    <tr><td>Nom</td><td>${p.nom}</td></tr>
    <tr><td>Referència</td><td>${p.ref_cadastral}</td></tr>
    <tr><td>Municipi</td><td>${p.municipi}</td></tr>
    <tr><td>Superfície</td><td>${p.superficie} ha</td></tr>
    <tr><td>Tipus sòl</td><td>${p.tipus_sol}</td></tr>
    <tr><td>Pendent</td><td>${p.pendent}%</td></tr>
    <tr><td>Orientació</td><td>${p.orientacio}</td></tr>
  `;

  // Links
  document.getElementById("editLink").href = "editar_parcela.php?id="+id;
  document.getElementById("deleteLink").href = "../PHP/eliminar_parcela.php?id="+id;

  // Mapa
  const l = index[id];
  if(l){
    map.fitBounds(l.getBounds(),{maxZoom:18});
    l.openPopup();
  }
}
</script>

</body>
</html>




