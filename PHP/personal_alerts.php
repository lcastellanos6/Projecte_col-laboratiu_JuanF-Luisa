<?php
require_once __DIR__ . '/db.php';

/**
 * Personal alerts (single source of truth).
 *
 * Sources:
 * - Contracts ending soon (`contracte.data_finalitzacio`)
 * - Certifications expiring soon (`treballador_formacio_cert.data_caducitat`)
 * - Manual/preset alerts (`alerta`) still pending
 */

function fetch_all_assoc(mysqli_result $result): array
{
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    $result->free();
    return $rows;
}

function get_personal_alerts(int $dies = 30, ?mysqli $conn = null, ?int $id_treballador = null): array
{
    $dies = max(1, min(365, $dies));
    $owns = false;
    if ($conn === null) {
        $conn = db_connect();
        $owns = true;
    }

    $payload = [
        'available' => true,
        'alerts'    => [],
        'error'     => '',
    ];

    $whereClause = $id_treballador ? " AND t.id_treballador = ? " : "";

    // 1) Contracts ending soon
    $sqlContractes = "
        SELECT
          t.id_treballador,
          t.nom_complet,
          'Contracte' AS tipus_alerta,
          c.data_finalitzacio AS data_avis,
          CONCAT('Finalitza el contracte (', COALESCE(c.tipus_contracte, '—'), ')') AS titol,
          CONCAT('Contracte #', c.id_contracte, ' · Lloc: ', COALESCE(c.lloc_treball, '—')) AS detall
        FROM contracte c
        JOIN treballador t ON t.id_treballador = c.id_treballador
        WHERE c.data_finalitzacio IS NOT NULL
          AND c.data_finalitzacio <= DATE_ADD(CURDATE(), INTERVAL ? DAY)
          $whereClause
        ORDER BY c.data_finalitzacio ASC
    ";
    
    $stmt = $conn->prepare($sqlContractes);
    if (!$stmt) {
        $payload['available'] = false;
        $payload['error'] = 'No s\'ha pogut preparar la consulta de contractes.';
        if ($owns) $conn->close();
        return $payload;
    }
    
    if ($id_treballador) {
        $stmt->bind_param('ii', $dies, $id_treballador);
    } else {
        $stmt->bind_param('i', $dies);
    }
    
    $stmt->execute();
    $res = $stmt->get_result();
    $contractes = $res ? fetch_all_assoc($res) : [];
    $stmt->close();

    // 2) Certifications expiring soon
    $sqlCerts = "
        SELECT
          t.id_treballador,
          t.nom_complet,
          'Certificacio' AS tipus_alerta,
          tfc.data_caducitat AS data_avis,
          CONCAT('Caduca: ', fc.nom) AS titol,
          CONCAT('Formació/cert #', tfc.id_tfc, ' · Hores: ', COALESCE(tfc.hores, '—')) AS detall
        FROM treballador_formacio_cert tfc
        JOIN treballador t ON t.id_treballador = tfc.id_treballador
        JOIN formacio_certificacio fc ON fc.id_formacio_cert = tfc.id_formacio_cert
        WHERE tfc.data_caducitat IS NOT NULL
          AND tfc.data_caducitat <= DATE_ADD(CURDATE(), INTERVAL ? DAY)
          $whereClause
        ORDER BY tfc.data_caducitat ASC
    ";
    
    $stmt = $conn->prepare($sqlCerts);
    if (!$stmt) {
        $payload['available'] = false;
        $payload['error'] = 'No s\'ha pogut preparar la consulta de certificacions.';
        if ($owns) $conn->close();
        return $payload;
    }
    
    if ($id_treballador) {
        $stmt->bind_param('ii', $dies, $id_treballador);
    } else {
        $stmt->bind_param('i', $dies);
    }
    
    $stmt->execute();
    $res = $stmt->get_result();
    $certs = $res ? fetch_all_assoc($res) : [];
    $stmt->close();

    // 3) Existing alerts table (pending)
    $whereClauseManual = $id_treballador ? " AND a.id_treballador = ? " : "";
    $sqlManual = "
        SELECT
          a.id_treballador,
          t.nom_complet,
          a.tipus_alerta,
          a.data_avis,
          CONCAT('Alerta: ', a.tipus_alerta) AS titol,
          COALESCE(a.observacions, '') AS detall
        FROM alerta a
        JOIN treballador t ON t.id_treballador = a.id_treballador
        WHERE a.estat = 'Pendent'
        $whereClauseManual
        ORDER BY a.data_avis ASC
    ";
    
    $stmt = $conn->prepare($sqlManual);
    if (!$stmt) {
        $payload['available'] = false;
        $payload['error'] = 'No s\'ha pogut preparar la consulta d\'alertes.';
        if ($owns) $conn->close();
        return $payload;
    }
    
    if ($id_treballador) {
        $stmt->bind_param('i', $id_treballador);
    }
    
    $stmt->execute();
    $res = $stmt->get_result();
    $manuals = $res ? fetch_all_assoc($res) : [];
    $stmt->close();

    // 4) EPIs expiring soon
    $sqlEPIs = "
        SELECT
          t.id_treballador,
          t.nom_complet,
          'EPI' AS tipus_alerta,
          le.data_caducitat AS data_avis,
          CONCAT('Caducitat EPI: ', et.nom) AS titol,
          CONCAT('Lliurament #', le.id_lliurament, ' · Quantitat: ', le.quantitat) AS detall
        FROM epi_lliurament le
        JOIN treballador t ON t.id_treballador = le.id_treballador
        JOIN epi_tipus et ON et.id_epi_tipus = le.id_epi_tipus
        WHERE le.data_caducitat IS NOT NULL
          AND le.data_caducitat <= DATE_ADD(CURDATE(), INTERVAL ? DAY)
          $whereClause
        ORDER BY le.data_caducitat ASC
    ";
    
    $stmt = $conn->prepare($sqlEPIs);
    if ($stmt) {
        if ($id_treballador) {
            $stmt->bind_param('ii', $dies, $id_treballador);
        } else {
            $stmt->bind_param('i', $dies);
        }
        $stmt->execute();
        $res = $stmt->get_result();
        $epis = $res ? fetch_all_assoc($res) : [];
        $stmt->close();
    } else {
        $epis = [];
    }

    // 5) Absències que finalitzen aviat (especialment Baixes)
    $sqlAbsencies = "
        SELECT
          t.id_treballador,
          t.nom_complet,
          'Absència' AS tipus_alerta,
          ab.data_fi AS data_avis,
          CONCAT('Finalitza ', ab.tipus, ' (', ab.estat, ')') AS titol,
          COALESCE(ab.observacions, '') AS detall
        FROM absencia ab
        JOIN treballador t ON t.id_treballador = ab.id_treballador
        WHERE ab.estat != 'Tancada' AND ab.estat != 'Rebutjada'
          AND ab.data_fi IS NOT NULL
          AND ab.data_fi <= DATE_ADD(CURDATE(), INTERVAL ? DAY)
          $whereClause
        ORDER BY ab.data_fi ASC
    ";
    
    $stmt = $conn->prepare($sqlAbsencies);
    if ($stmt) {
        if ($id_treballador) {
            $stmt->bind_param('ii', $dies, $id_treballador);
        } else {
            $stmt->bind_param('i', $dies);
        }
        $stmt->execute();
        $res = $stmt->get_result();
        $absencies = $res ? fetch_all_assoc($res) : [];
        $stmt->close();
    } else {
        $absencies = [];
    }

    // 6) Documents expiring soon
    $sqlDocs = "
        SELECT
          t.id_treballador,
          t.nom_complet,
          'Document' AS tipus_alerta,
          rd.data_caducitat AS data_avis,
          CONCAT('Caducitat Document: ', rd.nom_document) AS titol,
          COALESCE(rd.observacions, '') AS detall
        FROM registre_document rd
        JOIN treballador t ON t.id_treballador = rd.id_treballador
        WHERE rd.data_caducitat IS NOT NULL
          AND rd.data_caducitat <= DATE_ADD(CURDATE(), INTERVAL ? DAY)
          $whereClause
        ORDER BY rd.data_caducitat ASC
    ";
    
    $stmt = $conn->prepare($sqlDocs);
    if ($stmt) {
        if ($id_treballador) {
            $stmt->bind_param('ii', $dies, $id_treballador);
        } else {
            $stmt->bind_param('i', $dies);
        }
        $stmt->execute();
        $res = $stmt->get_result();
        $docs = $res ? fetch_all_assoc($res) : [];
        $stmt->close();
    } else {
        $docs = [];
    }

    $alerts = array_merge($contractes, $certs, $manuals, $epis, $absencies, $docs);

    // Normalize for UI
    foreach ($alerts as &$a) {
        $a['id_treballador'] = (int) ($a['id_treballador'] ?? 0);
        $a['nom_complet'] = (string) ($a['nom_complet'] ?? '');
        $a['tipus_alerta'] = (string) ($a['tipus_alerta'] ?? '');
        $a['data_avis'] = (string) ($a['data_avis'] ?? '');
        $a['titol'] = (string) ($a['titol'] ?? '');
        $a['detall'] = (string) ($a['detall'] ?? '');
        $a['is_overdue'] = ($a['data_avis'] !== '' && $a['data_avis'] < date('Y-m-d'));
    }
    unset($a);

    $payload['alerts'] = $alerts;

    if ($owns) {
        $conn->close();
    }
    return $payload;
}

function get_personal_alert_summary(int $dies = 30, ?mysqli $conn = null, ?int $id_treballador = null): array
{
    $result = get_personal_alerts($dies, $conn, $id_treballador);
    return [
        'available' => (bool) ($result['available'] ?? false),
        'count'     => is_array($result['alerts'] ?? null) ? count($result['alerts']) : 0,
        'error'     => (string) ($result['error'] ?? ''),
    ];
}