<?php
/**
 * SwiNOG · Event date · location line · server render
 *
 * Renders the short "<date> · <location>" line under the hero H1.
 * Both values come from the page's meta. Each is optional — the
 * separator only renders when both sides are populated.
 *
 * @package SwiNOG
 */

if (!defined('ABSPATH')) {
    exit;
}

$post_id = get_queried_object_id();
if (!$post_id) {
    return;
}

$date     = (string) get_post_meta($post_id, 'swinog_event_date', true);
$location = (string) get_post_meta($post_id, 'swinog_event_location', true);

$nice_date = function_exists('swinog_format_event_date') ? swinog_format_event_date($date) : $date;

if ($nice_date === '' && $location === '') {
    if ((defined('REST_REQUEST') && REST_REQUEST) || is_admin()) {
        $wrapper = function_exists('get_block_wrapper_attributes')
            ? get_block_wrapper_attributes(['class' => 'swinog-event-hero__meta swinog-event-hero__meta--placeholder'])
            : 'class="swinog-event-hero__meta swinog-event-hero__meta--placeholder"';
        printf(
            '<p %s>%s</p>',
            $wrapper, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            esc_html__('Date · Location — fill in the SwiNOG · Event details box.', 'swinog')
        );
    }
    return;
}

$wrapper = function_exists('get_block_wrapper_attributes')
    ? get_block_wrapper_attributes(['class' => 'swinog-event-hero__meta'])
    : 'class="swinog-event-hero__meta"';

ob_start();
?>
<p <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
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
<?php
echo ob_get_clean(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
