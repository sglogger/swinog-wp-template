<?php
/**
 * SwiNOG · Per-page options
 *
 * Adds a "SwiNOG · Page options" meta box to the Page edit screen with:
 *   - Hide the page title (H1)        → meta `_swinog_hide_page_title`
 *   - Hide breadcrumbs on this page   → meta `_swinog_hide_breadcrumbs`
 *
 * Filters strip the rendered output at the block-render level so no
 * orphan DOM remains.
 *
 * @package SwiNOG
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

const SWINOG_HIDE_TITLE_META       = '_swinog_hide_page_title';
const SWINOG_HIDE_BREADCRUMBS_META = '_swinog_hide_breadcrumbs';
const SWINOG_EVENT_DATE_META       = 'swinog_event_date';
const SWINOG_EVENT_LOCATION_META   = 'swinog_event_location';
const SWINOG_EVENT_ADDRESS_META    = 'swinog_event_address';
const SWINOG_EVENT_TAG_META        = 'swinog_event_tag';
const SWINOG_EVENT_PILL_META       = 'swinog_event_pill';
const SWINOG_EVENT_FEE_META        = 'swinog_event_fee';
const SWINOG_EVENT_TALKS_META      = 'swinog_event_talks';
const SWINOG_EVENT_FORMAT_META     = 'swinog_event_format';
const SWINOG_EVENT_RECORDING_META  = 'swinog_event_recording_url';

/* ------------------------------------------------------------------
 * Register meta so block-editor + REST clients can read/write it.
 * Hide-breadcrumbs is registered for pages, posts and the plugin's
 * CPTs so the option works everywhere a breadcrumb would render.
 * ------------------------------------------------------------------ */

add_action('init', static function (): void {
    register_post_meta('page', SWINOG_HIDE_TITLE_META, [
        'type'          => 'boolean',
        'single'        => true,
        'show_in_rest'  => true,
        'default'       => false,
        'auth_callback' => static fn (): bool => current_user_can('edit_pages'),
    ]);

    $crumb_post_types = ['page', 'post'];
    if (function_exists('swinog_events_plugin_active') && swinog_events_plugin_active()) {
        $crumb_post_types[] = 'stgl_presentation';
        $crumb_post_types[] = 'stgl_sponsor';
    }
    foreach ($crumb_post_types as $pt) {
        register_post_meta($pt, SWINOG_HIDE_BREADCRUMBS_META, [
            'type'          => 'boolean',
            'single'        => true,
            'show_in_rest'  => true,
            'default'       => false,
            'auth_callback' => static fn (): bool => current_user_can('edit_posts'),
        ]);
    }

    // Event-detail meta: used by the page-event template + by the
    // [swinog_list_all_events] overview to display each event's date
    // and location alongside the page title.
    foreach ([
        SWINOG_EVENT_DATE_META      => ['type' => 'string', 'sanitize' => 'sanitize_text_field'],
        SWINOG_EVENT_LOCATION_META  => ['type' => 'string', 'sanitize' => 'sanitize_text_field'],
        SWINOG_EVENT_ADDRESS_META   => ['type' => 'string', 'sanitize' => 'sanitize_textarea_field'],
        SWINOG_EVENT_TAG_META       => ['type' => 'string', 'sanitize' => 'sanitize_text_field'],
        SWINOG_EVENT_PILL_META      => ['type' => 'string', 'sanitize' => 'sanitize_text_field'],
        SWINOG_EVENT_FEE_META       => ['type' => 'string', 'sanitize' => 'sanitize_text_field'],
        SWINOG_EVENT_TALKS_META     => ['type' => 'string', 'sanitize' => 'sanitize_text_field'],
        SWINOG_EVENT_FORMAT_META    => ['type' => 'string', 'sanitize' => 'sanitize_text_field'],
        SWINOG_EVENT_RECORDING_META => ['type' => 'string', 'sanitize' => 'sanitize_text_field'],
    ] as $key => $cfg) {
        register_post_meta('page', $key, [
            'type'              => $cfg['type'],
            'single'            => true,
            'show_in_rest'      => true,
            'default'           => '',
            'auth_callback'     => static fn (): bool => current_user_can('edit_pages'),
            'sanitize_callback' => $cfg['sanitize'],
        ]);
    }
});

