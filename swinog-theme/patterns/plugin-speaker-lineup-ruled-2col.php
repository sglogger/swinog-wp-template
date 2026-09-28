<?php
/**
 * Title: SwiNOG · Plugin · Speaker line-up (ruled list, 2 columns) · #plugin-speaker-lineup-ruled-2col
 * Slug: swinog/plugin-speaker-lineup-ruled-2col
 * Categories: swinog-events
 * Description: Two-column version of the ruled speaker list – numbered entries with hairline rules in two newspaper columns above 900px (01–06 left, 07–12 right), talk title under the speaker, pulled live from the CFP tool. Numbering follows the programme order. Edit the [swinog_list_speaker_lineup] shortcode's "event" to the CFP event slug (e.g. swinog-42) – not the event category. Anchor: #plugin-speaker-lineup-ruled-2col
 * Inserter: true
 */
?>
<!-- wp:group {"anchor":"plugin-speaker-lineup-ruled-2col","tagName":"section","className":"swinog-lineup-ruled-wrap swinog-plugin-wrap","align":"full","layout":{"type":"constrained","wideSize":"1280px"}} -->
<section id="plugin-speaker-lineup-ruled-2col" class="wp-block-group alignfull swinog-lineup-ruled-wrap swinog-plugin-wrap">

	<!-- wp:group {"className":"swinog-lineup-ruled swinog-lineup-ruled--2col","align":"wide","layout":{"type":"default"}} -->
	<div class="wp-block-group alignwide swinog-lineup-ruled swinog-lineup-ruled--2col">
		<!-- wp:paragraph {"className":"swinog-lineup-ruled__eyebrow"} -->
		<p class="swinog-lineup-ruled__eyebrow">Speaker line-up</p>
		<!-- /wp:paragraph -->

		<!-- wp:heading {"level":2,"className":"swinog-lineup-ruled__title"} -->
		<h2 class="wp-block-heading swinog-lineup-ruled__title">Who's on stage.</h2>
		<!-- /wp:heading -->

<!-- wp:shortcode -->
[swinog_list_speaker_lineup event="swinog-42"]
<!-- /wp:shortcode -->

		<!-- wp:paragraph {"className":"swinog-lineup-ruled__note"} -->
		<p class="swinog-lineup-ruled__note">More talks will be added as they are confirmed.</p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->
