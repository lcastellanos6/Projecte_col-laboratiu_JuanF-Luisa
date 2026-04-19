<?php
session_start();
require_once __DIR__ . '/db.php';
$conn = db_connect();

$id_usuari = $_SESSION['id_usuari'] ?? 0;
$id_treballador_sessio = $_SESSION['id_treballador'] ?? 0;
$rol_sessio = $_SESSION['rol'] ?? 'usuari';

// Si no tenim id_treballador a la sessió però si id_usuari, intentem recuperar-lo
if (!$id_treballador_sessio && $id_usuari) {
    $stmt_u = $conn->prepare("SELECT id_treballador FROM usuari WHERE id_usuari = ?");
    $stmt_u->bind_param("i", $id_usuari);
    $stmt_u->execute();
    $res_u = $stmt_u->get_result()->fetch_assoc();
    if ($res_u && $res_u['id_treballador']) {
        $id_treballador_sessio = (int)$res_u['id_treballador'];
        $_SESSION['id_treballador'] = $id_treballador_sessio; // Actualitzem la sessió
    }
}

// Si es passa un ID per GET i som admin, mirem aquest treballador.
// Si no, mirem el nostre propi perfil.
$id_treballador = null;
if (isset($_GET['id']) && $rol_sessio === 'admin') {
    $id_treballador = (int)$_GET['id'];
} else {
    $id_treballador = $id_treballador_sessio;
}

if (!$id_treballador) {
    die("No s'ha especificat cap treballador o no tens un perfil associat. Si ets administrador, vincula el teu usuari a un treballador a la secció de Gestió d'Usuaris.");
}

// Obtenir dades del treballador
$sql = "SELECT t.*, p.nom as nom_posicio, h.nom as nom_horari, c.nom as nom_calendari
        FROM treballador t 
        LEFT JOIN posicio p ON t.id_posicio = p.id_posicio 
        LEFT JOIN horari_model h ON t.id_horari_model = h.id_horari_model 
        LEFT JOIN calendari_model c ON t.id_calendari_model = c.id_calendari_model
        WHERE t.id_treballador = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_treballador);
$stmt->execute();
$treballador = $stmt->get_result()->fetch_assoc();

if (!$treballador) {
    die("Perfil no trobat.");
}

// Obtenir dades de contracte
$contracte = null;
$sql_cont = "SELECT * FROM contracte WHERE id_treballador = ? ORDER BY data_incorporacio DESC LIMIT 1";
$stmt_c = $conn->prepare($sql_cont);
$stmt_c->bind_param("i", $id_treballador);
$stmt_c->execute();
$contracte = $stmt_c->get_result()->fetch_assoc();

// Obtenir tasques assignades
$tasques = [];
$sql_tasques = "SELECT tt.*, ta.nom_tasca, ta.durada_estimada
                FROM treballador_tasca tt 
                JOIN tasca ta ON tt.id_tasca = ta.id_tasca 
                WHERE tt.id_treballador = ? 
                ORDER BY tt.data_assignacio DESC LIMIT 10";
$stmt_t = $conn->prepare($sql_tasques);
$stmt_t->bind_param("i", $id_treballador);
$stmt_t->execute();
$res_t = $stmt_t->get_result();
while ($row = $res_t->fetch_assoc()) {
    $tasques[] = $row;
}

// Obtenir EPIs lliurats
$epis = [];
$sql_epis = "SELECT le.*, et.nom as nom_epi 
             FROM epi_lliurament le 
             JOIN epi_tipus et ON le.id_epi_tipus = et.id_epi_tipus 
             WHERE le.id_treballador = ? 
             ORDER BY le.data_lliurament DESC";
$stmt_e = $conn->prepare($sql_epis);
$stmt_e->bind_param("i", $id_treballador);
$stmt_e->execute();
$res_e = $stmt_e->get_result();
while ($row = $res_e->fetch_assoc()) {
    $epis[] = $row;
}

// Obtenir Formacions i Certificacions
$formacions = [];
$sql_form = "SELECT tfc.*, fc.nom as nom_formacio 
             FROM treballador_formacio_cert tfc 
             JOIN formacio_certificacio fc ON tfc.id_formacio_cert = fc.id_formacio_cert 
             WHERE tfc.id_treballador = ? 
             ORDER BY tfc.data_caducitat ASC";
