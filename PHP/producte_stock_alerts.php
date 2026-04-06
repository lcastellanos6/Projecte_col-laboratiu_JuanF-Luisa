<?php
require_once __DIR__ . '/db.php';

/**
 * Check whether producte.stock_minim exists in the current database.
 */
function producte_has_stock_minim_column(mysqli $conn): bool
{
    $sql = "
        SELECT 1
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = 'producte'
          AND COLUMN_NAME = 'stock_minim'
        LIMIT 1
    ";
    $result = $conn->query($sql);
    if (!$result) {
        return false;
    }

    $exists = $result->num_rows > 0;
    $result->free();
    return $exists;
}

/**
 * Single source of truth for product stock-minimum alerts.
 *
 * @return array{
 *   available: bool,
 *   alerts: array<int, array{
 *     id_producte:int,
 *     nom_comercial:string,
 *     stock_minim:int,
 *     stock_actual:float
 *   }>,
 *   error: string
 * }
 */
function get_producte_stock_alerts(?mysqli $conn = null): array
{
    $ownsConnection = false;
    if ($conn === null) {
        $conn = db_connect();
        $ownsConnection = true;
    }

    $payload = [
        'available' => false,
        'alerts' => [],
        'error' => ''
    ];

    if (!producte_has_stock_minim_column($conn)) {
        $payload['error'] = "Missing column producte.stock_minim. Run DB migration before enabling stock alerts.";
        if ($ownsConnection) {
            $conn->close();
        }
        return $payload;
    }

    $sql = "
        SELECT
          p.id_producte,
          p.nom_comercial,
          p.stock_minim,
          COALESCE(SUM(pl.quantitat_disponible), 0) AS stock_actual
        FROM producte p
        LEFT JOIN producte_lot pl
          ON p.id_producte = pl.id_producte
        GROUP BY p.id_producte, p.nom_comercial, p.stock_minim
        HAVING COALESCE(SUM(pl.quantitat_disponible), 0) <= p.stock_minim
        ORDER BY p.nom_comercial
    ";

    $result = $conn->query($sql);
    if (!$result) {
        $payload['error'] = 'Error executing stock alert query.';
        if ($ownsConnection) {
            $conn->close();
        }
        return $payload;
    }

    $alerts = [];
    while ($row = $result->fetch_assoc()) {
        $alerts[] = [
            'id_producte' => (int) ($row['id_producte'] ?? 0),
            'nom_comercial' => (string) ($row['nom_comercial'] ?? ''),
            'stock_minim' => (int) ($row['stock_minim'] ?? 0),
            'stock_actual' => (float) ($row['stock_actual'] ?? 0)
        ];
    }
    $result->free();

    $payload['available'] = true;
    $payload['alerts'] = $alerts;

    if ($ownsConnection) {
        $conn->close();
    }

    return $payload;
}

/**
 * Reuse the same alert source to provide only summary data.
 */
function get_producte_stock_alert_summary(?mysqli $conn = null): array
{
    $result = get_producte_stock_alerts($conn);
    return [
        'available' => $result['available'],
        'count' => count($result['alerts']),
        'error' => $result['error']
    ];
}

