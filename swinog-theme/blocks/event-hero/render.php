<?php
/**
 * SwiNOG · Event hero · server render
 *
 * Renders the full event hero on the "Page · event detail" template.
 * Static copy (CTA labels/URLs) lives on the block as attributes;
 * the per-event facts (date, location, pill, fee, talks, format,
 * recording URL) come from the queried page's meta — set them in the
 * "SwiNOG · Event details" sidebar meta box.
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

$post_id = get_queried_object_id();
if (!$post_id) {
    return '';
}

$title    = (string) get_the_title($post_id);
$excerpt  = (string) get_post_field('post_excerpt', $post_id);

$date      = (string) get_post_meta($post_id, 'swinog_event_date', true);
$location  = (string) get_post_meta($post_id, 'swinog_event_location', true);
$tag       = (string) get_post_meta($post_id, 'swinog_event_tag', true);
$pill      = (string) get_post_meta($post_id, 'swinog_event_pill', true);
$fee       = (string) get_post_meta($post_id, 'swinog_event_fee', true);
$talks     = (string) get_post_meta($post_id, 'swinog_event_talks', true);
$format    = (string) get_post_meta($post_id, 'swinog_event_format', true);
$recording = (string) get_post_meta($post_id, 'swinog_event_recording_url', true);

$nice_date = function_exists('swinog_format_event_date') ? swinog_format_event_date($date) : $date;

$primary_label   = trim((string) ($attributes['primaryLabel']   ?? ''));
$primary_url     = trim((string) ($attributes['primaryUrl']     ?? ''));
$secondary_label = trim((string) ($attributes['secondaryLabel'] ?? ''));
$secondary_url   = trim((string) ($attributes['secondaryUrl']   ?? ''));
$ics_label       = trim((string) ($attributes['icsLabel']       ?? ''));
$ics_url         = trim((string) ($attributes['icsUrl']         ?? ''));

$recording_url = $recording !== '' ? $recording : '#program';

$wrapper = function_exists('get_block_wrapper_attributes')
    ? get_block_wrapper_attributes(['class' => 'swinog-event-hero-wrap'])
    : 'class="swinog-event-hero-wrap"';

ob_start();
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="swinog-event-hero">
		<div class="swinog-event-hero__copy">

			<?php if ($pill !== '') : ?>
				<div class="swinog-pill swinog-pill--status">
					<span class="swinog-pill__dot" aria-hidden="true"></span>
					<span><?php echo esc_html($pill); ?></span>
				</div>
			<?php endif; ?>

			<h1 class="swinog-event-hero__title"><?php echo esc_html($title); ?></h1>

			<?php if ($nice_date !== '' || $location !== '') : ?>
				<p class="swinog-event-hero__meta">
					<?php if ($nice_date !== '') : ?>
						<span class="swinog-event-hero__meta-date"><?php echo esc_html($nice_date); ?></span>
					<?php endif; ?>
					<?php if ($nice_date !== '' && $location !== '') : ?>
						<span class="swinog-event-hero__meta-sep" aria-hidden="true"> · </span>
					<?php endif; ?>
					<?php if ($location !== '') : ?>
						<strong class="swinog-event-hero__meta-loc"><?php echo esc_html($location); ?></strong>
					<?php endif; ?>
				</p>
			<?php endif; ?>

			<?php if ($excerpt !== '') : ?>
				<p class="swinog-event-hero__lead"><?php echo esc_html($excerpt); ?></p>
			<?php endif; ?>

			<?php if (
				($primary_label !== '' && $primary_url !== '') ||
				($secondary_label !== '' && $secondary_url !== '') ||
				($ics_label !== '' && $ics_url !== '')
			) : ?>
				<div class="wp-block-buttons swinog-cta-row">
					<?php if ($primary_label !== '' && $primary_url !== '') : ?>
						<div class="wp-block-button swinog-btn swinog-btn--primary">
							<a class="wp-block-button__link wp-element-button" href="<?php echo esc_url($primary_url); ?>"><?php echo esc_html($primary_label); ?></a>
						</div>
					<?php endif; ?>
					<?php if ($secondary_label !== '' && $secondary_url !== '') : ?>
						<div class="wp-block-button swinog-btn swinog-btn--secondary">
							<a class="wp-block-button__link wp-element-button" href="<?php echo esc_url($secondary_url); ?>"><?php echo esc_html($secondary_label); ?></a>
						</div>
					<?php endif; ?>
					<?php if ($ics_label !== '' && $ics_url !== '') : ?>
						<div class="wp-block-button swinog-btn swinog-btn--ghost">
							<a class="wp-block-button__link wp-element-button" href="<?php echo esc_url($ics_url); ?>"><?php echo esc_html($ics_label); ?></a>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>

		</div>

		<aside class="swinog-event-hero__facts swinog-soft-shadow">
			<div class="swinog-kicker"><?php esc_html_e('Quick facts', 'swinog'); ?></div>
			<dl class="swinog-facts-dl">
				<?php if ($nice_date !== '') : ?>
					<dt><?php esc_html_e('Date', 'swinog'); ?></dt><dd><?php echo esc_html($nice_date); ?></dd>
				<?php endif; ?>
				<?php if ($location !== '') : ?>
					<dt><?php esc_html_e('Venue', 'swinog'); ?></dt><dd><?php echo esc_html($location); ?></dd>
				<?php endif; ?>
				<?php if ($fee !== '') : ?>
					<dt><?php esc_html_e('Fee', 'swinog'); ?></dt><dd><?php echo esc_html($fee); ?></dd>
				<?php endif; ?>
				<?php if ($talks !== '') : ?>
					<dt><?php esc_html_e('Talks', 'swinog'); ?></dt><dd><?php echo esc_html($talks); ?></dd>
				<?php endif; ?>
				<?php if ($format !== '') : ?>
					<dt><?php esc_html_e('Format', 'swinog'); ?></dt><dd><?php echo esc_html($format); ?></dd>
				<?php endif; ?>
				<dt><?php esc_html_e('Code of conduct', 'swinog'); ?></dt>
				<dd><a href="<?php echo esc_url(home_url('/code-of-conduct/')); ?>"><?php esc_html_e('swinog.ch/coc →', 'swinog'); ?></a></dd>
			</dl>
			<a class="swinog-facts-dl__cta" href="<?php echo esc_url($recording_url); ?>"><?php esc_html_e('View the recordings', 'swinog'); ?></a>
		</aside>
	</div>
</section>
<?php

echo ob_get_clean(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
