<?php
/**
 * SwiNOG · Presentation byline · server render
 *
 * On a single stgl_presentation, the WordPress author is just whoever
 * uploaded the entry — not the speaker. So instead of an avatar + author
 * we render the presenter from the plugin meta (stgl_presenter_name /
 * _company) plus "watch video" / "view slides" buttons when those exist.
 *
 * @package SwiNOG
 */

if (!defined('ABSPATH')) {
    exit;
}

$post_id = get_the_ID() ?: get_queried_object_id();
if (!$post_id) {
    return '';
}

$name    = trim((string) get_post_meta($post_id, 'stgl_presenter_name', true));
$company = trim((string) get_post_meta($post_id, 'stgl_presenter_company', true));

$video_url = trim((string) get_post_meta($post_id, 'stgl_presenter_videourl', true));

// Slides: prefer the new attachment id, fall back to the legacy
// wp_custom_attachment array — same resolution the plugin uses.
$slides_url = '';
$att_id = (int) get_post_meta($post_id, '_stgl_presentation_attachment_id', true);
if ($att_id) {
    $slides_url = (string) wp_get_attachment_url($att_id);
}
if ($slides_url === '') {
    $legacy = get_post_meta($post_id, 'wp_custom_attachment', true);
    if (is_array($legacy) && !empty($legacy['url'])) {
        $slides_url = (string) $legacy['url'];
    }
}

$has_person  = ($name !== '' || $company !== '');
$has_buttons = ($video_url !== '' || $slides_url !== '');
if (!$has_person && !$has_buttons) {
    return '';
}

$wrapper = function_exists('get_block_wrapper_attributes')
    ? get_block_wrapper_attributes(['class' => 'swinog-presentation-byline'])
    : 'class="swinog-presentation-byline"';

ob_start();
?>
<div <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php if ($has_person) : ?>
		<p class="swinog-presentation-byline__by">
			<?php if ($name !== '') : ?>
				<span class="swinog-presentation-byline__name"><?php echo esc_html($name); ?></span>
			<?php endif; ?>
			<?php if ($name !== '' && $company !== '') : ?>
				<span class="swinog-presentation-byline__sep" aria-hidden="true"> · </span>
			<?php endif; ?>
			<?php if ($company !== '') : ?>
				<span class="swinog-presentation-byline__org"><?php echo esc_html($company); ?></span>
			<?php endif; ?>
		</p>
	<?php endif; ?>

	<?php if ($has_buttons) : ?>
		<div class="wp-block-buttons swinog-cta-row">
			<?php if ($video_url !== '') : ?>
				<div class="wp-block-button swinog-btn swinog-btn--accent">
					<a class="wp-block-button__link wp-element-button" href="<?php echo esc_url($video_url); ?>" target="_blank" rel="noopener"><?php esc_html_e('Watch the video →', 'swinog'); ?></a>
				</div>
			<?php endif; ?>
			<?php if ($slides_url !== '') : ?>
				<div class="wp-block-button swinog-btn swinog-btn--accent">
					<a class="wp-block-button__link wp-element-button" href="<?php echo esc_url($slides_url); ?>" target="_blank" rel="noopener"><?php esc_html_e('View slides →', 'swinog'); ?></a>
				</div>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</div>
<?php
echo ob_get_clean(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
