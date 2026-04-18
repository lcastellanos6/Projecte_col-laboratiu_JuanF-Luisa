<?php
function db_connect(): mysqli {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "web";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Error de connexió: " . $conn->connect_error);
    }

    $conn->set_charset("utf8mb4");
    return $conn;
}

/**
 * Leaflet `toGeoJSON()` returns a GeoJSON Feature. MariaDB/MySQL ST_GeomFromGeoJSON()
 * expects a GeoJSON Geometry object (Polygon, MultiPolygon, ...).
 *
 * Returns a JSON string for the geometry or null if invalid.
 */
function geojson_feature_to_geometry_json(?string $raw): ?string
{
    $raw = trim((string) $raw);
    if ($raw === '') {
        return null;
    }

    $decoded = json_decode($raw, true);
    if (!is_array($decoded)) {
        return null;
    }

    // If it's already a geometry object
    if (isset($decoded['type']) && isset($decoded['coordinates']) && is_string($decoded['type'])) {
        return json_encode(
            ['type' => $decoded['type'], 'coordinates' => $decoded['coordinates']],
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
    }

    // Feature -> geometry
    if (($decoded['type'] ?? null) === 'Feature' && isset($decoded['geometry']) && is_array($decoded['geometry'])) {
        $g = $decoded['geometry'];
        if (isset($g['type']) && isset($g['coordinates']) && is_string($g['type'])) {
            return json_encode(
                ['type' => $g['type'], 'coordinates' => $g['coordinates']],
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            );
        }
    }

    // FeatureCollection -> first geometry (best-effort for single drawn shape)
    if (($decoded['type'] ?? null) === 'FeatureCollection' && isset($decoded['features']) && is_array($decoded['features'])) {
        foreach ($decoded['features'] as $f) {
            if (!is_array($f) || (($f['type'] ?? null) !== 'Feature') || !isset($f['geometry']) || !is_array($f['geometry'])) {
                continue;
            }
            $g = $f['geometry'];
            if (isset($g['type']) && isset($g['coordinates']) && is_string($g['type'])) {
                return json_encode(
                    ['type' => $g['type'], 'coordinates' => $g['coordinates']],
                    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
                );
            }
        }
    }

    return null;
}
?>
