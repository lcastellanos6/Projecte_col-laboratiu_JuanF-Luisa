<?php
session_start();

$stockAlertSummary = null;
if (isset($_SESSION['usuari'])) {
    require_once __DIR__ . '/../PHP/producte_stock_alerts.php';
    $stockAlertSummary = get_producte_stock_alert_summary();
}

$personalAlertSummary = null;
if (isset($_SESSION['usuari'])) {
    require_once __DIR__ . '/../PHP/personal_alerts.php';
    $rol = $_SESSION['rol'] ?? 'usuari';
    $id_tr = $_SESSION['id_treballador'] ?? 0;
    $filter_id = ($rol === 'admin') ? null : $id_tr;
    $personalAlertSummary = get_personal_alert_summary(60, null, $filter_id);
}

$tecnicaAlertSummary = null;
if (isset($_SESSION['usuari'])) {
    require_once __DIR__ . '/../PHP/tecnica_alerts.php';
    $tecnicaAlertSummary = get_tecnica_alert_summary();
}

$nom_usuari = $_SESSION['usuari'] ?? null;
$rol_usuari = $_SESSION['rol'] ?? null;
?>
<!DOCTYPE html>
<html lang="ca">
<head>
<meta charset="UTF-8">
<title>Gestió de l'Explotació Fruitera</title>
<link rel="stylesheet" href="styles.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<script>
function openPage(url) {
    const frame = document.getElementById('contentFrame');
    if (frame) {
        frame.src = url;
    }

    document.querySelectorAll('.menu-section-body button').forEach((btn) => {
        if (btn.getAttribute('onclick') && btn.getAttribute('onclick').includes(url)) {
            btn.classList.add('is-active');
        } else {
            btn.classList.remove('is-active');
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
    const toggleButton = document.getElementById('menuToggle');
    const appShell = document.querySelector('.app-shell');

    if (toggleButton && appShell) {
        toggleButton.addEventListener('click', () => {
            appShell.classList.toggle('sidebar-collapsed');
        });
    }

    document.querySelectorAll('.menu-section-header').forEach((header) => {
        header.addEventListener('click', () => {
            const section = header.parentElement;
            const isOpen = section.classList.contains('is-open');

            document.querySelectorAll('.menu-section').forEach((menuSection) => {
                menuSection.classList.remove('is-open');
            });

            if (!isOpen) {
                section.classList.add('is-open');
            }
        });
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && appShell) {
            appShell.classList.add('sidebar-collapsed');
        }
    });
});
</script>
</head>

<body class="app-layout-body">
<div class="app-shell">
    <header class="hero">
      <div class="hero-inner">
        <div class="hero-text">
          <div class="hero-brand">
            <button class="hero-menu-toggle" type="button" id="menuToggle" aria-expanded="false" aria-controls="dashboardDropdown">
              <span class="sr-only">Obrir el menú principal</span>
              <i class="fa-solid fa-bars" aria-hidden="true"></i>
            </button>
            <img src="logo.png" alt="Logo de l'explotació">
            <div class="hero-copy">
              <h1>Gestió de l'Explotació Fruitera</h1>
              <span>Panell de control general</span>
            </div>
          </div>

          <div class="hero-inline-actions">
            <?php if ($nom_usuari): ?>
              <span style="color: white; margin-right: 15px; font-weight: 500;">
                Hola, <?= htmlspecialchars($nom_usuari) ?>
              </span>

              <div class="flex gap-1">
                <?php if (is_array($stockAlertSummary) && !empty($stockAlertSummary['available'])): ?>
                  <?php $count = (int) ($stockAlertSummary['count'] ?? 0); ?>
                  <a class="btn <?= $count > 0 ? 'btn-primary' : 'btn-ghost' ?>" href="javascript:void(0)" onclick="openPage('../PHP/notificacions_stock.php')">
                    Estoc (<?= $count ?>)
                  </a>
                <?php endif; ?>

                <?php if (is_array($personalAlertSummary) && !empty($personalAlertSummary['available'])): ?>
                  <?php $count = (int) ($personalAlertSummary['count'] ?? 0); ?>
                  <a class="btn <?= $count > 0 ? 'btn-primary' : 'btn-ghost' ?>" href="javascript:void(0)" onclick="openPage('../PHP/notificacions_personal.php')">
                    Personal (<?= $count ?>)
                  </a>
                <?php endif; ?>

                <?php if (is_array($tecnicaAlertSummary) && !empty($tecnicaAlertSummary['available'])): ?>
                  <?php $count = (int) ($tecnicaAlertSummary['count'] ?? 0); ?>
                  <a class="btn <?= $count > 0 ? 'btn-primary' : 'btn-ghost' ?>" href="javascript:void(0)" onclick="openPage('../PHP/notificacions_tecniques.php')">
                    Tècniques (<?= $count ?>)
                  </a>
                <?php endif; ?>
              </div>

              <div class="flex gap-1 ml-2">
                <a href="javascript:void(0)" onclick="openPage('../PHP/perfil_treballador.php')" class="btn btn-ghost btn-sm" style="color: white; border-color: rgba(255,255,255,0.3);">
                  El meu perfil
                </a>
                <a href="logout.php" class="btn btn-ghost btn-sm" style="color: white; border-color: rgba(255,255,255,0.3);">Sortir</a>
              </div>
            <?php else: ?>
              <a class="login-cta" href="login.php">Inicia sessió</a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </header>

  <div class="app-container">
    <aside id="dashboardDropdown" class="sidebar" aria-label="Menú principal">
      <button class="sidebar-close" type="button" aria-label="Tancar el menú" onclick="document.getElementById('menuToggle').click()">
        <i class="fa-solid fa-xmark" aria-hidden="true"></i>
      </button>

      <div class="app-menu">
        <?php if ($rol_usuari === 'admin'): ?>
          <div class="menu-section">
            <button class="menu-section-header" type="button">
              <span class="menu-title"><span class="menu-icon"><i class="fa-solid fa-seedling" aria-hidden="true"></i></span><span class="menu-label">Gestió de cultius</span></span>
              <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
            </button>
            <div class="menu-section-body">
              <button onclick="openPage('../PHP/modul_parceles_cultius.php')" data-url="../PHP/modul_parceles_cultius.php">Estat del mòdul parcel·les i cultius</button>
              <button onclick="openPage('../PHP/planificacio_explotacio.php')" data-url="../PHP/planificacio_explotacio.php">Planificació i estimacions</button>
              <button onclick="openPage('../PHP/mapa_tematic_parceles.php')" data-url="../PHP/mapa_tematic_parceles.php">Mapa temàtic d'estat</button>
              <button onclick="openPage('../PHP/consulta_parcela_sector.php')" data-url="../PHP/consulta_parcela_sector.php">Parcel·les i sectors</button>
              <button onclick="openPage('parcela_nou.php')" data-url="parcela_nou.php">Nova parcel·la</button>
              <button onclick="openPage('sector_nou.php')" data-url="sector_nou.php">Nou sector</button>
              <button onclick="openPage('especie.html')" data-url="especie.html">Nova espècie</button>
              <button onclick="openPage('varietat.php')" data-url="varietat.php">Nova varietat</button>
              <button onclick="openPage('../PHP/consulta_cultius_varietats.php')" data-url="../PHP/consulta_cultius_varietats.php">Cultius i varietats</button>
              <button onclick="openPage('../PHP/comparativa_varietats.php')" data-url="../PHP/comparativa_varietats.php">Comparativa parcel·les i varietats</button>
              <button onclick="openPage('../PHP/infraestructura.php')" data-url="../PHP/infraestructura.php">Infraestructura</button>
              <button onclick="openPage('../PHP/consulta_sol.php')" data-url="../PHP/consulta_sol.php">Consulta de sòls</button>
              <button onclick="openPage('plantacio.php')" data-url="plantacio.php">Nova plantació</button>
              <button onclick="openPage('fila.php')" data-url="fila.php">Nova fila</button>
              <button onclick="openPage('previsio_collita.php')" data-url="previsio_collita.php">Previsió de collita</button>
            </div>
          </div>
        <?php endif; ?>

        <div class="menu-section">
          <button class="menu-section-header" type="button">
            <span class="menu-title"><span class="menu-icon"><i class="fa-solid fa-flask-vial" aria-hidden="true"></i></span><span class="menu-label">Operacions</span></span>
            <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
          </button>
          <div class="menu-section-body">
            <button onclick="openPage('tractament.php')" data-url="tractament.php">Nou tractament</button>
            <button onclick="openPage('../PHP/modul_tractaments_fertilitzacio.php')" data-url="../PHP/modul_tractaments_fertilitzacio.php">Mòdul tractaments i fertilització</button>
            <button onclick="openPage('../PHP/tractament.php')" data-url="../PHP/tractament.php">Calendari de tractaments</button>
            <button onclick="openPage('pla_tractament.php')" data-url="pla_tractament.php">Nou pla de tractaments</button>
            <button onclick="openPage('../HTML/calendari_tractament.php')" data-url="../HTML/calendari_tractament.php">Calendari del pla</button>
            <button onclick="openPage('aplicacio.php')" data-url="aplicacio.php">Nova aplicació</button>
            <button onclick="openPage('../PHP/consulta_productes.php')" data-url="../PHP/consulta_productes.php">Consulta de productes</button>
            <button onclick="openPage('../PHP/notificacions_stock.php')" data-url="../PHP/notificacions_stock.php">Alertes</button>
            <button onclick="openPage('producte.php')" data-url="producte.php">Nou producte</button>
            <button onclick="openPage('magatzem.php')" data-url="magatzem.php">Nou magatzem</button>
            <button onclick="openPage('../PHP/tasca.php')" data-url="../PHP/tasca.php">Tasques</button>
            <button onclick="openPage('../PHP/calendari_tasques.php')" data-url="../PHP/calendari_tasques.php">Calendari de tasques</button>

            <?php if ($rol_usuari === 'admin'): ?>
              <button onclick="openPage('seguiment.php')" data-url="seguiment.php">Nou seguiment</button>
              <button onclick="openPage('registre.php')" data-url="registre.php">Nou registre</button>
              <button onclick="openPage('../PHP/collita_nova.php')" data-url="../PHP/collita_nova.php">Collita</button>
              <button onclick="openPage('../PHP/produccio.php')" data-url="../PHP/produccio.php">Producció de collita</button>
              <button onclick="openPage('../PHP/consulta_qualitat.php')" data-url="../PHP/consulta_qualitat.php">Llistat controls qualitat</button>
              <button onclick="openPage('lot_produccio.php')" data-url="lot_produccio.php">Lot de producció</button>
              <button onclick="openPage('control_qualitat.php')" data-url="control_qualitat.php">Nou control de qualitat</button>
              <button onclick="openPage('estat_fenologic.php')" data-url="estat_fenologic.php">Nou estat fenològic</button>
              <button onclick="openPage('../PHP/mapa_calor_parceles.php')" data-url="../PHP/mapa_calor_parceles.php">Mapa de calor</button>
              <div class="menu-subheader">Anàlisi i Evolució</div>
              <button onclick="openPage('../PHP/evolucio_produccio.php')" data-url="../PHP/evolucio_produccio.php">Evolució producció</button>
              <button onclick="openPage('../PHP/evolucio_vendes.php')" data-url="../PHP/evolucio_vendes.php">Evolució vendes</button>
              <button onclick="openPage('analisi.php')" data-url="analisi.php">Anàlisi agronòmic</button>
              <button onclick="openPage('monitoratge_captures.php')" data-url="monitoratge_captures.php">Monitoratge de captures</button>
              <button onclick="openPage('trampa.php')" data-url="trampa.php">Gestionar trampes</button>
              <button onclick="openPage('calculadora_dosi.php')" data-url="calculadora_dosi.php">Calculadora de dosis</button>
              <button onclick="openPage('cost_explotacio.php')" data-url="cost_explotacio.php">Costos d'explotació</button>
              <button onclick="openPage('sensor_dades.php')" data-url="sensor_dades.php">Dades de sensors</button>
              <div class="menu-subheader">Detall tècnic</div>
              <button onclick="openPage('aplicacio_productes.php')" data-url="aplicacio_productes.php">Aplicació de productes</button>
              <button onclick="openPage('pla_producte.php')" data-url="pla_producte.php">Pla de producte</button>
              <button onclick="openPage('producte_lot.php')" data-url="producte_lot.php">Lot de producte</button>
              <button onclick="openPage('moviment_lot.php')" data-url="moviment_lot.php">Moviment d'estoc</button>
            <?php endif; ?>
          </div>
        </div>

        <?php if ($rol_usuari === 'admin'): ?>
          <div class="menu-section">
            <button class="menu-section-header" type="button">
              <span class="menu-title"><span class="menu-icon"><i class="fa-solid fa-cart-flatbed" aria-hidden="true"></i></span><span class="menu-label">Comandes i Clients</span></span>
              <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
            </button>
            <div class="menu-section-body">
              <button onclick="openPage('../PHP/consulta_comandes.php')" data-url="../PHP/consulta_comandes.php">Llistat de comandes</button>
              <button onclick="openPage('comanda.php')" data-url="comanda.php">Nova comanda</button>
              <button onclick="openPage('client.php')" data-url="client.php">Gestionar clients</button>
              <button onclick="openPage('lot_produccio.php')" data-url="lot_produccio.php">Lots de producció</button>
            </div>
          </div>

          <div class="menu-section">
            <button class="menu-section-header" type="button">
              <span class="menu-title"><span class="menu-icon"><i class="fa-solid fa-people-group" aria-hidden="true"></i></span><span class="menu-label">Treballadors</span></span>
              <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
            </button>
            <div class="menu-section-body">
              <button onclick="openPage('../PHP/consulta_treballadors.php')" data-url="../PHP/consulta_treballadors.php">Llistat de treballadors</button>
              <button onclick="openPage('../PHP/consulta_contractes.php')" data-url="../PHP/consulta_contractes.php">Llistat de contractes</button>
              <button onclick="openPage('../PHP/consulta_absencies.php')" data-url="../PHP/consulta_absencies.php">Llistat d'absències</button>
              <button onclick="openPage('../PHP/consulta_epis.php')" data-url="../PHP/consulta_epis.php">Llistat d'EPIs</button>
              <button onclick="openPage('treballador.php')" data-url="treballador.php">Nou treballador</button>
              <button onclick="openPage('operari.php')" data-url="operari.php">Nou operari</button>
              <button onclick="openPage('maquinaria.php')" data-url="maquinaria.php">Nova maquinaria</button>
              <button onclick="openPage('manteniment_maquinaria.php')" data-url="manteniment_maquinaria.php">Manteniment maquinaria</button>
              <button onclick="openPage('contracte.php')" data-url="contracte.php">Nou contracte</button>
              <button onclick="openPage('absencia.php')" data-url="absencia.php">Nova absència</button>
              <button onclick="openPage('../PHP/notificacions_personal.php')" data-url="../PHP/notificacions_personal.php">Alertes personal</button>
              <button onclick="openPage('documentacio.php')" data-url="documentacio.php">Documentació</button>
              <button onclick="openPage('departament.php')" data-url="departament.php">Departaments</button>
              <button onclick="openPage('equip.php')" data-url="equip.php">Equips</button>
              <button onclick="openPage('membres_equip.php')" data-url="membres_equip.php">Membres de l'equip</button>
              <button onclick="openPage('epis.php')" data-url="epis.php">EPIs (Configuració)</button>
              <button onclick="openPage('lliurament_epis.php')" data-url="lliurament_epis.php">Lliurament d'EPIs</button>
              <button onclick="openPage('horari.php')" data-url="horari.php">Horaris model</button>
              <button onclick="openPage('calendari.php')" data-url="calendari.php">Calendaris model</button>
            </div>
          </div>
        <?php endif; ?>

        <div class="menu-section">
          <button class="menu-section-header" type="button">
            <span class="menu-title"><span class="menu-icon"><i class="fa-regular fa-clock" aria-hidden="true"></i></span><span class="menu-label">Jornades i horaris</span></span>
            <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
          </button>
          <div class="menu-section-body">
            <button onclick="openPage('../PHP/registrar_jornada.php')" data-url="../PHP/registrar_jornada.php">Registrar jornada</button>
            <button onclick="openPage('../PHP/calendari_mensual.php')" data-url="../PHP/calendari_mensual.php">Vista calendari mensual</button>
            <button onclick="openPage('../PHP/calendari_absencies.php')" data-url="../PHP/calendari_absencies.php">Vista d'absències</button>
            <?php if ($rol_usuari === 'admin'): ?>
              <button onclick="openPage('../PHP/llista_jornades.php')" data-url="../PHP/llista_jornades.php">Resum d'hores treballades</button>
            <?php endif; ?>
          </div>
        </div>

        <div class="menu-section">
          <button class="menu-section-header" type="button">
            <span class="menu-title"><span class="menu-icon"><i class="fa-solid fa-gear" aria-hidden="true"></i></span><span class="menu-label">Configuració</span></span>
            <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
          </button>
          <div class="menu-section-body">
            <?php if ($rol_usuari === 'admin'): ?>
              <button onclick="openPage('../PHP/consulta_usuaris.php')" data-url="../PHP/consulta_usuaris.php">Gestió d'usuaris</button>
              <button onclick="openPage('../PHP/usuari_nou.php')" data-url="../PHP/usuari_nou.php">Nou usuari</button>
            <?php endif; ?>
            <button onclick="openPage('../PHP/perfil_treballador.php')" data-url="../PHP/perfil_treballador.php">El meu perfil</button>
          </div>
        </div>
      </div>
    </aside>

    <div class="app-content">
      <iframe id="contentFrame" src="../PHP/dashboard.php" title="Àrea de contingut"></iframe>
    </div>
  </div>
</div>
</body>
</html>
