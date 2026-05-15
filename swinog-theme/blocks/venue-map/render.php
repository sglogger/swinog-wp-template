<?php
/**
 * SwiNOG · Venue map block · server render
 *
 * Renders ONLY the cached static-map image (or an editor-only
 * placeholder if no map has been generated yet). Use this as a
 * drop-in replacement for the hand-drawn SVG inside venue layouts.
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

$venue   = trim((string) get_post_meta($post_id, 'swinog_event_location', true));
$address = trim((string) get_post_meta($post_id, 'swinog_event_address', true));
$map_url = (string) get_post_meta($post_id, 'swinog_event_map_url', true);

$wrapper = function_exists('get_block_wrapper_attributes')
    ? get_block_wrapper_attributes(['class' => 'swinog-venue__map'])
    : 'class="swinog-venue__map"';

ob_start();
?>
<div <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php if ($map_url !== '') : ?>
		<img
			src="<?php echo esc_url($map_url); ?>"
			alt="<?php echo esc_attr(sprintf(
				/* translators: %s = venue name or address */
				__('Map showing the location of %s', 'swinog'),
				$venue !== '' ? $venue : $address
			)); ?>"
			loading="lazy"
			decoding="async"
		/>
	<?php elseif (is_user_logged_in() && current_user_can('edit_pages')) : ?>
		<div class="swinog-venue__map-placeholder">
			<p><strong><?php esc_html_e('Map not yet generated.', 'swinog'); ?></strong></p>
			<p>
				<?php if ($address === '') : ?>
					<?php esc_html_e('Add a Full address in the SwiNOG · Event details meta box, then save the page.', 'swinog'); ?>
				<?php else : ?>
					<?php esc_html_e('Save this page again to trigger geocoding + map fetch. (Visible to editors only.)', 'swinog'); ?>
				<?php endif; ?>
			</p>
		</div>
	<?php endif; ?>
</div>
<?php
echo ob_get_clean(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
