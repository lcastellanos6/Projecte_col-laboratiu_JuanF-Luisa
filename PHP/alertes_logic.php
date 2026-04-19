<?php
require_once __DIR__ . '/db.php';

/**
 * Obté el resum de totes les alertes del sistema
 */
function get_all_alerts_summary() {
    $conn = db_connect();
    $summary = [
        'produccio' => 0,
        'manteniment' => 0,
        'plagues' => 0,
        'qualitat' => 0,
        'total' => 0
    ];

    // 1. Alertes de producció (Rendiment baix < 1000 kg)
    $res = $conn->query("SELECT COUNT(*) as count FROM collita WHERE quantitat_total < 1000 AND data_inici >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
    if ($res) {
        $summary['produccio'] = (int)$res->fetch_assoc()['count'];
    }

    // 2. Alertes de manteniment (proxim_manteniment <= AVUI + 15 dies)
    $res = $conn->query("SELECT COUNT(*) as count FROM manteniment_maquinaria WHERE proxim_manteniment <= DATE_ADD(CURDATE(), INTERVAL 15 DAY)");
    if ($res) {
        $summary['manteniment'] = (int)$res->fetch_assoc()['count'];
    }

    // 3. Alertes de plagues (captures > 15 en l'última setmana)
    $res = $conn->query("SELECT COUNT(*) as count FROM monitoratge_plaga WHERE quantitat_capturada > 15 AND data_registre >= DATE_SUB(CURDATE(), INTERVAL 15 DAY)");
    if ($res) {
        $summary['plagues'] = (int)$res->fetch_assoc()['count'];
    }

    // 4. Alertes de qualitat (qualificacio_final = 'C' o 'D' o 'Rebuig')
    $res = $conn->query("SELECT COUNT(*) as count FROM control_qualitat WHERE qualificacio_final IN ('C', 'D', 'Rebuig')");
    if ($res) {
        $summary['qualitat'] = (int)$res->fetch_assoc()['count'];
    }

    if (function_exists('get_personal_alert_summary')) {
        $personal = get_personal_alert_summary(30);
        $summary['personal'] = $personal['count'] ?? 0;
    } else {
        $summary['personal'] = 0;
    }

    $summary['tecnica_total'] = $summary['produccio'] + $summary['manteniment'] + $summary['plagues'] + $summary['qualitat'];
    $summary['total'] = $summary['tecnica_total'] + $summary['personal'];
    
    $conn->close();
    return $summary;
}

/**
 * Single source of truth for technical alerts (detailed)
 */
function get_technical_alerts() {
    $conn = db_connect();
    $alerts = [
        'produccio' => [],
        'manteniment' => [],
        'plagues' => [],
        'qualitat' => []
    ];

    // 1. Alertes de Producció (Rendiment baix < 1000 kg)
    $sql_prod = "SELECT c.*, s.nom as sector_nom, v.nom_comu as varietat
                 FROM collita c
                 JOIN plantacio p ON c.plantacio_id = p.id_plantacio
                 JOIN sector s ON p.id_sector = s.id_sector
                 JOIN varietat v ON p.id_varietat = v.id_varietat
                 WHERE c.quantitat_total < 1000 
                 AND c.data_inici >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
    $res = $conn->query($sql_prod);
    if ($res) {
        while($row = $res->fetch_assoc()) {
            $alerts['produccio'][] = [
                'data' => $row['data_inici'],
                'sector' => $row['sector_nom'],
                'missatge' => "Rendiment baix en collita: " . $row['quantitat_total'] . " " . $row['unitat'] . " (" . $row['varietat'] . ")",
                'nivell' => 'Avís'
            ];
        }
    }

    // 2. Alertes de Manteniment de Maquinària (Propers 15 dies)
    $sql_mant = "SELECT m.*, e.tipus as equip_nom 
                 FROM manteniment_maquinaria m 
                 JOIN equip e ON m.id_equip = e.id_equip 
                 WHERE m.proxim_manteniment <= DATE_ADD(CURDATE(), INTERVAL 15 DAY) 
                 ORDER BY m.proxim_manteniment ASC";
    $res = $conn->query($sql_mant);
    if ($res) {
        while($row = $res->fetch_assoc()) {
            $diff = strtotime($row['proxim_manteniment']) - time();
            $dies = ceil($diff / 86400);
            $nivell = ($dies < 0) ? 'Crític' : (($dies < 5) ? 'Urgent' : 'Informativa');
            $alerts['manteniment'][] = [
                'data' => $row['proxim_manteniment'],
                'equip' => $row['equip_nom'],
                'missatge' => "Manteniment programat per a " . $row['equip_nom'],
                'nivell' => $nivell
            ];
        }
    }

    // 3. Alertes de Plagues (Captures > 15 en l'última setmana)
    $sql_plagues = "SELECT m.*, t.model, s.nom as sector_nom 
                    FROM monitoratge_plaga m 
                    JOIN trampa t ON m.id_trampa = t.id_trampa 
                    JOIN sector s ON t.id_sector = s.id_sector 
                    WHERE m.quantitat_capturada > 15 
                    AND m.data_registre >= DATE_SUB(CURDATE(), INTERVAL 15 DAY)";
    $res = $conn->query($sql_plagues);
    if ($res) {
        while($row = $res->fetch_assoc()) {
            $alerts['plagues'][] = [
                'data' => $row['data_registre'],
                'sector' => $row['sector_nom'],
                'plaga' => $row['plaga_objectiu'],
                'captures' => $row['quantitat_capturada'],
                'missatge' => "Nivell crític de " . $row['plaga_objectiu'] . " detectat."
            ];
        }
    }

    // 4. Alertes de Qualitat (Qualificacions C o D)
    $sql_qual = "SELECT cq.*, lp.codi_lot 
                 FROM control_qualitat cq 
                 JOIN lot_produccio lp ON cq.lot_id = lp.lot_id 
                 WHERE cq.qualificacio_final IN ('C', 'D', 'Rebuig') 
                 ORDER BY cq.data_control DESC";
    $res = $conn->query($sql_qual);
    if ($res) {
        while($row = $res->fetch_assoc()) {
            $alerts['qualitat'][] = [
                'data' => $row['data_control'],
                'lot' => $row['codi_lot'],
                'qualificacio' => $row['qualificacio_final'],
                'defectes' => $row['defectes']
            ];
        }
    }

    $conn->close();
    return $alerts;
}

/**
 * Obté el detall de les alertes tècniques/producció (Legacy table)
 */
function get_produccio_alerts() {
    $conn = db_connect();
    $alerts = [];
    $sql = "SELECT a.*, s.nom as sector_nom 
            FROM alerta_produccio a 
            LEFT JOIN sector s ON a.id_sector = s.id_sector 
            WHERE a.estat != 'Resolta' 
            ORDER BY a.data_avis DESC";
    $res = $conn->query($sql);
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $alerts[] = $row;
        }
    }
    $conn->close();
    return $alerts;
}

