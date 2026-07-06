<?php
/**
 * SwiNOG · Venue map · server-side geocode + static-map caching
 *
 * On `save_post_page`, if the page has a `swinog_event_address` meta
 * value, we:
 *   1. Geocode it via OpenStreetMap Nominatim (no API key required).
 *   2. Fetch a static PNG of the area from staticmap.openstreetmap.de.
 *   3. Save the PNG under wp-content/uploads/swinog-venue-maps/event-<id>.png.
 *   4. Persist the resulting URL + lat/lng + a hash of the address
 *      to post meta so the venue block can render it on the front-end.
 *
 * Visitors hit only your server — no third-party requests from the
 * rendered page (the privacy stance called out in the design handoff).
 *
 * Cache invalidation is by md5 of the trimmed address: if the editor
 * changes the address, we refetch; otherwise the file is reused.
 *
 * @package SwiNOG
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

const SWINOG_VENUE_MAP_URL_META  = 'swinog_event_map_url';
const SWINOG_VENUE_MAP_PATH_META = 'swinog_event_map_path';
const SWINOG_VENUE_MAP_LAT_META  = 'swinog_event_map_lat';
const SWINOG_VENUE_MAP_LNG_META  = 'swinog_event_map_lng';
const SWINOG_VENUE_MAP_HASH_META = 'swinog_event_map_hash';

/**
 * Return ['path' => …, 'url' => …] for the venue-map cache directory.
 */
function swinog_venue_uploads_dir(): array
{
    $upload = wp_upload_dir();
    return [
        'path' => trailingslashit($upload['basedir']) . 'swinog-venue-maps',
        'url'  => trailingslashit($upload['baseurl']) . 'swinog-venue-maps',
    ];
}

/**
 * User-Agent string used for outbound requests. Nominatim requires a
 * valid UA that identifies the calling app + contact.
 */
function swinog_venue_user_agent(): string
{
    $host  = wp_parse_url(home_url(), PHP_URL_HOST) ?: 'swinog.ch';
    $email = (string) get_bloginfo('admin_email');
    return sprintf('SwiNOG WordPress theme (+https://%s; %s)', $host, $email);
}

/**
 * Build the ordered list of geocode queries to try for an address.
 *
 * Editors enter multi-line postal addresses ("Venue name / c/o line /
 * street / PLZ town"), which Nominatim usually can't match as a single
 * free-form query. Fallbacks: the last two lines (street + town) are
 * the most reliable match; the first line (venue name) catches POIs
 * Nominatim knows by name.
 */
function swinog_geocode_candidates(string $address): array
{
    $lines = preg_split('/\R+/', $address) ?: [];
    $lines = array_values(array_filter(array_map('trim', $lines), static fn(string $l): bool => $l !== ''));
    if ($lines === []) {
        return [];
    }

    $candidates = [implode(', ', $lines)];
    if (count($lines) >= 2) {
        $candidates[] = implode(', ', array_slice($lines, -2));
        $candidates[] = $lines[0];
    }

    return array_values(array_unique($candidates));
}

/**
 * Geocode an address via Nominatim, trying each candidate query in
 * order (see swinog_geocode_candidates). Returns
 * ['lat','lng','display_name'] or null when nothing matches.
 */
function swinog_geocode_address(string $address): ?array
{
    $first = true;
    foreach (swinog_geocode_candidates(trim($address)) as $query) {
        if (!$first) {
            // Nominatim usage policy: ≤ 1 req/sec.
            usleep(1_100_000);
        }
        $first = false;

        $geo = swinog_geocode_query($query);
        if ($geo !== null) {
            return $geo;
        }
    }

    return null;
}

/**
 * Run a single Nominatim query. Returns ['lat','lng','display_name']
 * or null on failure.
 */
function swinog_geocode_query(string $query): ?array
{
    $url = add_query_arg([
        'q'              => rawurlencode($query),
        'format'         => 'json',
        'limit'          => 1,
        'addressdetails' => 0,
    ], 'https://nominatim.openstreetmap.org/search');

    $response = wp_safe_remote_get($url, [
        'timeout'    => 8,
        'user-agent' => swinog_venue_user_agent(),
        'headers'    => ['Accept-Language' => 'en'],
    ]);

    if (is_wp_error($response) || (int) wp_remote_retrieve_response_code($response) !== 200) {
        return null;
    }

    $data = json_decode((string) wp_remote_retrieve_body($response), true);
    if (!is_array($data) || !isset($data[0]['lat'], $data[0]['lon'])) {
        return null;
    }

    return [
        'lat'          => (float) $data[0]['lat'],
        'lng'          => (float) $data[0]['lon'],
        'display_name' => (string) ($data[0]['display_name'] ?? ''),
    ];
}

