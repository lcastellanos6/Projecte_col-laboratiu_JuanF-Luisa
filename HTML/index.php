<?php
session_start();
?>
<!DOCTYPE html>
<html lang="ca">
<head>
<meta charset="UTF-8">
<title>Gestió de l'Explotació Fruiteres</title>

<link rel="stylesheet" href="styles.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<script>
function openPage(url) {
    document.getElementById("contentFrame").src = url;
    if (typeof window.setActiveMenu === 'function') {
        window.setActiveMenu(url);
    }
    if (typeof window.saveIframePage === 'function') {
        window.saveIframePage(url);
    }
}

document.addEventListener('DOMContentLoaded', function() {

    const menuSections = Array.from(document.querySelectorAll('.menu-section'));
    const menuStateKey = 'menu-seccio-oberta';
    const openSingleSection = (targetSection) => {
        menuSections.forEach((section) => {
            section.classList.toggle('is-open', section === targetSection);
        });
    };
    const closeAllSections = () => {
        menuSections.forEach((section) => section.classList.remove('is-open'));
    };
    const saveOpenSection = (section) => {
        const index = menuSections.indexOf(section);
        if (index >= 0) {
            localStorage.setItem(menuStateKey, String(index));
        }
    };
    const clearOpenSection = () => {
        localStorage.removeItem(menuStateKey);
    };
    const restoreOpenSection = () => {
        const stored = Number.parseInt(localStorage.getItem(menuStateKey), 10);
        if (Number.isInteger(stored) && menuSections[stored]) {
            openSingleSection(menuSections[stored]);
        }
    };

    const menuButtons = document.querySelectorAll('.menu-section-body button[data-url]');
    const iframeStateKey = 'menu-iframe-pagina';
    const saveIframePage = (url) => {
        if (url) {
            localStorage.setItem(iframeStateKey, url);
        }
    };
    const restoreIframePage = () => {
        const stored = localStorage.getItem(iframeStateKey);
        if (stored && contentFrame) {
            contentFrame.src = stored;
        }
    };
    const normalizeUrl = (url) => {
        try {
            const normalized = new URL(url, window.location.href).pathname;
            return normalized.replace(/\/+$/g, '');
        } catch (error) {
            return String(url || '').replace(/\/+$/g, '');
        }
    };

    window.setActiveMenu = (url) => {
        const targetPath = normalizeUrl(url);

        menuButtons.forEach((button) => {
            const buttonPath = normalizeUrl(button.dataset.url || '');
            const isActive = buttonPath === targetPath;
            button.classList.toggle('is-active', isActive);

            if (isActive) {
                button.setAttribute('aria-current', 'page');
                const section = button.closest('.menu-section');
                if (section) {
                    openSingleSection(section);
                    saveOpenSection(section);
                }
            } else {
                button.removeAttribute('aria-current');
            }
        });
    };
    window.saveIframePage = saveIframePage;

    restoreOpenSection();

    document.querySelectorAll('.menu-section-header').forEach(header => {
        header.addEventListener('click', () => {
            const section = header.closest('.menu-section');
            if (section) {
                if (section.classList.contains('is-open')) {
                    section.classList.remove('is-open');
                    clearOpenSection();
                } else {
                    openSingleSection(section);
                    saveOpenSection(section);
                }
            }
        });
    });

    const toggleButton = document.getElementById('menuToggle');
    const appShell = document.querySelector('.app-shell');
    const contentFrame = document.getElementById('contentFrame');

    if (toggleButton && appShell) {
        const syncToggleState = () => {
            const isCollapsed = appShell.classList.contains('sidebar-collapsed');
            toggleButton.setAttribute('aria-expanded', isCollapsed ? 'false' : 'true');
        };

        syncToggleState();

        toggleButton.addEventListener('click', () => {
            appShell.classList.toggle('sidebar-collapsed');
            syncToggleState();
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                appShell.classList.add('sidebar-collapsed');
                syncToggleState();
            }
        });
    }

    if (contentFrame) {
        restoreIframePage();

        contentFrame.addEventListener('load', () => {
            window.setActiveMenu(contentFrame.src);
            saveIframePage(contentFrame.src);
        });

        window.setActiveMenu(contentFrame.src);
    }
});
</script>
</head>

