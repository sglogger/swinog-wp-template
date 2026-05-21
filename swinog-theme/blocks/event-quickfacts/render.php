<?php
/**
 * SwiNOG · Event Quick Facts · server render
 *
 * Renders the white "Quick facts" card with rows pulled from the
 * page's SwiNOG · Event details meta:
 *   - Date     (long form: "Saturday, May 23, 2026")
 *   - Venue    (swinog_event_location)
 *   - Fee
 *   - Talks
 *   - Format
 *   - Recording CTA (red button at the bottom — only when meta is set)
 *
 * Designed to live in the right column of the event-hero pattern
 * (where the SVG used to be), alongside editable core blocks on the
 * left. No attributes — drop the block in and it just works.
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

$date      = (string) get_post_meta($post_id, 'swinog_event_date', true);
$location  = (string) get_post_meta($post_id, 'swinog_event_location', true);
$fee       = (string) get_post_meta($post_id, 'swinog_event_fee', true);
$talks     = (string) get_post_meta($post_id, 'swinog_event_talks', true);
$attendees = (string) get_post_meta($post_id, 'swinog_event_attendees', true);
$format    = (string) get_post_meta($post_id, 'swinog_event_format', true);
$recording = (string) get_post_meta($post_id, 'swinog_event_recording_url', true);

$nice_date_long = function_exists('swinog_format_event_date')
    ? swinog_format_event_date($date, true)
    : $date;

$wrapper = function_exists('get_block_wrapper_attributes')
    ? get_block_wrapper_attributes(['class' => 'swinog-event-hero__facts swinog-soft-shadow'])
    : 'class="swinog-event-hero__facts swinog-soft-shadow"';

ob_start();
?>
<aside <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="swinog-kicker"><?php esc_html_e('Quick facts', 'swinog'); ?></div>
	<dl class="swinog-facts-dl">
		<?php if ($nice_date_long !== '') : ?>
			<dt><?php esc_html_e('Date', 'swinog'); ?></dt><dd><?php echo esc_html($nice_date_long); ?></dd>
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
		<?php if ($attendees !== '') : ?>
			<dt><?php esc_html_e('Attendees', 'swinog'); ?></dt><dd><?php echo esc_html($attendees); ?></dd>
		<?php endif; ?>
		<?php if ($format !== '') : ?>
			<dt><?php esc_html_e('Format', 'swinog'); ?></dt><dd><?php echo esc_html($format); ?></dd>
		<?php endif; ?>
	</dl>
	<?php if ($recording !== '') : ?>
		<a class="swinog-facts-dl__cta" href="<?php echo esc_url($recording); ?>"><?php esc_html_e('View the recordings', 'swinog'); ?></a>
	<?php endif; ?>
</aside>
<?php
echo ob_get_clean(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
