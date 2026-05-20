<?php
/**
 * SwiNOG · Server-rendered blocks
 *
 *   swinog/header-ctas   — renders the two top-right buttons from Customizer settings
 *   swinog/footer-widgets — renders N footer widget columns; N is per-instance or
 *                           falls back to the Customizer swinog_footer_columns setting
 *   swinog/footer-copyright — renders the swinog-footer-copyright widget area
 *
 * @package SwiNOG
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/* ------------------------------------------------------------------
 * Header CTAs
 * ------------------------------------------------------------------ */

add_action('init', static function (): void {
    register_block_type('swinog/header-ctas', [
        'api_version'     => 3,
        'title'           => __('SwiNOG · Header CTAs', 'swinog'),
        'category'        => 'theme',
        'icon'            => 'megaphone',
        'description'     => __('Renders the two top-right header buttons from Customizer settings.', 'swinog'),
        'supports'        => ['html' => false, 'inserter' => true],
        'render_callback' => 'swinog_render_header_ctas',
    ]);

    register_block_type('swinog/footer-widgets', [
        'api_version'     => 3,
        'title'           => __('SwiNOG · Footer widget grid', 'swinog'),
        'category'        => 'theme',
        'icon'            => 'columns',
        'description'     => __('A configurable grid of footer widget columns. Drop widgets into each column under Appearance → Widgets.', 'swinog'),
        'supports'        => [
            'html'    => false,
            'align'   => ['wide', 'full'],
            'spacing' => ['margin' => true, 'padding' => true],
        ],
        'attributes'      => [
            'columns' => ['type' => 'number'],   // null → use Customizer
            'gap'     => ['type' => 'string', 'default' => '32px'],
        ],
        'render_callback' => 'swinog_render_footer_widgets',
    ]);

    register_block_type('swinog/footer-copyright', [
        'api_version'     => 3,
        'title'           => __('SwiNOG · Footer copyright row', 'swinog'),
        'category'        => 'theme',
        'icon'            => 'editor-textcolor',
        'description'     => __('Renders the swinog-footer-copyright widget area. Edit widgets under Appearance → Widgets.', 'swinog'),
        'supports'        => ['html' => false, 'inserter' => true],
        'render_callback' => 'swinog_render_footer_copyright',
    ]);
});

function swinog_render_header_ctas(): string
{
    $buttons = [];
    foreach ([1, 2] as $n) {
        $label = trim((string) swinog_mod("swinog_cta_{$n}_label"));
        $url   = trim((string) swinog_mod("swinog_cta_{$n}_url"));
        if ($label === '' || $url === '') {
            continue;
        }
        $buttons[] = [
            'label'  => $label,
            'url'    => $url,
            'style'  => (string) swinog_mod("swinog_cta_{$n}_style") ?: 'primary',
            'target' => (bool)   swinog_mod("swinog_cta_{$n}_target"),
        ];
    }
    if (!$buttons) {
        return '';
    }

    $wrapper = function_exists('get_block_wrapper_attributes')
        ? get_block_wrapper_attributes(['class' => 'swinog-header-ctas swinog-softbar__actions'])
        : 'class="swinog-header-ctas swinog-softbar__actions"';

    $html = '<div ' . $wrapper . '>';
    foreach ($buttons as $btn) {
        $target = $btn['target'] ? ' target="_blank" rel="noopener noreferrer"' : '';
        $html  .= sprintf(
            '<a class="swinog-header-cta swinog-header-cta--%s" href="%s"%s>%s</a>',
            esc_attr($btn['style']),
            esc_url($btn['url']),
            $target,
            esc_html($btn['label'])
        );
    }
    $html .= '</div>';
    return $html;
}

function swinog_render_footer_widgets(array $attrs): string
{
    $columns = isset($attrs['columns']) && $attrs['columns'] !== null
        ? (int) $attrs['columns']
        : (int) swinog_mod('swinog_footer_columns');
    $columns = max(1, min(SWINOG_FOOTER_COLUMNS_MAX, $columns));
    $gap     = isset($attrs['gap']) ? (string) $attrs['gap'] : '32px';

    $wrapper = function_exists('get_block_wrapper_attributes')
        ? get_block_wrapper_attributes([
            'class' => 'swinog-footer-grid',
            'style' => sprintf('--swinog-footer-columns:%d;--swinog-footer-gap:%s;', $columns, esc_attr($gap)),
        ])
        : sprintf('class="swinog-footer-grid" style="--swinog-footer-columns:%d;--swinog-footer-gap:%s;"', $columns, esc_attr($gap));

    $cols = '';
    for ($i = 1; $i <= $columns; $i++) {
        $sidebar_id = 'swinog-footer-col-' . $i;
        $cols .= sprintf('<div class="swinog-footer-grid__col swinog-footer-grid__col--%d">', $i);

        if (is_active_sidebar($sidebar_id)) {
            ob_start();
            dynamic_sidebar($sidebar_id);
            $cols .= ob_get_clean();
        } else {
            $cols .= sprintf(
                '<div class="swinog-footer-grid__empty"><div class="swinog-widget__h">%s</div><p>%s <a href="%s">%s</a></p></div>',
                /* translators: %d column index. */
                esc_html(sprintf(__('Footer column %d', 'swinog'), $i)),
                esc_html__('Empty. Add widgets to this column under', 'swinog'),
                esc_url(admin_url('widgets.php')),
                esc_html__('Appearance → Widgets', 'swinog')
            );
        }
        $cols .= '</div>';
    }

    return sprintf('<div %s>%s</div>', $wrapper, $cols);
}