/**
 * Obté el detall de les alertes de manteniment (proxim_manteniment <= AVUI + 15 dies)
 */
function get_manteniment_alerts() {
    $conn = db_connect();
    $alerts = [];
    $sql = "SELECT m.*, e.tipus as equip_nom 
            FROM manteniment_maquinaria m 
            JOIN equip e ON m.id_equip = e.id_equip 
            WHERE m.proxim_manteniment <= DATE_ADD(CURDATE(), INTERVAL 15 DAY) 
            ORDER BY m.proxim_manteniment ASC";
    $res = $conn->query($sql);
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $alerts[] = $row;
        }
    }
    $conn->close();
    return $alerts;
}

/**
 * Obté el detall de captures de plagues crítiques
 */
function get_plagues_alerts() {
    $conn = db_connect();
    $alerts = [];
    $sql = "SELECT m.*, t.model, s.nom as sector_nom 
            FROM monitoratge_plaga m 
            JOIN trampa t ON m.id_trampa = t.id_trampa 
            JOIN sector s ON t.id_sector = s.id_sector 
            WHERE m.quantitat_capturada > 15 
            AND m.data_registre >= DATE_SUB(CURDATE(), INTERVAL 15 DAY) 
            ORDER BY m.data_registre DESC";
    $res = $conn->query($sql);
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $alerts[] = $row;
        }
    }
    $conn->close();
    return $alerts;
}

/**
 * Obté el detall de controls de qualitat deficients
 */
function get_qualitat_alerts() {
    $conn = db_connect();
    $alerts = [];
    $sql = "SELECT cq.*, lp.codi_lot 
            FROM control_qualitat cq 
            JOIN lot_produccio lp ON cq.lot_id = lp.lot_id 
            WHERE cq.qualificacio_final IN ('C', 'D', 'Rebuig') 
            ORDER BY cq.data_control DESC";
    $res = $conn->query($sql);
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $alerts[] = $row;
        }
    }
    $conn->close();
    return $alerts;
}
?>
