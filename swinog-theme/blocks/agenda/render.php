<?php
/**
 * SwiNOG · Recent talks block · server render
 *
 * Queries stgl_presentation posts filtered by 1..N stgl_presentation_cat
 * terms (the "events" attribute, comma-separated slugs). Talk IDs are
 * auto-derived from the first event term + the post's menu_order
 * ("T-41-08"). If the plugin isn't active or no posts match, a tonal
 * "no talks" hint replaces the grid.
 *
 * @var array  $attributes
 * @var string $content
 * @var WP_Block $block
 *
 * @package SwiNOG
 */

if (!defined('ABSPATH')) {
    exit;
}

$events_raw   = trim((string) ($attributes['events'] ?? ''));
$count        = max(1, min(18, (int) ($attributes['count'] ?? 6)));
$columns      = max(1, min(3, (int) ($attributes['columns'] ?? 2)));
$kicker_attr  = trim((string) ($attributes['kicker'] ?? ''));
$title        = trim((string) ($attributes['title'] ?? 'Recent talks worth your time.'));
$arch_label   = trim((string) ($attributes['archiveLabel'] ?? 'Full archive →'));
$arch_url     = trim((string) ($attributes['archiveUrl'] ?? '/event-category/'));

$plugin_on    = function_exists('swinog_events_plugin_active') && swinog_events_plugin_active();

$event_slugs  = $events_raw === ''
    ? []
    : array_values(array_filter(array_map(
        static fn ($s): string => sanitize_title((string) $s),
        preg_split('/[,\s]+/', $events_raw) ?: []
    )));

$query = null;
if ($plugin_on) {
    $query_args = [
        'post_type'              => 'stgl_presentation',
        'posts_per_page'         => $count,
        'orderby'                => 'rand',
        'no_found_rows'          => true,
        'update_post_term_cache' => true,
        // Only talks that actually have a video URL set.
        'meta_query'             => [
            [
                'key'     => 'stgl_presenter_videourl',
                'value'   => '',
                'compare' => '!=',
            ],
        ],
    ];
    if ($event_slugs) {
        $query_args['tax_query'] = [
            [
                'taxonomy' => 'stgl_presentation_cat',
                'field'    => 'slug',
                'terms'    => $event_slugs,
                'operator' => 'IN',
            ],
        ];
    }
    $query = new WP_Query($query_args);
}

/** Auto-derive a kicker from the selected event terms when the user didn't set one. */
$kicker = $kicker_attr;
if ($kicker === '' && $event_slugs) {
    $names = [];
    foreach ($event_slugs as $slug) {
        $term = get_term_by('slug', $slug, 'stgl_presentation_cat');
        if ($term instanceof WP_Term) {
            $names[] = $term->name;
        }
    }
    if ($names) {
        $kicker = sprintf(
            _n('From %s', 'From %s', count($names), 'swinog'),
            implode(' · ', $names)
        );
    }
}

$wrapper_attrs = function_exists('get_block_wrapper_attributes')
    ? get_block_wrapper_attributes(['class' => 'swinog-soft-agenda-wrap swinog-agenda-block'])
    : 'class="swinog-soft-agenda-wrap swinog-agenda-block"';

ob_start();
?>
<section <?php echo $wrapper_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="swinog-soft-agenda">

		<div class="swinog-soft-agenda__head">
			<div>
				<?php if ($kicker !== '') : ?>
					<p class="swinog-kicker swinog-kicker--accent"><?php echo esc_html($kicker); ?></p>
				<?php endif; ?>
				<?php if ($title !== '') : ?>
					<h2 class="swinog-display-sm"><?php echo esc_html($title); ?></h2>
				<?php endif; ?>
			</div>
			<?php if ($arch_label !== '' && $arch_url !== '') : ?>
				<a class="swinog-link-pill" href="<?php echo esc_url($arch_url); ?>"><?php echo esc_html($arch_label); ?></a>
			<?php endif; ?>
		</div>

		<?php if (!$plugin_on) : ?>
			<div class="swinog-agenda-block__empty">
				<p><strong><?php esc_html_e('SwiNOG · Recent talks', 'swinog'); ?></strong></p>
				<p><?php esc_html_e('Activate the wp-swinog-events plugin to populate this section from real presentation entries. Until then, this block stays empty.', 'swinog'); ?></p>
			</div>
		<?php elseif (!$query instanceof WP_Query || !$query->have_posts()) : ?>
			<div class="swinog-agenda-block__empty">
				<p>
					<?php
					if ($event_slugs) {
						echo esc_html(sprintf(
							/* translators: %s: comma-separated event slug list */
							__('No published presentations found for events %s. Add some under Presentations → Add new, or change the event slugs in the block inspector.', 'swinog'),
							implode(', ', $event_slugs)
						));
					} else {
						esc_html_e('No published presentations yet. Add some under Presentations → Add new.', 'swinog');
					}
					?>
				</p>
			</div>
		<?php else : ?>
			<div class="swinog-soft-agenda__grid" style="grid-template-columns: repeat(<?php echo (int) $columns; ?>, minmax(0, 1fr));">
				<?php
				while ($query->have_posts()) {
					$query->the_post();
					$id        = (int) get_the_ID();
					$presenter = (string) get_post_meta($id, 'stgl_presenter_name',    true);
					$company   = (string) get_post_meta($id, 'stgl_presenter_company', true);
					$time      = (string) get_post_meta($id, 'stgl_presenter_time',    true);
					$video_url = (string) get_post_meta($id, 'stgl_presenter_videourl', true);

					$terms = get_the_terms($id, 'stgl_presentation_cat');
					$event_slug = ($terms && !is_wp_error($terms)) ? $terms[0]->slug : '';
					$event_num  = $event_slug ? preg_replace('/[^0-9]+/', '', $event_slug) : '';
					$order_n    = (int) get_post_field('menu_order', $id);
					$talk_id    = ($event_num && $order_n)
						? sprintf('T-%s-%02d', $event_num, $order_n)
						: ($time !== '' ? $time : (string) get_the_ID());

					$company_label = $company !== '' ? $company : ($event_slug !== '' ? $terms[0]->name : '');
					?>
					<a class="swinog-talk-card" href="<?php echo esc_url($video_url); ?>" target="_blank" rel="noopener">
						<div class="swinog-talk-card__row">
							<span class="swinog-mono"><?php echo esc_html($talk_id); ?></span>
							<span class="swinog-mono"><?php echo esc_html($company_label); ?></span>
						</div>
						<div class="swinog-talk-card__title"><?php the_title(); ?></div>
						<div class="swinog-talk-card__foot">
							<span><?php echo esc_html($presenter); ?><?php if ($company !== '') : ?> · <span class="swinog-ink-3"><?php echo esc_html($company); ?></span><?php endif; ?></span>
							<span class="swinog-talk-card__arrow" aria-hidden="true">→</span>
						</div>
					</a>
					<?php
				}
				wp_reset_postdata();
				?>
			</div>
		<?php endif; ?>
	</div>
</section>
<?php

echo ob_get_clean(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
