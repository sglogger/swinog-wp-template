<?php
/**
 * SwiNOG · wp-swinog-events integration
 *
 *   - Adds `swinog-events-active` body class when the plugin is loaded so
 *     CSS can target plugin-aware contexts.
 *   - Adds the `swinog-events` pattern category for wrapper patterns
 *     that embed the plugin's shortcodes inside SwiNOG section chrome.
 *
 * The shortcode patterns themselves live in /patterns/plugin-*.php.
 *
 * Theme CPT/meta registration is short-circuited in functions.php when
 * the plugin is detected — see swinog_events_plugin_active().
 *
 * @package SwiNOG
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

add_filter('body_class', static function (array $classes): array {
    if (function_exists('swinog_events_plugin_active') && swinog_events_plugin_active()) {
        $classes[] = 'swinog-events-active';
    }
    return $classes;
});

/**
 * WP 6.x's core/shortcode block render_callback is literally just
 * wpautop($content) — it never calls do_shortcode(). That's fine inside
 * post_content (where the_content filter runs do_shortcode for us),
 * but block-template patterns bypass that filter, so [swinog_list_agenda]
 * etc. would render as literal text wrapped in <p>.
 *
 * This filter post-processes the shortcode block's output through
 * do_shortcode so plugin shortcodes work everywhere — inside template
 * patterns, custom templates, and template parts, not just inside
 * post_content.
 */
add_filter('render_block_core/shortcode', static function (string $content): string {
    return do_shortcode($content);
}, 9);

/* ==================================================================
 * Override plugin shortcodes with SwiNOG-styled program rows.
 *
 *   [swinog_list_agenda]         — agenda (time / kind / title / who / mins)
 *   [swinog_list_presentations]  — post-event (title / who / mins / slides / video)
 *
 * Both render the same row component; only the visible columns differ.
 * Rows are click-to-expand showing the abstract underneath.
 * "Kind" (talk / break / breakout / social) is inferred from the title.
 *
 * Hooked at init priority 20 so the plugin (priority 10) has already
 * registered its versions and we cleanly replace them.
 * ================================================================== */

add_action('init', static function (): void {
    if (!function_exists('swinog_events_plugin_active') || !swinog_events_plugin_active()) {
        return;
    }
    foreach (['swinog_list_agenda', 'swinog_list_presentations', 'stgl_list_presentations', 'swinog_list_all_events', 'stgl_childpages'] as $sc) {
        remove_shortcode($sc);
    }
    add_shortcode('swinog_list_agenda',        'swinog_render_program_agenda');
    add_shortcode('swinog_list_presentations', 'swinog_render_program_presentations');
    add_shortcode('stgl_list_presentations',   'swinog_render_program_presentations');
    add_shortcode('swinog_list_all_events',    'swinog_render_events_overview');
    add_shortcode('stgl_childpages',           'swinog_render_events_overview');
}, 20);

function swinog_render_program_agenda($atts): string
{
    return swinog_render_program($atts, 'agenda');
}

function swinog_render_program_presentations($atts): string
{
    return swinog_render_program($atts, 'presentations');
}

/**
 * Infer a row kind from the presentation title.
 *
 * Editors can opt out of inference by setting the post meta
 * `stgl_presenter_kind` to one of: talk, break, breakout, social.
 */
function swinog_infer_program_kind(string $title, int $post_id = 0): string
{
    if ($post_id) {
        $explicit = (string) get_post_meta($post_id, 'stgl_presenter_kind', true);
        if (in_array($explicit, ['talk', 'social'], true)) {
            return $explicit;
        }
    }
    // Simplified rule per project convention:
    //   social = there is NO presenter set, AND the title matches one of
    //            the known non-talk slots ("social event", "morning break",
    //            "afternoon break", "coffee break", "lunch", "lunch break",
    //            "dinner", "reception").
    //   talk   = everything else (whether or not a presenter is set).
    $presenter = $post_id ? trim((string) get_post_meta($post_id, 'stgl_presenter_name', true)) : '';
    $t = mb_strtolower(trim($title));
    if ($presenter === '' && preg_match(
        '/^(social\s*event|morning\s*break|afternoon\s*break|coffee\s*break|lunch(?:\s*break)?|dinner|reception)$/u',
        $t
    )) {
        return 'social';
    }
    return 'talk';
}

/**
 * Return the public URL of a presentation's slide deck, if published.
 * Mirrors the plugin's private resolver so we don't depend on it.
 */
