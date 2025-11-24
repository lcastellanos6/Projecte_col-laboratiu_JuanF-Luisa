<!DOCTYPE html>
<html lang="ca">
<head>
<meta charset="UTF-8">
<title>Gestió de l'Explotació</title>
<style>
body { font-family: Arial; margin: 20px; }
.tabs { display: flex; cursor: pointer; margin-bottom: 10px; }
.tab { padding: 10px 20px; border: 1px solid #ccc; border-bottom: none; margin-right: 2px; background: #eee; }
.tab.active { background: white; font-weight: bold; }
.tab-content { border: 1px solid #ccc; padding: 20px; display: none; }
.tab-content.active { display: block; }
form label { display: block; margin-top: 10px; font-weight: bold; }
form input, form textarea, form select, form button { width: 100%; padding: 6px; margin-top: 5px; border-radius: 5px; border: 1px solid #ccc; }
form button { background-color: #4CAF50; color: white; border: none; cursor: pointer; margin-top: 15px; }
form button:hover { background-color: #45a049; }
</style>
</head>
<body>

<h1>Gestió de l'Explotació</h1>

<div class="tabs">
  <div class="tab active" data-tab="parceles">Parcel·les</div>
  <div class="tab" data-tab="sector">Sector</div>
  <div class="tab" data-tab="varietat">Varietat</div>
</div>

<!-- Pestaña Parcel·les -->
<div id="parceles" class="tab-content active">
    <?php include 'formulari_parcella.php'; ?>
</div>

<!-- Pestaña Sector -->
<div id="sector" class="tab-content">
    <?php include 'formulari_sector.php'; ?>
</div>

<!-- Pestaña Varietat -->
<div id="varietat" class="tab-content">
    <?php include 'formulari_varietat.php'; ?>
</div>

<script>
// Cambiar pestañas
const tabs = document.querySelectorAll('.tab');
tabs.forEach(tab => {
    tab.addEventListener('click', () => {
        document.querySelectorAll('.tab, .tab-content').forEach(el => el.classList.remove('active'));
        tab.classList.add('active');
        document.getElementById(tab.dataset.tab).classList.add('active');
    });
});
</script>

</body>
</html>