/**
 * Convert a (lng, lat, zoom) triple to absolute pixel coords in the
 * world map at that zoom (Web Mercator, 256×256 tile size).
 *
 * @return array{0:float,1:float}  [pixelX, pixelY]
 */
function swinog_venue_lonlat_to_pixel(float $lng, float $lat, int $zoom): array
{
    $size   = 256.0 * (1 << $zoom);
    $x      = (($lng + 180.0) / 360.0) * $size;
    $sinLat = sin(deg2rad($lat));
    $y      = (0.5 - log((1.0 + $sinLat) / (1.0 - $sinLat)) / (4.0 * M_PI)) * $size;
    return [$x, $y];
}

/**
 * Fetch a single OSM tile PNG. Returns the raw bytes, or null on error.
 */
function swinog_fetch_osm_tile(int $z, int $x, int $y): ?string
{
    $url = sprintf('https://tile.openstreetmap.org/%d/%d/%d.png', $z, $x, $y);
    $response = wp_safe_remote_get($url, [
        'timeout'    => 8,
        'user-agent' => swinog_venue_user_agent(),
        'headers'    => ['Referer' => home_url('/')],
    ]);
    if (is_wp_error($response) || (int) wp_remote_retrieve_response_code($response) !== 200) {
        return null;
    }
    $body = (string) wp_remote_retrieve_body($response);
    return $body !== '' ? $body : null;
}

/**
 * Build a static map PNG for (lat, lng) by fetching the OSM tiles
 * that cover the requested viewport and compositing them with GD,
 * then drawing a marker dot at the center.
 *
 * Self-hosted compositing — no third-party static-map service is
 * touched. Tile usage stays under OSM's policy at typical SwiNOG
 * volumes (≤ a handful of events per year × ~6 tiles per map).
 */
function swinog_fetch_venue_map_image(float $lat, float $lng): ?string
{
    if (!function_exists('imagecreatetruecolor') || !function_exists('imagecreatefromstring')) {
        error_log('SwiNOG venue-map: PHP GD not available — cannot composite tiles.');
        return null;
    }

    $zoom   = 15;
    $width  = 800;
    $height = 500;

    [$cx, $cy] = swinog_venue_lonlat_to_pixel($lng, $lat, $zoom);

    $left = $cx - $width  / 2;
    $top  = $cy - $height / 2;

    $tileLeft   = (int) floor($left   / 256);
    $tileTop    = (int) floor($top    / 256);
    $tileRight  = (int) floor(($left   + $width)  / 256);
    $tileBottom = (int) floor(($top    + $height) / 256);

    $canvas = imagecreatetruecolor($width, $height);
    // Soft paper-ish background so blank-tile gaps are not pure black.
    $bg = imagecolorallocate($canvas, 240, 237, 232);
    imagefilledrectangle($canvas, 0, 0, $width, $height, $bg);

    $any_tile = false;
    for ($tx = $tileLeft; $tx <= $tileRight; $tx++) {
        for ($ty = $tileTop; $ty <= $tileBottom; $ty++) {
            $bytes = swinog_fetch_osm_tile($zoom, $tx, $ty);
            if ($bytes === null) {
                continue;
            }
            $tile = @imagecreatefromstring($bytes); // phpcs:ignore WordPress.PHP.NoSilencedErrors
            if ($tile === false) {
                continue;
            }
            $dstX = (int) round($tx * 256 - $left);
            $dstY = (int) round($ty * 256 - $top);
            imagecopy($canvas, $tile, $dstX, $dstY, 0, 0, 256, 256);
            imagedestroy($tile);
            $any_tile = true;
            // Be polite between tile requests.
            usleep(150_000);
        }
    }

    if (!$any_tile) {
        imagedestroy($canvas);
        error_log('SwiNOG venue-map: no OSM tiles could be fetched.');
        return null;
    }

    // Marker dot at the centre — white halo, accent-red fill, dark
    // outline. Matches the SwiNOG palette. 
    $mx     = (int) round($width  / 2);
    $my     = (int) round($height / 2);
    $halo   = imagecolorallocate($canvas, 255, 255, 255);
    $accent = imagecolorallocate($canvas, 216,  58,  44);
    $shadow = imagecolorallocate($canvas, 130,  30,  20);
    imagefilledellipse($canvas, $mx, $my, 26, 26, $halo);
    imagefilledellipse($canvas, $mx, $my, 18, 18, $accent);
    imageellipse($canvas, $mx, $my, 18, 18, $shadow);

    ob_start();
    imagepng($canvas);
    $png = (string) ob_get_clean();
    imagedestroy($canvas);

    return $png !== '' ? $png : null;
}

