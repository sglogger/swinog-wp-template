<?php
/**
 * SwiNOG theme bootstrap.
 *
 * Phase 1 · skeleton.
 *
 *  - theme supports (post thumbnails, responsive embeds, editor styles, etc.)
 *  - enqueue assets/css/tokens.css on the front end + in the block editor
 *  - register meeting + talk CPTs with show_in_rest for block bindings
 *  - register a "swinog" block pattern category for later pattern files
 *
 * @package SwiNOG
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/* ------------------------------------------------------------------
 * Theme supports + setup
 * ------------------------------------------------------------------ */

add_action('after_setup_theme', static function (): void {
    load_theme_textdomain('swinog', get_template_directory() . '/languages');

    add_theme_support('post-thumbnails');
    add_theme_support('responsive-embeds');
    add_theme_support('editor-styles');
    add_theme_support('wp-block-styles');
    add_theme_support('align-wide');
    add_theme_support('automatic-feed-links');
    add_theme_support('title-tag');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script']);
    add_theme_support('custom-logo', [
        'height'      => 64,
        'width'       => 64,
        'flex-height' => true,
        'flex-width'  => true,
    ]);

    register_nav_menus([
        'primary' => __('Header (primary)', 'swinog'),
    ]);

    add_editor_style('assets/css/tokens.css');
});

/* ------------------------------------------------------------------
 * Asset enqueue (front end + block editor)
 * ------------------------------------------------------------------ */

add_action('wp_enqueue_scripts', static function (): void {
    $theme = wp_get_theme();
    wp_enqueue_style(
        'swinog-tokens',
        get_theme_file_uri('/assets/css/tokens.css'),
        [],
        $theme->get('Version')
    );
});

add_action('enqueue_block_editor_assets', static function (): void {
    $theme = wp_get_theme();
    wp_enqueue_style(
        'swinog-tokens-editor',
        get_theme_file_uri('/assets/css/tokens.css'),
        [],
        $theme->get('Version')
    );
    // Default every core/html block in the editor to the Preview tab.
    wp_enqueue_script(
        'swinog-editor-html-preview',
        get_theme_file_uri('/assets/js/editor-html-preview.js'),
        ['wp-hooks', 'wp-compose', 'wp-element'],
        $theme->get('Version'),
        true
    );
});

/* ------------------------------------------------------------------
 * Privacy: drop the WP emoji loader. CH/EU privacy-friendly.
 * ------------------------------------------------------------------ */