/* ------------------------------------------------------------------
 * Meta box · single panel with both checkboxes.
 * ------------------------------------------------------------------ */

add_action('add_meta_boxes', static function (): void {
    $post_types = ['page', 'post'];
    if (function_exists('swinog_events_plugin_active') && swinog_events_plugin_active()) {
        $post_types[] = 'stgl_presentation';
        $post_types[] = 'stgl_sponsor';
    }
    foreach ($post_types as $pt) {
        add_meta_box(
            'swinog_page_options',
            __('SwiNOG · Page options', 'swinog'),
            'swinog_render_page_options_box',
            $pt,
            'side',
            'default'
        );
    }

    // Event details meta box · Pages only. Shows where the event takes
    // place and when. The page-event template renders these in the hero;
    // the events overview renders them next to each child page.
    add_meta_box(
        'swinog_event_details',
        __('SwiNOG · Event details', 'swinog'),
        'swinog_render_event_details_box',
        'page',
        'side',
        'default'
    );
});

function swinog_render_page_options_box(WP_Post $post): void
{
    $hide_title  = (bool) get_post_meta($post->ID, SWINOG_HIDE_TITLE_META, true);
    $hide_crumbs = (bool) get_post_meta($post->ID, SWINOG_HIDE_BREADCRUMBS_META, true);
    wp_nonce_field('swinog_page_options', 'swinog_page_options_nonce');
    ?>
    <?php if ($post->post_type === 'page') : ?>
    <p>
        <label>
            <input type="checkbox" name="swinog_hide_page_title" value="1" <?php checked($hide_title); ?> />
            <?php esc_html_e('Hide the page title (H1) on this page', 'swinog'); ?>
        </label>
    </p>
    <?php endif; ?>
    <p>
        <label>
            <input type="checkbox" name="swinog_hide_breadcrumbs" value="1" <?php checked($hide_crumbs); ?> />
            <?php esc_html_e('Hide breadcrumbs on this page', 'swinog'); ?>
        </label>
    </p>
    <p style="color:#646970;font-size:12px;margin:6px 0 0;">
        <?php esc_html_e('Breadcrumbs are added automatically to every non-home page.', 'swinog'); ?>
    </p>
    <?php
}

/* ------------------------------------------------------------------
 * Save handler · one nonce, two booleans.
 * ------------------------------------------------------------------ */

