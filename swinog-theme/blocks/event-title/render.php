<?php
/**
 * SwiNOG · Event title · server render
 *
 * Renders the queried page's title as the hero H1.
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

// Honour the per-page "Hide the page title (H1) on this page" toggle
// (meta key _swinog_hide_page_title — see inc/page-options.php). The
// existing render_block_core/post-title filter only catches the core
// post-title block, so this server-rendered hero title needs to opt
// in explicitly to stay in sync.
if (get_post_meta($post_id, '_swinog_hide_page_title', true)) {
    return;
}

$title = (string) get_the_title($post_id);
if ($title === '') {
    return;
}

$wrapper = function_exists('get_block_wrapper_attributes')
    ? get_block_wrapper_attributes(['class' => 'swinog-event-hero__title'])
    : 'class="swinog-event-hero__title"';

printf(
    '<h1 %s>%s</h1>',
    $wrapper, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    esc_html($title)
);