<body class="app-layout-body">

<div class="app-shell">

<!-- ===== HEADER ===== -->
<header class="hero">
<div class="hero-inner">
<div class="hero-text">

<div class="hero-brand">
<button class="hero-menu-toggle" type="button" id="menuToggle" aria-expanded="false" aria-controls="dashboardDropdown">
<span class="sr-only">Obrir el menú principal</span>
<i class="fa-solid fa-bars" aria-hidden="true"></i>
</button>

<img src="logo.png" alt="Logo Explotació">
<div class="hero-copy">
<h1>Gestió de l'Explotació Fruiteres</h1>
<span>Panell de control general</span>
</div>
</div>

<div class="hero-inline-actions">
<?php if(isset($_SESSION['usuari'])): ?>
<div class="hero-session-info">
<p><?= htmlspecialchars($_SESSION['usuari']) ?></p>
<small><?= date('d/m/Y') ?></small>
</div>
<?php else: ?>
<a class="login-cta" href="login.html">Inicia sessió</a>
<?php endif; ?>
</div>

</div>
</div>
</header>

<div class="app-container">

    <aside id="dashboardDropdown" class="sidebar">
        <button class="sidebar-close" type="button" aria-label="Tancar el menú" onclick="document.getElementById('menuToggle').click()">
            <i class="fa-solid fa-xmark" aria-hidden="true"></i>
        </button>
        <div class="app-menu">
            <div class="menu-section">
                <button class="menu-section-header" type="button">
                    <span class="menu-title"><span class="menu-icon">📌</span><span class="menu-label">Gestió de cultius</span></span>
                    <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
                </button>
                <div class="menu-section-body">
                <button onclick="openPage('../PHP/consulta_parcela_sector.php')" data-url="../PHP/consulta_parcela_sector.php">Parcel·les i sectors</button>
                <button onclick="openPage('../PHP/consulta_cultius_varietats.php')" data-url="../PHP/consulta_cultius_varietats.php">Cultius i varietats</button>
                <button onclick="openPage('../PHP/comparativa_varietats.php')" data-url="../PHP/comparativa_varietats.php">Comparacio Parcel·les i Varietats</button>
                <button onclick="openPage('../PHP/infraestructura.php')" data-url="../PHP/infraestructura.php">Infraestructura</button>
                <button onclick="openPage('plantacio.html')" data-url="plantacio.html">Plantació</button>
                <button onclick="openPage('fila.html')" data-url="fila.html">Files</button>
                <button onclick="openPage('previsio_collita.html')" data-url="previsio_collita.html">Previsió de collita</button>
                </div>
            </div>


<!-- ===== OPERACIONS ===== -->
<div class="menu-section">
<button class="menu-section-header">
🌱 Operacions <i class="fa-solid fa-chevron-down"></i>
</button>
<div class="menu-section-body">
<button onclick="openPage('tractament.html')" data-url="tractament.html">Tractaments</button>
<button onclick="openPage('../PHP/tractament.php')" data-url="../PHP/tractament.php">Calendari de Tractaments</button>
<button onclick="openPage('pla_tractament.html')" data-url="pla_tractament.html">Pla de tractaments</button>
<button onclick="openPage('aplicacio.html')" data-url="aplicacio.html">Aplicació</button>
<button onclick="openPage('aplicacio_productes.html')" data-url="aplicacio_productes.html">Aplicació de productes</button>
<button onclick="openPage('../PHP/consulta_productes.php')" data-url="../PHP/consulta_productes.php">Consulta productes</button>
<button onclick="openPage('producte.html')" data-url="producte.html">Productes</button>
<button onclick="openPage('pla_producte.html')" data-url="pla_producte.html">Pla de producte</button>
<button onclick="openPage('magatzem.html')" data-url="magatzem.html">Magatzem</button>
<button onclick="openPage('producte_lot.html')" data-url="producte_lot.html">Lot de producte</button>
<button onclick="openPage('moviment_lot.html')" data-url="moviment_lot.html">Moviment estoc</button>
<button onclick="openPage('../PHP/tasca.php')" data-url="../PHP/tasca.php">Tasques</button>
<button onclick="openPage('../PHP/calendari_tasques.php')" data-url="../PHP/calendari_tasques.php">Calendari Tasques</button>
<button onclick="openPage('seguiment.html')" data-url="seguiment.html">Seguiment</button>
<button onclick="openPage('registre.html')" data-url="registre.html">Registre</button>
<button onclick="openPage('../PHP/collita_nova.php')" data-url="../PHP/collita_nova.php">Collita</button>
<button onclick="openPage('../PHP/produccio.php')" data-url="../PHP/produccio.php">Produccio de collita</button>
<button onclick="openPage('lot_produccio.html')" data-url="lot_produccio.html">Lot de producció</button>
<button onclick="openPage('control_qualitat.html')" data-url="control_qualitat.html">Control de qualitat</button>
<button onclick="openPage('estat_fenologic.html')" data-url="estat_fenologic.html">Estat fenològic</button>
<button onclick="openPage('../PHP/mapa_calor_parceles.php')" data-url="../PHP/mapa_calor_parceles.php">Mapa de calor</button>
</div>
</div>

