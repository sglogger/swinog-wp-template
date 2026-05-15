<?php
/**
 * SwiNOG · Customizer
 *
 * Settings managed here:
 *   - Header → Menu             : dropdown of every wp_navigation post
 *   - Header → Buttons          : label + URL + style for two top-right CTAs
 *   - Footer → Columns          : number of footer widget columns (1–6)
 *
 * Block themes hide the Customize link by default, but it reappears as soon
 * as a theme registers Customizer settings. The link is at Appearance →
 * Customize, or directly /wp-admin/customize.php.
 *
 * @package SwiNOG
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

const SWINOG_FOOTER_COLUMNS_MAX = 6;

/* ------------------------------------------------------------------
 * Defaults — single source of truth, reused by render filters.
 * ------------------------------------------------------------------ */

function swinog_defaults(): array
{
    return [
        'swinog_primary_nav'      => '',
        'swinog_cta_1_label'      => 'Sign in',
        'swinog_cta_1_url'        => 'https://lists.swinog.ch/hyperkitty/list/swinog@lists.swinog.ch/',
        'swinog_cta_1_style'      => 'ghost',
        'swinog_cta_1_target'     => false,
        'swinog_cta_2_label'      => 'Register',
        'swinog_cta_2_url'        => 'https://lists.swinog.ch/postorius/lists/swinog.lists.swinog.ch/',
        'swinog_cta_2_style'      => 'primary',
        'swinog_cta_2_target'     => false,
        'swinog_footer_columns'   => 4,
    ];
}

function swinog_mod(string $key)
{
    $defaults = swinog_defaults();
    return get_theme_mod($key, $defaults[$key] ?? '');
}

/* ------------------------------------------------------------------
 * Customizer registration
 * ------------------------------------------------------------------ */

add_action('customize_register', static function (WP_Customize_Manager $wp): void {
    $defaults = swinog_defaults();

    /* ====== Panel: SwiNOG · Header ============================== */
    $wp->add_panel('swinog_header', [
        'title'    => __('SwiNOG · Header', 'swinog'),
        'priority' => 30,
    ]);

    /* -- Menu selector ------------------------------------------ */
    $wp->add_section('swinog_header_nav', [
        'title'       => __('Primary navigation', 'swinog'),
        'description' => __('Pick which menu the header uses. Menus are managed under Site Editor → Navigation.', 'swinog'),
        'panel'       => 'swinog_header',
        'priority'    => 10,
    ]);

    $wp->add_setting('swinog_primary_nav', [
        'default'           => $defaults['swinog_primary_nav'],
        'sanitize_callback' => 'sanitize_title',
        'transport'         => 'refresh',
    ]);

    $nav_choices = ['' => __('— Theme default (SwiNOG · Primary) —', 'swinog')];
    foreach (get_posts([
        'post_type'      => 'wp_navigation',
        'posts_per_page' => -1,
        'post_status'    => ['publish', 'draft'],
        'orderby'        => 'title',
        'order'          => 'ASC',
    ]) as $nav) {
        $nav_choices[$nav->post_name] = $nav->post_title;
    }

    $wp->add_control('swinog_primary_nav', [
        'label'   => __('Menu in the header', 'swinog'),
        'section' => 'swinog_header_nav',
        'type'    => 'select',
        'choices' => $nav_choices,
    ]);

    /* -- Two header buttons ------------------------------------- */
    $wp->add_section('swinog_header_ctas', [
        'title'       => __('Header buttons', 'swinog'),
        'description' => __('The two buttons in the top-right of every page. Clear a label or URL to hide that button.', 'swinog'),
        'panel'       => 'swinog_header',
        'priority'    => 20,
    ]);

    $style_choices = [
        'primary'   => __('Primary (filled, dark)', 'swinog'),
        'secondary' => __('Secondary (white, bordered)', 'swinog'),
        'outline'   => __('Outline (accent)', 'swinog'),
        'accent'    => __('Accent (filled, red)', 'swinog'),
        'ghost'     => __('Ghost (text only)', 'swinog'),
    ];

    foreach ([1, 2] as $n) {
        $wp->add_setting("swinog_cta_{$n}_label", [
            'default'           => $defaults["swinog_cta_{$n}_label"],
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ]);
        $wp->add_setting("swinog_cta_{$n}_url", [
            'default'           => $defaults["swinog_cta_{$n}_url"],
            'sanitize_callback' => 'esc_url_raw',
            'transport'         => 'refresh',
        ]);
        $wp->add_setting("swinog_cta_{$n}_style", [
            'default'           => $defaults["swinog_cta_{$n}_style"],
            'sanitize_callback' => static fn ($v) => array_key_exists($v, $style_choices) ? $v : 'primary',
            'transport'         => 'refresh',
        ]);
        $wp->add_setting("swinog_cta_{$n}_target", [
            'default'           => $defaults["swinog_cta_{$n}_target"],
            'sanitize_callback' => 'rest_sanitize_boolean',
            'transport'         => 'refresh',
        ]);

        $wp->add_control("swinog_cta_{$n}_label", [
            /* translators: %d slot index. */
            'label'   => sprintf(__('Button %d · text', 'swinog'), $n),
            'section' => 'swinog_header_ctas',
            'type'    => 'text',
        ]);
        $wp->add_control("swinog_cta_{$n}_url", [
            'label'   => sprintf(__('Button %d · URL', 'swinog'), $n),
            'section' => 'swinog_header_ctas',
            'type'    => 'url',
        ]);
        $wp->add_control("swinog_cta_{$n}_style", [
            'label'   => sprintf(__('Button %d · style', 'swinog'), $n),
            'section' => 'swinog_header_ctas',
            'type'    => 'select',
            'choices' => $style_choices,
        ]);
        $wp->add_control("swinog_cta_{$n}_target", [
            'label'   => sprintf(__('Button %d · open in new tab', 'swinog'), $n),
            'section' => 'swinog_header_ctas',
            'type'    => 'checkbox',
        ]);
    }

    /* ====== Panel: SwiNOG · Footer ============================== */
    $wp->add_panel('swinog_footer', [
        'title'    => __('SwiNOG · Footer', 'swinog'),
        'priority' => 32,
    ]);

    $wp->add_section('swinog_footer_main', [
        'title'       => __('Footer widgets', 'swinog'),
        'description' => __('Footer is built from widget areas — manage individual widgets under Appearance → Widgets. Set the column count here.', 'swinog'),
        'panel'       => 'swinog_footer',
        'priority'    => 10,
    ]);

    $wp->add_setting('swinog_footer_columns', [
        'default'           => $defaults['swinog_footer_columns'],
        'sanitize_callback' => static fn ($v) => max(1, min(SWINOG_FOOTER_COLUMNS_MAX, (int) $v)),
        'transport'         => 'refresh',
    ]);
    $wp->add_control('swinog_footer_columns', [
        'label'       => __('Number of footer widget columns', 'swinog'),
        'description' => __('1 through 6. Each column gets its own widget area.', 'swinog'),
        'section'     => 'swinog_footer_main',
        'type'        => 'number',
        'input_attrs' => ['min' => 1, 'max' => SWINOG_FOOTER_COLUMNS_MAX, 'step' => 1],
    ]);
});