function swinog_render_footer_copyright(): string
{
    $wrapper = function_exists('get_block_wrapper_attributes')
        ? get_block_wrapper_attributes(['class' => 'swinog-footer-copyright swinog-softfoot__bottom'])
        : 'class="swinog-footer-copyright swinog-softfoot__bottom"';

    if (is_active_sidebar('swinog-footer-copyright')) {
        ob_start();
        dynamic_sidebar('swinog-footer-copyright');
        $body = ob_get_clean();
    } else {
        $body = sprintf(
            '<p class="swinog-footer-copyright__empty"><a href="%s">%s</a></p>',
            esc_url(admin_url('widgets.php')),
            esc_html__('Add copyright widget under Appearance → Widgets', 'swinog')
        );
    }
    return '<div ' . $wrapper . '>' . $body . '</div>';
}

/* ------------------------------------------------------------------
 * Widget areas — 6 footer columns + a copyright row.
 *
 * All six column sidebars are always registered so editors can drop
 * widgets into any of them in advance, even before bumping the
 * column count in the Customizer.
 * ------------------------------------------------------------------ */

add_action('widgets_init', static function (): void {
    for ($i = 1; $i <= SWINOG_FOOTER_COLUMNS_MAX; $i++) {
        register_sidebar([
            /* translators: %d column number. */
            'name'          => sprintf(__('Footer · column %d', 'swinog'), $i),
            'id'            => 'swinog-footer-col-' . $i,
            'description'   => __('Renders inside the footer widget grid. Visible only when the footer block exposes this many columns (Customize → SwiNOG · Footer).', 'swinog'),
            'before_widget' => '<section id="%1$s" class="swinog-widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<div class="swinog-widget__h">',
            'after_title'   => '</div>',
        ]);
    }

    register_sidebar([
        'name'          => __('Footer · copyright row', 'swinog'),
        'id'            => 'swinog-footer-copyright',
        'description'   => __('The slim row at the very bottom of every page (copyright, hosted-by). Use any block widget — Heading, Paragraph, Text, Custom HTML.', 'swinog'),
        'before_widget' => '<div id="%1$s" class="swinog-footer-copyright__widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<span class="swinog-visually-hidden">',
        'after_title'   => '</span>',
    ]);
});

/* ------------------------------------------------------------------
 * Render the header's core/navigation block from a classic menu
 * (Appearance → Menus) instead of a block wp_navigation post.
 *
 * The header part marks its nav with the `swinog-softbar__nav` class.
 * We swap that block's output for wp_nav_menu(), choosing the menu the
 * Customizer points at (swinog_primary_nav = menu term id) or, when
 * unset, whichever menu is assigned to the `primary` location. The
 * markup is remapped onto the block-navigation classes the header CSS
 * already targets, so styling is unchanged.
 * ------------------------------------------------------------------ */

function swinog_render_header_menu(): string
{
    $menu_id = (string) swinog_mod('swinog_primary_nav');

    $args = [
        'container'            => 'nav',
        'container_class'      => 'wp-block-navigation swinog-softbar__nav',
        'container_aria_label' => __('Primary', 'swinog'),
        'menu_class'           => 'wp-block-navigation__container',
        'depth'                => 0,
        'fallback_cb'          => false,
        'echo'                 => false,
        'swinog_header'        => true,
    ];
    if ($menu_id !== '') {
        $args['menu'] = (int) $menu_id;
    } else {
        $args['theme_location'] = 'primary';
    }

    $html = wp_nav_menu($args);

    return is_string($html) ? $html : '';
}

add_filter('render_block', static function (string $content, array $block): string {
    if (($block['blockName'] ?? '') !== 'core/navigation') {
        return $content;
    }
    if (!str_contains((string) ($block['attrs']['className'] ?? ''), 'swinog-softbar__nav')) {
        return $content;
    }
    return swinog_render_header_menu();
}, 10, 2);

/* Map classic-menu markup onto the block-navigation classes the CSS uses. */
add_filter('nav_menu_css_class', static function (array $classes, $item, $args): array {
    if (!empty($args->swinog_header)) {
        $classes[] = 'wp-block-navigation-item';
    }
    return $classes;
}, 10, 3);

add_filter('nav_menu_link_attributes', static function (array $atts, $item, $args): array {
    if (!empty($args->swinog_header)) {
        $atts['class'] = trim((string) ($atts['class'] ?? '') . ' wp-block-navigation-item__content');
    }
    return $atts;
}, 10, 3);

