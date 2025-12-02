<!DOCTYPE html>
<html lang="ca">
<head>
<meta charset="UTF-8">
<title>Gestió de l'Explotació</title>
<link rel="stylesheet" href="../HTML/styles.css">
<style>
.tabs { display: flex; cursor: pointer; margin-bottom: 10px; }
.tab { padding: 0.6rem 1.1rem; border-radius: 999px; margin-right: 0.4rem; background: #e5e7eb; font-size: 0.9rem; }
.tab.active { background: #2563eb; color: #ffffff; }
.tab-content { display: none; margin-top: 0.75rem; }
.tab-content.active { display: block; }
</style>
</head>
<body>

<div class="page">
  <div class="page-header">
    <h1>Gestió de l'explotació</h1>
    <p class="page-subtitle">Accés ràpid als formularis principals.</p>
  </div>

  <div class="panel">
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
  </div>
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
