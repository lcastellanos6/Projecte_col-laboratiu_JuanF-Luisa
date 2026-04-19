<?php
require_once __DIR__ . '/alertes_logic.php';

/**
 * Summary function for technical alerts as expected by index.php
 */
function get_tecnica_alert_summary() {
    $summary = get_all_alerts_summary();
    return [
        'available' => true,
        'count' => $summary['tecnica_total'] ?? 0,
        'error' => ''
    ];
}