function swinog_resolve_presentation_slides(int $post_id): string
{
    $publish = (bool) get_post_meta($post_id, 'stgl_presenter_publish', true);
    if (!$publish) {
        return '';
    }
    $aid = (int) get_post_meta($post_id, '_stgl_presentation_attachment_id', true);
    if ($aid) {
        $url = wp_get_attachment_url($aid);
        if ($url) {
            return (string) $url;
        }
    }
    $legacy = get_post_meta($post_id, 'wp_custom_attachment', true);
    if (is_array($legacy) && !empty($legacy['url'])) {
        return (string) $legacy['url'];
    }
    return '';
}

function swinog_render_program($atts, string $mode): string
{
    $atts = shortcode_atts([
        'event'    => '',
        'orderby'  => 'meta_value',
        'order'    => 'ASC',
        'meta_key' => 'stgl_presenter_time',
        'posts'    => -1,
    ], (array) $atts, 'swinog_program');

    $event_slug = trim((string) $atts['event']);
    $term_name  = '';
    if ($event_slug !== '') {
        $term = get_term_by('slug', $event_slug, 'stgl_presentation_cat');
        if ($term instanceof WP_Term) {
            $term_name = $term->name;
        }
    }

    $query_args = [
        'post_type'              => 'stgl_presentation',
        'posts_per_page'         => (int) $atts['posts'],
        'orderby'                => $atts['orderby'],
        'meta_key'               => sanitize_key((string) $atts['meta_key']), // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
        'order'                  => strtoupper((string) $atts['order']) === 'DESC' ? 'DESC' : 'ASC',
        'no_found_rows'          => true,
        'update_post_term_cache' => false,
    ];
    if ($event_slug !== '') {
        $query_args['tax_query'] = [[
            'taxonomy' => 'stgl_presentation_cat',
            'field'    => 'slug',
            'terms'    => $event_slug,
        ]];
    }

    $query = new WP_Query($query_args);

    if (!$query->have_posts()) {
        return sprintf(
            '<div class="swinog-program-empty">%s</div>',
            esc_html(sprintf(
                /* translators: %s: event name */
                __('No published presentations for %s yet.', 'swinog'),
                $term_name ?: $event_slug ?: __('this event', 'swinog')
            ))
        );
    }

    ob_start();
    ?>
    <div class="swinog-program-shortcode swinog-program swinog-program--<?php echo esc_attr($mode); ?>" data-mode="<?php echo esc_attr($mode); ?>">
        <div class="swinog-program__rows">
        <?php
        while ($query->have_posts()) :
            $query->the_post();
            $post_id   = (int) get_the_ID();
            $title     = (string) get_the_title();
            $time      = (string) get_post_meta($post_id, 'stgl_presenter_time', true);
            $presenter = (string) get_post_meta($post_id, 'stgl_presenter_name', true);
            $company   = (string) get_post_meta($post_id, 'stgl_presenter_company', true);
            $length    = (int)    get_post_meta($post_id, 'stgl_presenter_lenght', true);
            $video_pub = (bool)   get_post_meta($post_id, 'stgl_presenter_publish_video', true);
            $video_url = (string) get_post_meta($post_id, 'stgl_presenter_videourl', true);
            $slides    = swinog_resolve_presentation_slides($post_id);
            $abstract  = trim((string) get_the_content());

            $kind = swinog_infer_program_kind($title, $post_id);
            $who  = trim($presenter . ($company !== '' ? ' · ' . $company : ''), " \t\n\r\0\x0B·");
            $has_abstract = $abstract !== '';
            ?>
            <article
                class="swinog-program__row swinog-program__row--<?php echo esc_attr($kind); ?><?php echo $has_abstract ? ' swinog-program__row--expandable' : ''; ?>"
                <?php if ($has_abstract) : ?>role="button" tabindex="0" aria-expanded="false"<?php endif; ?>
            >
                <?php if ($mode === 'agenda') : ?>
                    <span class="swinog-mono swinog-program__time"><?php echo esc_html($time); ?></span>
                    <span class="swinog-program__kind"><?php echo esc_html($kind); ?></span>
                    <span class="swinog-program__title"><?php echo esc_html($title); ?></span>
                    <span class="swinog-program__who"><?php echo esc_html($who); ?></span>
                    <span class="swinog-program__tail">
                        <span class="swinog-mono swinog-program__mins"><?php echo $length ? esc_html($length . 'm') : ''; ?></span>
                        <span class="swinog-program__chevron <?php echo $has_abstract ? '' : 'swinog-program__chevron--placeholder'; ?>" aria-hidden="true">▾</span>
                    </span>
                <?php else : /* presentations · no duration column */ ?>
                    <span class="swinog-program__title"><?php echo esc_html($title); ?></span>
                    <span class="swinog-program__who"><?php echo esc_html($who); ?></span>
                    <span class="swinog-program__link">
                        <?php if ($slides !== '') : ?>
                            <a href="<?php echo esc_url($slides); ?>" target="_blank" rel="noopener" onclick="event.stopPropagation();"><?php esc_html_e('Slides', 'swinog'); ?></a>
                        <?php else : ?>—<?php endif; ?>
                    </span>
                    <span class="swinog-program__tail">
                        <span class="swinog-program__link">
                            <?php if ($video_pub && $video_url !== '') : ?>
                                <a href="<?php echo esc_url($video_url); ?>" target="_blank" rel="noopener" onclick="event.stopPropagation();"><?php esc_html_e('Video', 'swinog'); ?></a>
                            <?php else : ?>—<?php endif; ?>
                        </span>
                        <span class="swinog-program__chevron <?php echo $has_abstract ? '' : 'swinog-program__chevron--placeholder'; ?>" aria-hidden="true">▾</span>
                    </span>
                <?php endif; ?>
                <?php if ($has_abstract) : ?>
                    <div class="swinog-program__abstract"><?php echo wp_kses_post(apply_filters('the_content', $abstract)); ?></div>
                <?php endif; ?>
            </article>
            <?php
        endwhile;
        wp_reset_postdata();
        ?>
        </div>
    </div>
    <?php
    return (string) ob_get_clean();
}

