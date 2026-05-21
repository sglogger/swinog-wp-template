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

// The "Hide the page title (H1)" toggle only suppresses the standalone
// core/post-title (see inc/page-options.php). The event hero title is the
// page's real visible H1, so it always renders — that's the whole point of
// the toggle on event pages: drop the duplicate title above the hero.

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