/**
 * Refresh the cached map for a post. Idempotent: if the address
 * hasn't changed since last fetch (md5 match) and the file still
 * exists, this is a no-op.
 *
 * Removes the cached file + meta when the address has been cleared.
 */
function swinog_refresh_venue_map(int $post_id): void
{
    $address = trim((string) get_post_meta($post_id, SWINOG_EVENT_ADDRESS_META, true));

    if ($address === '') {
        $existing = (string) get_post_meta($post_id, SWINOG_VENUE_MAP_PATH_META, true);
        if ($existing !== '' && file_exists($existing)) {
            @unlink($existing); // phpcs:ignore WordPress.PHP.NoSilencedErrors
        }
        foreach ([
            SWINOG_VENUE_MAP_URL_META, SWINOG_VENUE_MAP_PATH_META,
            SWINOG_VENUE_MAP_LAT_META, SWINOG_VENUE_MAP_LNG_META,
            SWINOG_VENUE_MAP_HASH_META,
        ] as $m) {
            delete_post_meta($post_id, $m);
        }
        return;
    }

    $hash        = md5($address);
    $cached_hash = (string) get_post_meta($post_id, SWINOG_VENUE_MAP_HASH_META, true);
    $cached_path = (string) get_post_meta($post_id, SWINOG_VENUE_MAP_PATH_META, true);

    if ($cached_hash === $hash && $cached_path !== '' && file_exists($cached_path)) {
        return;
    }

    $geo = swinog_geocode_address($address);
    if ($geo === null) {
        error_log('SwiNOG venue-map: geocoding failed for post ' . $post_id . ' — address: ' . $address);
        return;
    }

    // Nominatim usage policy: ≤ 1 req/sec.
    usleep(1_100_000);

    $image = swinog_fetch_venue_map_image($geo['lat'], $geo['lng']);
    if ($image === null) {
        error_log(sprintf('SwiNOG venue-map: tile composition failed for post %d at %.5f, %.5f', $post_id, $geo['lat'], $geo['lng']));
        return;
    }

    $dirs = swinog_venue_uploads_dir();
    if (!wp_mkdir_p($dirs['path'])) {
        return;
    }

    $filename = sprintf('event-%d.png', $post_id);
    $path     = trailingslashit($dirs['path']) . $filename;
    $url      = trailingslashit($dirs['url'])  . $filename;

    if (file_put_contents($path, $image) === false) { // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
        return;
    }

    update_post_meta($post_id, SWINOG_VENUE_MAP_PATH_META, $path);
    update_post_meta($post_id, SWINOG_VENUE_MAP_URL_META,  $url);
    update_post_meta($post_id, SWINOG_VENUE_MAP_LAT_META,  $geo['lat']);
    update_post_meta($post_id, SWINOG_VENUE_MAP_LNG_META,  $geo['lng']);
    update_post_meta($post_id, SWINOG_VENUE_MAP_HASH_META, $hash);
}

/**
 * Hook into save_post_page after the event-details save handler has
 * already persisted the new address (priority 20 puts us after that
 * handler — its priority is 10 by default).
 */
add_action('save_post_page', static function (int $post_id): void {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (wp_is_post_revision($post_id)) {
        return;
    }
    if (!current_user_can('edit_page', $post_id)) {
        return;
    }
    swinog_refresh_venue_map($post_id);
}, 20);