add_action('save_post', static function (int $post_id): void {
    if (!isset($_POST['swinog_page_options_nonce']) || !wp_verify_nonce(sanitize_key($_POST['swinog_page_options_nonce']), 'swinog_page_options')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    update_post_meta($post_id, SWINOG_HIDE_TITLE_META,       !empty($_POST['swinog_hide_page_title'])  ? 1 : 0);
    update_post_meta($post_id, SWINOG_HIDE_BREADCRUMBS_META, !empty($_POST['swinog_hide_breadcrumbs']) ? 1 : 0);
});

/* ------------------------------------------------------------------
 * Event details · meta box render + save.
 * ------------------------------------------------------------------ */

function swinog_render_event_details_box(WP_Post $post): void
{
    $date      = (string) get_post_meta($post->ID, SWINOG_EVENT_DATE_META, true);
    $location  = (string) get_post_meta($post->ID, SWINOG_EVENT_LOCATION_META, true);
    $address   = (string) get_post_meta($post->ID, SWINOG_EVENT_ADDRESS_META, true);
    $tag       = (string) get_post_meta($post->ID, SWINOG_EVENT_TAG_META, true);
    $pill      = (string) get_post_meta($post->ID, SWINOG_EVENT_PILL_META, true);
    $fee       = (string) get_post_meta($post->ID, SWINOG_EVENT_FEE_META, true);
    $talks     = (string) get_post_meta($post->ID, SWINOG_EVENT_TALKS_META, true);
    $format    = (string) get_post_meta($post->ID, SWINOG_EVENT_FORMAT_META, true);
    $recording = (string) get_post_meta($post->ID, SWINOG_EVENT_RECORDING_META, true);
    wp_nonce_field('swinog_event_details', 'swinog_event_details_nonce');
    ?>
    <p>
        <label for="swinog_event_date" style="display:block;margin-bottom:4px;font-weight:600;">
            <?php esc_html_e('Date', 'swinog'); ?>
        </label>
        <input id="swinog_event_date" name="swinog_event_date" type="date" value="<?php echo esc_attr($date); ?>" style="width:100%;" />
    </p>
    <p>
        <label for="swinog_event_location" style="display:block;margin-bottom:4px;font-weight:600;">
            <?php esc_html_e('Location', 'swinog'); ?>
        </label>
        <input id="swinog_event_location" name="swinog_event_location" type="text" value="<?php echo esc_attr($location); ?>" placeholder="<?php esc_attr_e('Kursaal Berne', 'swinog'); ?>" style="width:100%;" />
        <span style="display:block;color:#646970;font-size:11px;margin-top:4px;">
            <?php esc_html_e('Short venue name shown in the hero meta line (e.g. "Kursaal Berne").', 'swinog'); ?>
        </span>
    </p>
    <p>
        <label for="swinog_event_address" style="display:block;margin-bottom:4px;font-weight:600;">
            <?php esc_html_e('Full address (for map)', 'swinog'); ?>
        </label>
        <textarea id="swinog_event_address" name="swinog_event_address" rows="3" placeholder="<?php esc_attr_e("Kornhausstrasse 3\n3013 Berne\nSwitzerland", 'swinog'); ?>" style="width:100%;"><?php echo esc_textarea($address); ?></textarea>
        <span style="display:block;color:#646970;font-size:11px;margin-top:4px;">
            <?php esc_html_e('Geocoded via OpenStreetMap Nominatim on save; a static map PNG is cached locally and rendered by the SwiNOG · Event venue block.', 'swinog'); ?>
        </span>
    </p>
    <p>
        <label for="swinog_event_tag" style="display:block;margin-bottom:4px;font-weight:600;">
            <?php esc_html_e('Event tag (plugin slug, optional)', 'swinog'); ?>
        </label>
        <input id="swinog_event_tag" name="swinog_event_tag" type="text" value="<?php echo esc_attr($tag); ?>" placeholder="swinog-41" style="width:100%;" />
        <span style="display:block;color:#646970;font-size:11px;margin-top:4px;">
            <?php esc_html_e('Matches a stgl_presentation_cat term — used by the agenda + sponsor shortcodes inside this page.', 'swinog'); ?>
        </span>
    </p>
    <hr style="margin:14px 0;border:0;border-top:1px solid #dcdcde;" />
    <p>
        <label for="swinog_event_pill" style="display:block;margin-bottom:4px;font-weight:600;">
            <?php esc_html_e('Status pill (optional)', 'swinog'); ?>
        </label>
        <input id="swinog_event_pill" name="swinog_event_pill" type="text" value="<?php echo esc_attr($pill); ?>" placeholder="<?php esc_attr_e('Recording & slides online', 'swinog'); ?>" style="width:100%;" />
    </p>
    <p>
        <label for="swinog_event_fee" style="display:block;margin-bottom:4px;font-weight:600;">
            <?php esc_html_e('Fee', 'swinog'); ?>
        </label>
        <input id="swinog_event_fee" name="swinog_event_fee" type="text" value="<?php echo esc_attr($fee); ?>" placeholder="<?php esc_attr_e('CHF 0 — community funded', 'swinog'); ?>" style="width:100%;" />
    </p>
    <p>
        <label for="swinog_event_talks" style="display:block;margin-bottom:4px;font-weight:600;">
            <?php esc_html_e('Talks summary', 'swinog'); ?>
        </label>
        <input id="swinog_event_talks" name="swinog_event_talks" type="text" value="<?php echo esc_attr($talks); ?>" placeholder="<?php esc_attr_e('14 · single track', 'swinog'); ?>" style="width:100%;" />
    </p>
    <p>
        <label for="swinog_event_format" style="display:block;margin-bottom:4px;font-weight:600;">
            <?php esc_html_e('Format', 'swinog'); ?>
        </label>
        <input id="swinog_event_format" name="swinog_event_format" type="text" value="<?php echo esc_attr($format); ?>" placeholder="<?php esc_attr_e('One day · long-table dinner', 'swinog'); ?>" style="width:100%;" />
    </p>
    <p>
        <label for="swinog_event_recording_url" style="display:block;margin-bottom:4px;font-weight:600;">
            <?php esc_html_e('Recording CTA URL', 'swinog'); ?>
        </label>
        <input id="swinog_event_recording_url" name="swinog_event_recording_url" type="url" value="<?php echo esc_attr($recording); ?>" placeholder="#program" style="width:100%;" />
        <span style="display:block;color:#646970;font-size:11px;margin-top:4px;">
            <?php esc_html_e('Target of the red "View the recordings" button at the bottom of Quick Facts. Defaults to #program.', 'swinog'); ?>
        </span>
    </p>
    <p style="color:#646970;font-size:12px;margin:6px 0 0;">
        <?php esc_html_e('These fields appear in the page hero (when using "Page · event detail") and in the parent page\'s events overview.', 'swinog'); ?>
    </p>
    <?php
}

add_action('save_post_page', static function (int $post_id): void {
    if (!isset($_POST['swinog_event_details_nonce']) || !wp_verify_nonce(sanitize_key($_POST['swinog_event_details_nonce']), 'swinog_event_details')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_page', $post_id)) {
        return;
    }
    foreach ([
        SWINOG_EVENT_DATE_META      => 'swinog_event_date',
        SWINOG_EVENT_LOCATION_META  => 'swinog_event_location',
        SWINOG_EVENT_ADDRESS_META   => 'swinog_event_address',
        SWINOG_EVENT_TAG_META       => 'swinog_event_tag',
        SWINOG_EVENT_PILL_META      => 'swinog_event_pill',
        SWINOG_EVENT_FEE_META       => 'swinog_event_fee',
        SWINOG_EVENT_TALKS_META     => 'swinog_event_talks',
        SWINOG_EVENT_FORMAT_META    => 'swinog_event_format',
        SWINOG_EVENT_RECORDING_META => 'swinog_event_recording_url',
    ] as $key => $field) {
        if ($key === SWINOG_EVENT_RECORDING_META) {
            $value = isset($_POST[$field]) ? esc_url_raw((string) wp_unslash($_POST[$field])) : '';
        } elseif ($key === SWINOG_EVENT_ADDRESS_META) {
            $value = isset($_POST[$field]) ? sanitize_textarea_field((string) wp_unslash($_POST[$field])) : '';
        } else {
            $value = isset($_POST[$field]) ? sanitize_text_field((string) wp_unslash($_POST[$field])) : '';
        }
        if ($value === '') {
            delete_post_meta($post_id, $key);
        } else {
            update_post_meta($post_id, $key, $value);
        }
    }
});

/* ------------------------------------------------------------------
 * Small public helper · formatted date for an event page.
 * Used by the event-hero block and the events overview shortcode.
 * ------------------------------------------------------------------ */

function swinog_format_event_date(string $iso): string
{
    if ($iso === '') {
        return '';
    }
    $ts = strtotime($iso);
    if ($ts === false) {
        return $iso;
    }
    return wp_date(get_option('date_format') ?: 'j F Y', $ts);
}

/* ------------------------------------------------------------------
 * Render-time filter: strip core/post-title on Pages where the
 * `_swinog_hide_page_title` meta is set.
 * ------------------------------------------------------------------ */

add_filter('render_block_core/post-title', static function (string $content): string {
    if (!is_page()) {
        return $content;
    }
    $post_id = get_queried_object_id();
    if (!$post_id) {
        return $content;
    }
    return get_post_meta($post_id, SWINOG_HIDE_TITLE_META, true) ? '' : $content;
});
