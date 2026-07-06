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

    // Server-rendered "Event title" — H1 pulled from the page title.
    $title_dir = get_theme_file_path('/blocks/event-title');
    if (is_dir($title_dir) && file_exists($title_dir . '/block.json')) {
        register_block_type($title_dir);
    }

    // Server-rendered "Event meta line" — "<date> · <location>" line
    // under the H1, pulled from the event-details meta.
    $meta_dir = get_theme_file_path('/blocks/event-meta-line');
    if (is_dir($meta_dir) && file_exists($meta_dir . '/block.json')) {
        register_block_type($meta_dir);
    }

    // Server-rendered "Presentation byline" — presenter name/company +
    // video/slides buttons from the plugin meta, for single presentations.
    $byline_dir = get_theme_file_path('/blocks/presentation-byline');
    if (is_dir($byline_dir) && file_exists($byline_dir . '/block.json')) {
        register_block_type($byline_dir);
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
    if (!in_array($name, ['swinog/agenda', 'swinog/venue', 'swinog/venue-map', 'swinog/event-quickfacts', 'swinog/event-pill', 'swinog/event-title', 'swinog/event-meta-line'], true)) {
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
 * [swinog_list_all_events] / [stgl_childpages] — events timeline.
 *
 * Lists child pages of the current page (or the page given by
 * `parent="<id>"`) as a reverse-chronological timeline matching the
 * `.swinog-timeline*` styles in assets/css/tokens.css.
 *
 * Per-page meta keys read:
 *   swinog_event_date     — ISO date; formatted "M Y" (e.g. "May 2026").
 *   swinog_event_end_date — optional ISO date; when set, the row shows
 *                           a range (e.g. "20–21 Oct 2026").
 *   swinog_event_location — bold venue title (e.g. "Welle 7, Bern").
 *   swinog_event_tag      — slug of a stgl_presentation_cat term; the
 *                           number of presentations attached to that
 *                           term is rendered as the talks count.
 *   swinog_event_attendees— optional, e.g. "218" or "~200".
 *   swinog_event_topics   — optional, comma-separated chip labels.
 * ================================================================== */

function swinog_render_events_overview($atts): string
{
    $atts = shortcode_atts([
        'parent' => 0,
        'order'  => 'desc',
        'limit'  => 0,
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

    $rows = array_map(static function (WP_Post $p): array {
        $date = (string) get_post_meta($p->ID, 'swinog_event_date', true);
        return [
            'post' => $p,
            'date' => $date,
            'ts'   => $date !== '' ? (strtotime($date) ?: 0) : 0,
        ];
    }, $children);

    $dir = strtolower((string) $atts['order']) === 'asc' ? SORT_ASC : SORT_DESC;
    usort($rows, static fn ($a, $b) => $dir === SORT_ASC
        ? ($a['ts'] <=> $b['ts'])
        : ($b['ts'] <=> $a['ts'])
    );

    $limit = (int) $atts['limit'];
    if ($limit > 0) {
        $rows = array_slice($rows, 0, $limit);
    }

    $html = '';
    foreach ($rows as $row) {
        $html .= swinog_render_events_overview_row($row['post'], $row['date']);
    }
    if ($html === '') {
        return '';
    }

    return '<div class="swinog-timeline swinog-timeline--titled alignwide">' . $html . '</div>';
}

function swinog_count_event_posts(string $post_type, string $term_slug): int
{
    if ($post_type === '' || $term_slug === '') {
        return 0;
    }
    $q = new WP_Query([
        'post_type'              => $post_type,
        'post_status'            => 'publish',
        'posts_per_page'         => -1,
        'fields'                 => 'ids',
        'no_found_rows'          => true,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
        'tax_query'              => [[
            'taxonomy' => 'stgl_presentation_cat',
            'field'    => 'slug',
            'terms'    => $term_slug,
        ]],
    ]);
    return (int) $q->post_count;
}

function swinog_render_events_overview_row(WP_Post $page, string $date_meta): string
{
    $id    = $page->ID;
    $url   = (string) get_permalink($id);
    $title = get_the_title($id);

    $location  = trim((string) get_post_meta($id, 'swinog_event_location',  true));
    $event_tag = trim((string) get_post_meta($id, 'swinog_event_tag',       true));
    $attendees = trim((string) get_post_meta($id, 'swinog_event_attendees', true));
    $topics    = trim((string) get_post_meta($id, 'swinog_event_topics',    true));

    // Lead each row with the full page title (rather than a parsed "#NN").
    $number_h = esc_html($title);

    // Short date — "24 Jun 2025", or a range like "20–21 Oct 2026" when
    // an end date is set / fall back to post_date if no meta set.
    if ($date_meta !== '') {
        $end_meta = trim((string) get_post_meta($id, 'swinog_event_end_date', true));
        if (function_exists('swinog_format_event_date_range')) {
            $date_display = swinog_format_event_date_range($date_meta, $end_meta, 'compact');
        } else {
            $ts = strtotime($date_meta);
            $date_display = $ts !== false ? wp_date('j M Y', $ts) : $date_meta;
        }
    } else {
        $date_display = mysql2date('j M Y', $page->post_date);
    }

    // Talks + sponsors counts from the linked stgl_presentation_cat term.
    // The term's own `count` mixes both CPTs (the taxonomy is shared with
    // stgl_sponsor), so query each post type explicitly.
    $talks    = 0;
    $sponsors = 0;
    if ($event_tag !== '' && taxonomy_exists('stgl_presentation_cat')) {
        $slug      = sanitize_title($event_tag);
        $talks     = swinog_count_event_posts('stgl_presentation', $slug);
        $sponsors  = swinog_count_event_posts('stgl_sponsor',      $slug);
    }

    // Topic chips.
    $tags_html = '';
    if ($topics !== '') {
        foreach (array_filter(array_map('trim', explode(',', $topics))) as $t) {
            $tags_html .= '<span class="swinog-tag">' . esc_html($t) . '</span>';
        }
    }

    // Right-column counts — one inline line: "7 talks · 100 attendees · 1 sponsors".
    $stats = [];
    if ($talks > 0) {
        $stats[] = (int) $talks . ' talks';
    }
    if ($attendees !== '') {
        $stats[] = esc_html($attendees) . ' attendees';
    }
    if ($sponsors > 0) {
        $stats[] = (int) $sponsors . ' sponsors';
    }
    $count_html = $stats !== []
        ? '<div class="swinog-ink-3">' . implode(' · ', $stats) . '</div>'
        : '';

    $where      = $location !== '' ? esc_html($location) : esc_html($title);
    $tags_block = $tags_html !== ''
        ? '<div class="swinog-timeline__tags">' . $tags_html . '</div>'
        : '';

    return sprintf(
        '<a class="swinog-timeline__row" href="%1$s">'
        . '<div class="swinog-timeline__no">'
            . '<div class="swinog-timeline__no-h">%2$s</div>'
            . '<div class="swinog-timeline__no-d">%3$s</div>'
        . '</div>'
        . '<div class="swinog-timeline__body">'
            . '<div class="swinog-timeline__where">%4$s</div>'
            . '%5$s'
        . '</div>'
        . '<div class="swinog-timeline__count">%6$s</div>'
        . '<div class="swinog-timeline__arrow" aria-hidden="true">&rarr;</div>'
        . '</a>',
        esc_url($url),
        $number_h,
        esc_html($date_display),
        $where,
        $tags_block,
        $count_html
    );
}

/* ------------------------------------------------------------------
 * Make front-end search match presenters: the speaker name + company
 * live in stgl_presenter_name / _company meta, not the post content,
 * so a plain title/content search misses them. OR a presenter-meta
 * match against the existing search clause (subquery → no row dupes).
 * ------------------------------------------------------------------ */

add_filter('posts_search', static function (string $search, WP_Query $q): string {
    global $wpdb;
    if ($search === '' || is_admin() || !$q->is_main_query() || !$q->is_search()) {
        return $search;
    }
    $term = (string) $q->get('s');
    if ($term === '') {
        return $search;
    }
    $like = '%' . $wpdb->esc_like($term) . '%';
    $sub  = $wpdb->prepare(
        "({$wpdb->posts}.ID IN (SELECT post_id FROM {$wpdb->postmeta}"
        . " WHERE meta_key IN ('stgl_presenter_name', 'stgl_presenter_company')"
        . " AND meta_value LIKE %s))",
        $like
    );
    $orig = preg_replace('/^\s*AND\s*/', '', $search);
    return " AND ( {$orig} OR {$sub} )";
}, 10, 2);

/* ------------------------------------------------------------------
 * Live "next meeting" days countdown for the SoftHero card.
 *
 * The card is a raw wp:html block, so a value baked in at pattern
 * registration freezes when the pattern is inserted into a page. We
 * inject the count at render time instead — works whether the hero is
 * referenced via wp:pattern or inserted/expanded into content.
 *
 * @return array{0:string,1:string} [days, label]
 * ------------------------------------------------------------------ */
function swinog_next_meeting_days(): array
{
    $today = (int) current_time('timestamp');
    $next  = null;
    $last  = null;

    $pages = get_posts([
        'post_type'              => 'page',
        'post_status'            => 'publish',
        'posts_per_page'         => -1,
        'fields'                 => 'ids',
        'no_found_rows'          => true,
        'update_post_term_cache' => false,
        'meta_query'             => [
            ['key' => 'swinog_event_date', 'compare' => 'EXISTS'],
            ['key' => 'swinog_event_tag',  'compare' => 'EXISTS'],
        ],
    ]);
    foreach ($pages as $pid) {
        $d = trim((string) get_post_meta($pid, 'swinog_event_date', true));
        $t = trim((string) get_post_meta($pid, 'swinog_event_tag', true));
        if ($d === '' || $t === '') {
            continue;
        }
        $ts = strtotime($d);
        if ($ts === false) {
            continue;
        }
        if ($ts >= $today) {
            if ($next === null || $ts < $next['ts']) {
                $next = ['ts' => $ts, 'tag' => $t];
            }
        } elseif ($last === null || $ts > $last['ts']) {
            $last = ['ts' => $ts, 'tag' => $t];
        }
    }

    if ($next !== null) {
        $days = max(0, (int) ceil(($next['ts'] - $today) / DAY_IN_SECONDS));
        $num  = preg_replace('/[^0-9]+/', '', $next['tag']);
        $label = $num !== '' ? sprintf('Until #%s', $num) : 'Until next';
    } elseif ($last !== null) {
        $days = max(0, (int) floor(($today - $last['ts']) / DAY_IN_SECONDS));
        $num  = preg_replace('/[^0-9]+/', '', $last['tag']);
        $label = $num !== '' ? sprintf('Days since #%s', $num) : 'Days since';
    } else {
        $days  = '–';
        $label = 'Days';
    }

    return [(string) $days, $label];
}

add_filter('render_block', static function (string $content, array $block): string {
    if (($block['blockName'] ?? '') !== 'core/html') {
        return $content;
    }
    if (strpos($content, 'data-swinog-stat="next-meeting"') === false) {
        return $content;
    }
    [$days, $label] = swinog_next_meeting_days();
    $content = preg_replace_callback(
        '/(<[^>]*data-swinog-nm="label"[^>]*>)(.*?)(<\/[^>]+>)/s',
        static fn (array $m): string => $m[1] . esc_html($label) . $m[3],
        $content,
        1
    );
    $content = preg_replace_callback(
        '/(<[^>]*data-swinog-nm="days"[^>]*>)(.*?)(<\/[^>]+>)/s',
        static fn (array $m): string => $m[1] . esc_html($days) . $m[3],
        $content,
        1
    );
    return $content;
}, 10, 2);