/**
 * Enqueue the click-to-expand JS on the front end. Tiny file, no deps.
 */
add_action('wp_enqueue_scripts', static function (): void {
    $theme = wp_get_theme();
    wp_enqueue_script(
        'swinog-program-toggle',
        get_theme_file_uri('/assets/js/program-toggle.js'),
        [],
        $theme->get('Version'),
        true
    );
});

add_action('init', static function (): void {
    register_block_pattern_category('swinog-events', [
        'label'       => __('SwiNOG · Events plugin', 'swinog'),
        'description' => __('Wrappers that embed wp-swinog-events shortcodes in SwiNOG section chrome.', 'swinog'),
    ]);

    // Server-rendered "Recent talks" block — multi-event filter, dynamic.
    $agenda_dir = get_theme_file_path('/blocks/agenda');
    if (is_dir($agenda_dir) && file_exists($agenda_dir . '/block.json')) {
        register_block_type($agenda_dir);
    }

    // Server-rendered "Breadcrumbs" block — automatic + per-page hideable.
    $crumb_dir = get_theme_file_path('/blocks/breadcrumbs');
    if (is_dir($crumb_dir) && file_exists($crumb_dir . '/block.json')) {
        register_block_type($crumb_dir);
    }

    // Server-rendered "Event hero" block — pulls page title + meta.
    $hero_dir = get_theme_file_path('/blocks/event-hero');
    if (is_dir($hero_dir) && file_exists($hero_dir . '/block.json')) {
        register_block_type($hero_dir);
    }

    // Server-rendered "Event venue" block — renders the cached OSM map.
    $venue_dir = get_theme_file_path('/blocks/venue');
    if (is_dir($venue_dir) && file_exists($venue_dir . '/block.json')) {
        register_block_type($venue_dir);
    }

    // Server-rendered "Venue map" block — map image only, intended
    // to drop into the editable venue pattern in place of the SVG.
    $venue_map_dir = get_theme_file_path('/blocks/venue-map');
    if (is_dir($venue_map_dir) && file_exists($venue_map_dir . '/block.json')) {
        register_block_type($venue_map_dir);
    }

    // Server-rendered "Event Quick Facts" block — the right-column
    // card on event-detail pages. Drops into the editable event-hero
    // pattern next to the left-column copy blocks.
    $qf_dir = get_theme_file_path('/blocks/event-quickfacts');
    if (is_dir($qf_dir) && file_exists($qf_dir . '/block.json')) {
        register_block_type($qf_dir);
    }

    // Server-rendered "Event status pill" — the small kicker above
    // the H1 in the hero. Reads from swinog_event_pill meta.
    $pill_dir = get_theme_file_path('/blocks/event-pill');
    if (is_dir($pill_dir) && file_exists($pill_dir . '/block.json')) {
        register_block_type($pill_dir);
    }
}, 20);

/**
 * Ensure the agenda block's editor script can resolve its dependencies.
 *
 * register_block_type_from_metadata() reads `editorScript` from
 * block.json but doesn't know about its WP package deps — we declare
 * them here so InspectorControls / ServerSideRender / PanelBody all
 * load before the block script runs.
 */