/* ------------------------------------------------------------------
 * Seed the default primary navigation menu + footer widgets on first
 * theme activation. Idempotent — re-runs are no-ops once seeded.
 * ------------------------------------------------------------------ */

function swinog_seed_primary_nav(): void
{
    if (!post_type_exists('wp_navigation')) {
        return;
    }
    if (get_page_by_path('swinog-primary', OBJECT, 'wp_navigation') instanceof WP_Post) {
        return;
    }
    $links = [
        ['Charter',      '/charter/'],
        ['Meetings',     '/meetings/'],
        ['Mailing list', '/mailinglist/'],
        ['CFP',          '/cfp/'],
        ['News',         '/news/'],
        ['About',        '/about/'],
        ['Contact',      '/contact/'],
    ];
    $content = '';
    foreach ($links as [$label, $url]) {
        $content .= sprintf(
            '<!-- wp:navigation-link {"label":"%s","url":"%s","kind":"custom","isTopLevelLink":true} /-->' . "\n",
            esc_attr($label),
            esc_attr($url)
        );
    }
    wp_insert_post([
        'post_title'   => 'SwiNOG · Primary',
        'post_name'    => 'swinog-primary',
        'post_status'  => 'publish',
        'post_type'    => 'wp_navigation',
        'post_content' => $content,
    ]);
}
add_action('init', 'swinog_seed_primary_nav', 20);

function swinog_seed_footer_widgets(): void
{
    if (get_option('swinog_footer_widgets_seeded') === '1') {
        return;
    }

    $sidebars_widgets = wp_get_sidebars_widgets();

    $defaults = [
        'swinog-footer-col-1' => '<!-- wp:heading {"level":4,"className":"swinog-widget__h"} --><h4 class="wp-block-heading swinog-widget__h">SwiNOG</h4><!-- /wp:heading --><!-- wp:paragraph --><p>Run by volunteers since 2000. Non-political, not a lobby, no membership fees.</p><!-- /wp:paragraph --><!-- wp:paragraph --><p><strong>SwiNOG Organisation</strong><br>8000 Zürich · Switzerland<br><a href="mailto:swinog-core@swinog.ch">swinog-core@swinog.ch</a></p><!-- /wp:paragraph -->',
        'swinog-footer-col-2' => '<!-- wp:heading {"level":4,"className":"swinog-widget__h"} --><h4 class="wp-block-heading swinog-widget__h">Community</h4><!-- /wp:heading --><!-- wp:list --><ul class="wp-block-list"><li><a href="/charter/">Charter</a></li><li><a href="/mailinglist/">Mailing list</a></li><li><a href="https://lists.swinog.ch/hyperkitty/list/swinog@lists.swinog.ch/">List archive</a></li><li><a href="/code-of-conduct/">Code of conduct</a></li></ul><!-- /wp:list -->',
        'swinog-footer-col-3' => '<!-- wp:heading {"level":4,"className":"swinog-widget__h"} --><h4 class="wp-block-heading swinog-widget__h">Meetings</h4><!-- /wp:heading --><!-- wp:list --><ul class="wp-block-list"><li><a href="/meetings/">All meetings</a></li><li><a href="/meetings/swinog41/">SwiNOG #41</a></li><li><a href="/cfp/">Call for talks</a></li><li><a href="/sponsor/">Sponsor a meeting</a></li></ul><!-- /wp:list -->',
        'swinog-footer-col-4' => '<!-- wp:heading {"level":4,"className":"swinog-widget__h"} --><h4 class="wp-block-heading swinog-widget__h">Contact</h4><!-- /wp:heading --><!-- wp:list --><ul class="wp-block-list"><li><a href="mailto:swinog-core@swinog.ch">swinog-core@swinog.ch</a></li><li><a href="/supporters/">Supporters</a></li><li><a href="/feed.xml">RSS feed</a></li><li><a href="/contact/">Press</a></li></ul><!-- /wp:list -->',
        'swinog-footer-copyright' => '<!-- wp:paragraph --><p>© 2000–2026 SwiNOG · Switzerland · the first European NOG · Hosted by Netrics · list by init7</p><!-- /wp:paragraph -->',
    ];

    $existing = get_option('widget_block', []);
    if (!is_array($existing)) {
        $existing = [];
    }
    $next = $existing ? (max(array_filter(array_keys($existing), 'is_int') ?: [0]) + 1) : 2;

    foreach ($defaults as $sidebar_id => $content) {
        if (!empty($sidebars_widgets[$sidebar_id]) && is_array($sidebars_widgets[$sidebar_id])) {
            continue;
        }
        $existing[$next] = ['content' => $content];
        $sidebars_widgets[$sidebar_id] = ['block-' . $next];
        $next++;
    }

    update_option('widget_block', $existing);
    wp_set_sidebars_widgets($sidebars_widgets);
    update_option('swinog_footer_widgets_seeded', '1');
}
add_action('init', 'swinog_seed_footer_widgets', 30);