<!-- ===== TREBALLADORS ===== -->
<div class="menu-section">
<button class="menu-section-header">
👨‍🌾 Treballadors <i class="fa-solid fa-chevron-down"></i>
</button>
<div class="menu-section-body">
<button onclick="openPage('treballador.html')" data-url="treballador.html">Treballadors</button>
<button onclick="openPage('operari.html')" data-url="operari.html">Operari</button>
<button onclick="openPage('maquinaria.html')" data-url="maquinaria.html">Maquinària</button>
<button onclick="openPage('contracte.html')" data-url="contracte.html">Contractes</button>
<button onclick="openPage('absencia.php')" data-url="absencia.php">Absències</button>
<button onclick="openPage('documentacio.html')" data-url="documentacio.html">Documentació</button>
<button onclick="openPage('departament.html')" data-url="departament.html">Departaments</button>
<button onclick="openPage('equip.html')" data-url="equip.html">Equips</button>
<button onclick="openPage('membres_equip.html')" data-url="membres_equip.html">Membres del equip</button>
<button onclick="openPage('epis.html')" data-url="epis.html">EPIs</button>
<button onclick="openPage('lliurament_epis.html')" data-url="lliurament_epis.html">Lliurament EPIs</button>
<button onclick="openPage('horari.html')" data-url="horari.html">Horaris model</button>
<button onclick="openPage('calendari.html')" data-url="calendari.html">Calendaris model</button>
</div>
</div>

<!-- ===== JORNADES I HORARIS (NUEVO) ===== -->
<div class="menu-section">
<button class="menu-section-header">
⏱️ Jornades i horaris <i class="fa-solid fa-chevron-down"></i>
</button>
<div class="menu-section-body">
<button onclick="openPage('../PHP/registrar_jornada.php')" data-url="../PHP/registrar_jornada.php">
➕ Registrar jornada
</button>
<button onclick="openPage('../PHP/calendari_mensual.php')" data-url="../PHP/calendari_mensual.php">
📆 Vista calendari
</button>
<button onclick="openPage('../PHP/calendari_absencies.php')" data-url="../PHP/calendari_absencies.php">
📆 Vista calendari
</button>
<button onclick="openPage('../PHP/llista_jornades.php')" data-url="../PHP/llista_jornades.php">
📊 Resum hores treballades
</button>
</div>
</div>

<!-- ===== CONFIGURACIÓ ===== -->
<div class="menu-section">
<button class="menu-section-header">
⚙️ Configuració <i class="fa-solid fa-chevron-down"></i>
</button>
<div class="menu-section-body">
<button onclick="openPage('login.html')" data-url="login.html">Usuaris</button>
</div>
</div>

</div>
</aside>

    <div class="app-content">
        <iframe id="contentFrame" src="../PHP/consulta_parcela_sector.php"></iframe>
    </div>

</div>
</div>

</body>
</html>