$stmt_f = $conn->prepare($sql_form);
$stmt_f->bind_param("i", $id_treballador);
$stmt_f->execute();
$res_f = $stmt_f->get_result();
while ($row = $res_f->fetch_assoc()) {
    $formacions[] = $row;
}

// Obtenir Documentació
$documents = [];
$sql_docs = "SELECT * FROM registre_document WHERE id_treballador = ? ORDER BY created_at DESC";
$stmt_d = $conn->prepare($sql_docs);
$stmt_d->bind_param("i", $id_treballador);
$stmt_d->execute();
$res_d = $stmt_d->get_result();
while ($row = $res_d->fetch_assoc()) {
    $documents[] = $row;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Perfil de <?= htmlspecialchars($treballador['nom_complet']) ?></title>
    <link rel="stylesheet" href="../HTML/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .profile-container {
            max-width: 1000px;
            margin: 0 auto;
        }
        .profile-header-card {
            display: flex;
            gap: 2rem;
            align-items: center;
            background: white;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            margin-bottom: 2rem;
        }
        .profile-avatar {
            width: 180px;
            height: 180px;
            border-radius: 1rem;
            object-fit: cover;
            background: #f8fafc;
            border: 4px solid #fff;
            box-shadow: 0 0 0 1px #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 5rem;
            color: #cbd5e1;
        }
        .profile-main-info h1 {
            margin: 0;
            font-size: 2.25rem;
            color: #1e293b;
        }
        .profile-main-info .job-title {
            font-size: 1.125rem;
            color: #64748b;
            margin-bottom: 1rem;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
        }
        .info-group {
            background: white;
            padding: 1.5rem;
            border-radius: 0.75rem;
            border: 1px solid #e2e8f0;
        }
        .info-group h3 {
            margin-top: 0;
            margin-bottom: 1.25rem;
            font-size: 1rem;
            color: #1e293b;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .data-row {
            display: flex;
            flex-direction: column;
            margin-bottom: 1rem;
        }
        .data-row:last-child {
            margin-bottom: 0;
        }
        .data-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.025em;
            color: #94a3b8;
            font-weight: 600;
        }
        .data-value {
            font-size: 0.95rem;
            color: #334155;
            font-weight: 500;
        }
        .section-tabs {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
            overflow-x: auto;
            padding-bottom: 0.5rem;
        }
        .tab-btn {
            padding: 0.5rem 1rem;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            color: #64748b;
            cursor: pointer;
            white-space: nowrap;
            font-weight: 500;
            transition: all 0.2s;
        }
        .tab-btn.active {
            background: #1a3a1a;
            color: white;
            border-color: #1a3a1a;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
    </style>
</head>
<body class="page">
    <div class="profile-container">
        <div class="mb-2 flex justify-between">
            <a href="javascript:history.back()" class="btn btn-ghost">
                <i class="fa-solid fa-arrow-left"></i> Tornar
            </a>
            <?php if ($rol_sessio === 'admin'): ?>
                <a href="editar_treballador.php?id=<?= $id_treballador ?>" class="btn btn-primary">
                    <i class="fa-solid fa-user-pen"></i> Editar dades
                </a>
            <?php endif; ?>
        </div>

        <div class="profile-header-card">
            <?php if ($treballador['fotografia']): ?>
                <img src="<?= htmlspecialchars($treballador['fotografia']) ?>" alt="Foto" class="profile-avatar">
            <?php else: ?>
                <div class="profile-avatar">
                    <i class="fa-solid fa-user"></i>
                </div>
            <?php endif; ?>
            <div class="profile-main-info">
                <h1><?= htmlspecialchars($treballador['nom_complet']) ?></h1>
                <div class="job-title">
                    <i class="fa-solid fa-briefcase"></i> <?= htmlspecialchars($treballador['nom_posicio'] ?? 'Sense posició assignada') ?>
                </div>
                <div class="flex gap-1">
                    <span class="badge badge-info"><i class="fa-solid fa-clock"></i> <?= htmlspecialchars($treballador['nom_horari'] ?? 'Horari no assignat') ?></span>
                    <span class="badge badge-success"><i class="fa-solid fa-calendar-days"></i> <?= htmlspecialchars($treballador['nom_calendari'] ?? 'Calendari no assignat') ?></span>
                </div>
            </div>
        </div>

        <div class="section-tabs">
            <button class="tab-btn active" onclick="showTab(event, 'personal')">Dades Personals</button>
            <button class="tab-btn" onclick="showTab(event, 'laboral')">Informació Laboral</button>
            <button class="tab-btn" onclick="showTab(event, 'tasques')">Tasques (<?= count($tasques) ?>)</button>
            <button class="tab-btn" onclick="showTab(event, 'formacio')">Certificacions (<?= count($formacions) ?>)</button>
            <button class="tab-btn" onclick="showTab(event, 'epis')">EPIs (<?= count($epis) ?>)</button>
            <button class="tab-btn" onclick="showTab(event, 'documents')">Documents (<?= count($documents) ?>)</button>
        </div>

        <!-- TAB PERSONAL -->
        <div id="personal" class="tab-content active">
            <div class="info-grid">
                <div class="info-group">
                    <h3><i class="fa-solid fa-id-card"></i> Identificació</h3>
                    <div class="data-row">
                        <span class="data-label">Document Identitat</span>
                        <span class="data-value"><?= htmlspecialchars($treballador['document_identitat']) ?></span>
                    </div>
                    <div class="data-row">
                        <span class="data-label">Seguretat Social</span>
                        <span class="data-value"><?= htmlspecialchars($treballador['num_seguretat_social'] ?? '—') ?></span>
                    </div>
                    <div class="data-row">
                        <span class="data-label">Data Naixement</span>
                        <span class="data-value"><?= $treballador['data_naixement'] ? date('d/m/Y', strtotime($treballador['data_naixement'])) : '—' ?></span>
                    </div>
                </div>

                <div class="info-group">
                    <h3><i class="fa-solid fa-address-book"></i> Contacte</h3>
                    <div class="data-row">
                        <span class="data-label">Telèfon</span>
                        <span class="data-value"><?= htmlspecialchars($treballador['telefon'] ?? '—') ?></span>
                    </div>
                    <div class="data-row">
                        <span class="data-label">Email</span>
                        <span class="data-value"><?= htmlspecialchars($treballador['email'] ?? '—') ?></span>
                    </div>
                    <div class="data-row">
                        <span class="data-label">Adreça</span>
                        <span class="data-value"><?= htmlspecialchars($treballador['adreca'] ?? '—') ?></span>
                    </div>
                </div>

                <div class="info-group">
                    <h3><i class="fa-solid fa-life-ring"></i> Emergència</h3>
                    <div class="data-row">
                        <span class="data-label">Persona de contacte</span>
                        <span class="data-value"><?= htmlspecialchars($treballador['contacte_emergencia'] ?? '—') ?></span>
                    </div>
                    <div class="data-row">
                        <span class="data-label">Telèfon emergència</span>
                        <span class="data-value"><?= htmlspecialchars($treballador['telefon_emergencia'] ?? '—') ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB LABORAL -->
        <div id="laboral" class="tab-content">
            <div class="info-grid">
                <div class="info-group">
                    <h3><i class="fa-solid fa-file-contract"></i> Contracte Actual</h3>
                    <?php if ($contracte): ?>
                        <div class="data-row">
                            <span class="data-label">Tipus</span>
                            <span class="data-value"><?= htmlspecialchars($contracte['tipus_contracte']) ?></span>
                        </div>
                        <div class="data-row">
                            <span class="data-label">Incorporació</span>
                            <span class="data-value"><?= date('d/m/Y', strtotime($contracte['data_incorporacio'])) ?></span>
                        </div>
                        <div class="data-row">
                            <span class="data-label">Finalització</span>
                            <span class="data-value"><?= $contracte['data_finalitzacio'] ? date('d/m/Y', strtotime($contracte['data_finalitzacio'])) : 'Indefinit' ?></span>
                        </div>
                    <?php else: ?>
                        <p class="page-subtitle">No hi ha informació de contracte registrada.</p>
                    <?php endif; ?>
                </div>
                <div class="info-group">
                    <h3><i class="fa-solid fa-building-columns"></i> Dades Bancàries</h3>
                    <div class="data-row">
                        <span class="data-label">IBAN / Compte</span>
                        <span class="data-value"><?= htmlspecialchars($treballador['compte_bancari'] ?? '—') ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB TASQUES -->
        <div id="tasques" class="tab-content">
            <div class="panel">
                <h3 class="panel-title">Historial recent de tasques</h3>
                <?php if (empty($tasques)): ?>
                    <p class="page-subtitle">No s'han trobat tasques assignades.</p>
                <?php else: ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Tasca</th>
                                <th>Estimació</th>
                                <th>Estat</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tasques as $t): ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($t['data_assignacio'])) ?></td>
                                    <td><strong><?= htmlspecialchars($t['nom_tasca']) ?></strong></td>
                                    <td><?= htmlspecialchars($t['durada_estimada']) ?> h</td>
                                    <td><span class="badge badge-info">Assignada</span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

        <!-- TAB FORMACIO -->
        <div id="formacio" class="tab-content">
            <div class="panel">
                <h3 class="panel-title">Certificacions i Formació</h3>
                <?php if (empty($formacions)): ?>
                    <p class="page-subtitle">No s'han trobat certificacions registrades.</p>
                <?php else: ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Títol / Formació</th>
                                <th>Expedició</th>
                                <th>Caducitat</th>
                                <th>Hores</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($formacions as $f): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($f['nom_formacio']) ?></strong></td>
                                    <td><?= date('d/m/Y', strtotime($f['data_inici'])) ?></td>
                                    <td>
                                        <?php if ($f['data_caducitat']): ?>
                                            <span class="<?= strtotime($f['data_caducitat']) < time() ? 'text-danger font-bold' : '' ?>">
                                                <?= date('d/m/Y', strtotime($f['data_caducitat'])) ?>
                                            </span>
                                        <?php else: ?>
                                            —
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($f['hores'] ?? '—') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

        <!-- TAB EPIS -->
        <div id="epis" class="tab-content">
            <div class="panel">
                <h3 class="panel-title">Equips de Protecció Individual (EPI)</h3>
                <?php if (empty($epis)): ?>
                    <p class="page-subtitle">No s'han registrat lliuraments d'EPI.</p>
                <?php else: ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Equip</th>
                                <th>Data Lliurament</th>
                                <th>Talla</th>
                                <th>Quantitat</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($epis as $e): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($e['nom_epi']) ?></strong></td>
                                    <td><?= date('d/m/Y', strtotime($e['data_lliurament'])) ?></td>
                                    <td><?= htmlspecialchars($e['talla'] ?? '—') ?></td>
                                    <td><?= htmlspecialchars($e['quantitat']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

        <!-- TAB DOCUMENTS -->
        <div id="documents" class="tab-content">
            <div class="panel">
                <h3 class="panel-title">Gestió Documental</h3>
                <?php if (empty($documents)): ?>
                    <p class="page-subtitle">No s'ha pujat cap document per a aquest treballador.</p>
                <?php else: ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Document</th>
                                <th>Tipus</th>
                                <th>Data Emissió</th>
                                <th>Caducitat</th>
                                <th>Accions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($documents as $d): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($d['nom_document']) ?></strong></td>
                                    <td><?= htmlspecialchars($d['tipus_document'] ?? 'General') ?></td>
                                    <td><?= $d['data_emissio'] ? date('d/m/Y', strtotime($d['data_emissio'])) : '—' ?></td>
                                    <td>
                                        <?php if ($d['data_caducitat']): ?>
                                            <span class="<?= strtotime($d['data_caducitat']) < time() ? 'text-danger font-bold' : '' ?>">
                                                <?= date('d/m/Y', strtotime($d['data_caducitat'])) ?>
                                            </span>
                                        <?php else: ?>
                                            —
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= htmlspecialchars($d['ruta_url']) ?>" target="_blank" class="btn btn-ghost btn-sm">
                                            <i class="fa-solid fa-file-pdf"></i> Veure
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        function showTab(e, tabId) {
            if (e) e.preventDefault();
            
            // Amagar tots els continguts
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            // Desactivar tots els botons
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            // Mostrar el seleccionat
            const targetTab = document.getElementById(tabId);
            if (targetTab) targetTab.classList.add('active');
            
            // Activar el botó clicat
            if (e && e.currentTarget) {
                e.currentTarget.classList.add('active');
            }
        }
    </script>
</body>
</html>
