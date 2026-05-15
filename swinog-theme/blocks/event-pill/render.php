<?php
/**
 * SwiNOG · Event status pill · server render
 *
 * Reads `swinog_event_pill` from the queried page's meta and emits
 * the SwiNOG status pill. Renders nothing on the front-end when the
 * meta is blank; in the editor (REST preview) we show a dashed
 * placeholder so authors can still see the block is wired up.
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

$pill        = trim((string) get_post_meta($post_id, 'swinog_event_pill', true));
$is_editor   = (defined('REST_REQUEST') && REST_REQUEST) || is_admin();

if ($pill === '' && !$is_editor) {
    return;
}

if ($pill === '') {
    $wrapper = function_exists('get_block_wrapper_attributes')
        ? get_block_wrapper_attributes(['class' => 'swinog-pill swinog-pill--placeholder'])
        : 'class="swinog-pill swinog-pill--placeholder"';
    printf(
        '<div %s><span>%s</span></div>',
        $wrapper, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        esc_html__('Status pill — fill in "Status pill" meta to render.', 'swinog')
    );
    return;
}

$wrapper = function_exists('get_block_wrapper_attributes')
    ? get_block_wrapper_attributes(['class' => 'swinog-pill swinog-pill--status'])
    : 'class="swinog-pill swinog-pill--status"';

printf(
    '<div %s><span class="swinog-pill__dot" aria-hidden="true"></span><span>%s</span></div>',
    $wrapper, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    esc_html($pill)
);