add_filter('block_type_metadata', static function (array $metadata): array {
    $name = (string) ($metadata['name'] ?? '');
    if (!in_array($name, ['swinog/agenda', 'swinog/venue', 'swinog/venue-map', 'swinog/event-quickfacts', 'swinog/event-pill'], true)) {
        return $metadata;
    }
    $deps = ['wp-blocks', 'wp-element', 'wp-i18n', 'wp-components', 'wp-block-editor', 'wp-server-side-render'];
    if ($name === 'swinog/agenda') {
        $deps[] = 'wp-api-fetch';
    }
    $existing = $metadata['editorScriptDeps'] ?? $metadata['editorScript-dependencies'] ?? [];
    $metadata['editorScriptDeps'] = array_values(array_unique(array_merge((array) $existing, $deps)));
    return $metadata;
});

/* ==================================================================
 * [swinog_list_all_events] — styled events overview.
 *
 * Lists child pages of the current page (or the page given by
 * `parent="<id>"`), pulling each child's title, date and location
 * from the swinog_event_* meta fields. Sorted newest-first by date,
 * falling back to menu_order when no date is set.
 * ================================================================== */

function swinog_render_events_overview($atts): string
{
    $atts = shortcode_atts([
        'parent'    => 0,
        'order'     => 'desc',
        'show_past' => '1',
    ], (array) $atts, 'swinog_list_all_events');

    $parent = (int) $atts['parent'];
    if ($parent === 0) {
        $current = get_post();
        if ($current instanceof WP_Post) {
            $parent = (int) ($current->ID);
        }
    }
    if ($parent === 0) {
        return '';
    }

    $children = get_posts([
        'post_type'      => 'page',
        'posts_per_page' => -1,
        'post_parent'    => $parent,
        'post_status'    => 'publish',
        'orderby'        => 'menu_order title',
        'order'          => 'ASC',
    ]);

    if (!$children) {
        return '';
    }

    // Pair each page with its date for sorting, then sort by date desc.
    $rows = array_map(static function (WP_Post $p): array {
        $date = (string) get_post_meta($p->ID, 'swinog_event_date', true);
        $ts   = $date !== '' ? strtotime($date) : 0;
        return [
            'post' => $p,
            'date' => $date,
            'ts'   => $ts ?: 0,
            'loc'  => (string) get_post_meta($p->ID, 'swinog_event_location', true),
            'tag'  => (string) get_post_meta($p->ID, 'swinog_event_tag', true),
        ];
    }, $children);

    $dir = strtolower((string) $atts['order']) === 'asc' ? SORT_ASC : SORT_DESC;
    usort($rows, static fn ($a, $b) => $dir === SORT_ASC
        ? ($a['ts'] <=> $b['ts'])
        : ($b['ts'] <=> $a['ts'])
    );

    ob_start();
    ?>
    <div class="swinog-event-overview">
        <?php foreach ($rows as $row) :
            $p     = $row['post'];
            $date  = function_exists('swinog_format_event_date') ? swinog_format_event_date($row['date']) : $row['date'];
            $url   = (string) get_permalink($p);
            $past  = $row['ts'] > 0 && $row['ts'] < current_time('timestamp');
            ?>
            <a class="swinog-event-overview__row<?php echo $past ? ' swinog-event-overview__row--past' : ' swinog-event-overview__row--upcoming'; ?>" href="<?php echo esc_url($url); ?>">
                <div class="swinog-event-overview__date">
                    <?php if ($date !== '') : ?>
                        <span class="swinog-event-overview__date-main"><?php echo esc_html($date); ?></span>
                    <?php else : ?>
                        <span class="swinog-event-overview__date-main swinog-ink-3"><?php esc_html_e('Date TBA', 'swinog'); ?></span>
                    <?php endif; ?>
                </div>
                <div class="swinog-event-overview__body">
                    <h3 class="swinog-event-overview__title"><?php echo esc_html(get_the_title($p)); ?></h3>
                    <?php if ($row['loc'] !== '') : ?>
                        <div class="swinog-event-overview__loc"><?php echo esc_html($row['loc']); ?></div>
                    <?php endif; ?>
                </div>
                <div class="swinog-event-overview__tag">
                    <?php if ($row['tag'] !== '') : ?>
                        <span class="swinog-tag"><?php echo esc_html($row['tag']); ?></span>
                    <?php endif; ?>
                </div>
                <div class="swinog-event-overview__arrow" aria-hidden="true">→</div>
            </a>
        <?php endforeach; ?>
    </div>
    <?php
    return (string) ob_get_clean();
}