add_action('init', static function (): void {
    remove_action('wp_head',         'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
});

/* ------------------------------------------------------------------
 * Custom post types: meeting, talk — only registered when the
 * wp-swinog-events plugin is NOT active. With the plugin, the data
 * model is `stgl_presentation` + `stgl_sponsor` + the
 * `stgl_presentation_cat` taxonomy, and event landing pages are
 * regular WP Pages. See inc/swinog-events-integration.php for the
 * styling + wrapper patterns that adopt the plugin's output.
 *
 * Standalone fallback below keeps the theme usable without the plugin.
 * ------------------------------------------------------------------ */

function swinog_events_plugin_active(): bool
{
    return defined('STGL_SWINOG_VERSION') || class_exists('\\Stgl\\SwinogEvents\\Plugin', false);
}

add_action('init', static function (): void {
    if (swinog_events_plugin_active()) {
        return;
    }

    register_post_type('meeting', [
        'label'         => __('Meetings', 'swinog'),
        'labels'        => [
            'singular_name' => __('Meeting', 'swinog'),
            'add_new_item'  => __('Add new meeting', 'swinog'),
            'edit_item'     => __('Edit meeting', 'swinog'),
            'search_items'  => __('Search meetings', 'swinog'),
            'not_found'     => __('No meetings yet.', 'swinog'),
        ],
        'public'        => true,
        'has_archive'   => true,
        'show_in_rest'  => true,
        'supports'      => ['title', 'editor', 'excerpt', 'thumbnail', 'custom-fields', 'revisions'],
        'rewrite'       => ['slug' => 'meetings', 'with_front' => false],
        'menu_icon'     => 'dashicons-calendar-alt',
        'menu_position' => 20,
    ]);

    register_post_type('talk', [
        'label'         => __('Talks', 'swinog'),
        'labels'        => [
            'singular_name' => __('Talk', 'swinog'),
            'add_new_item'  => __('Add new talk', 'swinog'),
            'edit_item'     => __('Edit talk', 'swinog'),
            'search_items'  => __('Search talks', 'swinog'),
            'not_found'     => __('No talks yet.', 'swinog'),
        ],
        'public'        => true,
        'has_archive'   => true,
        'show_in_rest'  => true,
        'supports'      => ['title', 'editor', 'excerpt', 'custom-fields', 'revisions'],
        'rewrite'       => ['slug' => 'talks', 'with_front' => false],
        'menu_icon'     => 'dashicons-megaphone',
        'menu_position' => 21,
    ]);
});

/* ------------------------------------------------------------------
 * Theme-owned meeting + talk meta · only when the plugin is absent.
 * ------------------------------------------------------------------ */

add_action('init', static function (): void {
    if (swinog_events_plugin_active()) {
        return;
    }
    $meta = [
        'meeting' => ['meeting_date', 'meeting_venue', 'meeting_city', 'meeting_fee', 'meeting_seats', 'meeting_cfp_deadline', 'meeting_register_url', 'meeting_ical_url'],
        'talk'    => ['talk_meeting', 'talk_speaker', 'talk_org', 'talk_minutes', 'talk_slides_url', 'talk_video_url'],
    ];
    foreach ($meta as $type => $keys) {
        foreach ($keys as $key) {
            register_post_meta($type, $key, [
                'type'          => 'string',
                'single'        => true,
                'show_in_rest'  => true,
                'auth_callback' => static fn (): bool => current_user_can('edit_posts'),
            ]);
        }
    }
});

/* ------------------------------------------------------------------
 * Block pattern category (patterns themselves land in Phase 2+)
 * ------------------------------------------------------------------ */

add_action('init', static function (): void {
    register_block_pattern_category('swinog', [
        'label'       => __('SwiNOG · Sections', 'swinog'),
        'description' => __('Section patterns for the SwiNOG block theme.', 'swinog'),
    ]);
});

/* ------------------------------------------------------------------
 * Customizer + server-rendered blocks (Bosa-style inc/ layout)
 * ------------------------------------------------------------------ */

require_once get_theme_file_path('/inc/customizer.php');
require_once get_theme_file_path('/inc/blocks.php');
require_once get_theme_file_path('/inc/page-options.php');
require_once get_theme_file_path('/inc/venue-map.php');
require_once get_theme_file_path('/inc/swinog-events-integration.php');
require_once get_theme_file_path('/inc/github-updater.php');

/* ------------------------------------------------------------------
 * Exclude the current post from the news-related query (queryId 62
 * in patterns/news-related.php). The Query block has no built-in
 * "exclude current" toggle, so we filter the query vars at render.
 * ------------------------------------------------------------------ */

add_filter('query_loop_block_query_vars', static function (array $query, $block): array {
    $attrs = is_object($block) && isset($block->context['queryId'])
        ? null
        : (isset($block->parsed_block['attrs']) ? $block->parsed_block['attrs'] : []);
    $query_id = $attrs['queryId'] ?? ($block->context['queryId'] ?? null);
    if ((int) $query_id !== 62 || !is_singular('post')) {
        return $query;
    }
    $exclude = isset($query['post__not_in']) && is_array($query['post__not_in']) ? $query['post__not_in'] : [];
    $exclude[] = get_the_ID();
    $query['post__not_in'] = array_values(array_unique(array_map('intval', $exclude)));
    return $query;
}, 10, 2);
