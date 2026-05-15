<?php
/**
 * SwiNOG · Breadcrumbs · server render
 *
 * Builds a context-aware breadcrumb trail and emits it as a <nav>.
 * Suppresses itself on the homepage, on 404 pages, and when the
 * `_swinog_hide_breadcrumbs` post meta is true on the queried object.
 *
 * Uses a flat <div><a>·<span> structure instead of <ol>/<li> so the
 * default browser/list-style chrome can't bleed through.
 *
 * @package SwiNOG
 */

if (!defined('ABSPATH')) {
    exit;
}

if (is_front_page() || is_404()) {
    return '';
}

$post_id = get_queried_object_id();
if ($post_id && get_post_meta($post_id, '_swinog_hide_breadcrumbs', true)) {
    return '';
}

if (!function_exists('swinog_build_breadcrumbs')) {
    /**
     * Return the breadcrumb trail for the current request as a list of
     * [{label, url}] entries. The last entry is the current page (no URL).
     *
     * @return array<int, array{label:string,url:string}>
     */
    function swinog_build_breadcrumbs(): array
    {
        $crumbs = [
            ['label' => __('SwiNOG', 'swinog'), 'url' => home_url('/')],
        ];

        if (is_page()) {
            $page = get_queried_object();
            if ($page instanceof WP_Post) {
                $ancestors = array_reverse(get_post_ancestors($page));
                foreach ($ancestors as $aid) {
                    $crumbs[] = [
                        'label' => get_the_title($aid),
                        'url'   => (string) get_permalink($aid),
                    ];
                }
                $crumbs[] = ['label' => get_the_title($page), 'url' => ''];
            }
        } elseif (is_singular('post')) {
            $crumbs[] = ['label' => __('News', 'swinog'), 'url' => home_url('/news/')];
            $crumbs[] = ['label' => get_the_title(), 'url' => ''];
        } elseif (is_singular(['stgl_presentation', 'stgl_sponsor'])) {
            $crumbs[] = ['label' => __('Meetings', 'swinog'), 'url' => home_url('/meetings/')];
            $terms = get_the_terms(get_queried_object_id(), 'stgl_presentation_cat');
            if ($terms && !is_wp_error($terms)) {
                $term = $terms[0];
                $link = get_term_link($term);
                if (!is_wp_error($link)) {
                    $crumbs[] = ['label' => $term->name, 'url' => (string) $link];
                }
            }
            $crumbs[] = ['label' => get_the_title(), 'url' => ''];
        } elseif (is_tax('stgl_presentation_cat')) {
            $crumbs[] = ['label' => __('Meetings', 'swinog'), 'url' => home_url('/meetings/')];
            $term = get_queried_object();
            if ($term instanceof WP_Term) {
                $crumbs[] = ['label' => $term->name, 'url' => ''];
            }
        } elseif (is_category() || is_tag()) {
            $crumbs[] = ['label' => __('News', 'swinog'), 'url' => home_url('/news/')];
            $term = get_queried_object();
            if ($term instanceof WP_Term) {
                $crumbs[] = ['label' => $term->name, 'url' => ''];
            }
        } elseif (is_search()) {
            $crumbs[] = [
                'label' => sprintf(
                    /* translators: %s: the search query */
                    __('Search results for "%s"', 'swinog'),
                    get_search_query()
                ),
                'url' => '',
            ];
        } elseif (is_archive()) {
            $title = function_exists('get_the_archive_title') ? (string) get_the_archive_title() : '';
            $crumbs[] = ['label' => wp_strip_all_tags($title) ?: __('Archive', 'swinog'), 'url' => ''];
        } elseif (is_home()) {
            $crumbs[] = ['label' => __('News', 'swinog'), 'url' => ''];
        }

        /**
         * Filter the breadcrumb trail.
         *
         * @param array<int, array{label:string,url:string}> $crumbs
         */
        return (array) apply_filters('swinog_breadcrumbs', $crumbs);
    }
}

$crumbs = swinog_build_breadcrumbs();
if (count($crumbs) < 2) {
    return '';
}

$wrapper_attrs = function_exists('get_block_wrapper_attributes')
    ? get_block_wrapper_attributes(['class' => 'swinog-breadcrumb swinog-breadcrumb--block', 'aria-label' => __('Breadcrumb', 'swinog')])
    : 'class="swinog-breadcrumb swinog-breadcrumb--block" aria-label="' . esc_attr__('Breadcrumb', 'swinog') . '"';

ob_start();
?>
<nav <?php echo $wrapper_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="swinog-breadcrumb__list">
		<?php
		$last = count($crumbs) - 1;
		foreach ($crumbs as $i => $c) :
			$is_last = ($i === $last);
			if (!$is_last && !empty($c['url'])) :
				?>
				<a class="swinog-breadcrumb__link" href="<?php echo esc_url($c['url']); ?>"><?php echo esc_html($c['label']); ?></a>
				<span class="swinog-breadcrumb__sep" aria-hidden="true">›</span>
				<?php
			else :
				?>
				<span class="swinog-breadcrumb__current" aria-current="page"><?php echo esc_html($c['label']); ?></span>
				<?php
			endif;
		endforeach;
		?>
	</div>
</nav>
<?php

echo ob_get_clean(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
