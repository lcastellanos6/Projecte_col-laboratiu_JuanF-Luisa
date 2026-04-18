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

function get_personal_alerts(int $dies = 30, ?mysqli $conn = null): array
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
        ORDER BY c.data_finalitzacio ASC
    ";
    
    $stmt = $conn->prepare($sqlContractes);
    if (!$stmt) {
        $payload['available'] = false;
        $payload['error'] = 'No s\'ha pogut preparar la consulta de contractes.';
        if ($owns) $conn->close();
        return $payload;
    }
    $stmt->bind_param('i', $dies);
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
        ORDER BY tfc.data_caducitat ASC
    ";
    
    $stmt = $conn->prepare($sqlCerts);
    if (!$stmt) {
        $payload['available'] = false;
        $payload['error'] = 'No s\'ha pogut preparar la consulta de certificacions.';
        if ($owns) $conn->close();
        return $payload;
    }
    $stmt->bind_param('i', $dies);
    $stmt->execute();
    $res = $stmt->get_result();
    $certs = $res ? fetch_all_assoc($res) : [];
    $stmt->close();

    // 3) Existing alerts table (pending)
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
          AND a.data_avis <= DATE_ADD(CURDATE(), INTERVAL ? DAY)
        ORDER BY a.data_avis ASC
    ";
    
    $stmt = $conn->prepare($sqlManual);
    if (!$stmt) {
        $payload['available'] = false;
        $payload['error'] = 'No s\'ha pogut preparar la consulta d\'alertes.';
        if ($owns) $conn->close();
        return $payload;
    }
    $stmt->bind_param('i', $dies);
    $stmt->execute();
    $res = $stmt->get_result();
    $manuals = $res ? fetch_all_assoc($res) : [];
    $stmt->close();

    $alerts = array_merge($contractes, $certs, $manuals);

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

function get_personal_alert_summary(int $dies = 30, ?mysqli $conn = null): array
{
    $result = get_personal_alerts($dies, $conn);
    return [
        'available' => (bool) ($result['available'] ?? false),
        'count'     => is_array($result['alerts'] ?? null) ? count($result['alerts']) : 0,
        'error'     => (string) ($result['error'] ?? ''),
    ];
}