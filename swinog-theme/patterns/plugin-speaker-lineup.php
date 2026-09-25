<?php
/**
 * Title: SwiNOG · Plugin · Speaker line-up · #plugin-speaker-lineup
 * Slug: swinog/plugin-speaker-lineup
 * Categories: swinog-events
 * Description: Speaker cards pulled live from the CFP tool (name, company, talk title). Edit the [swinog_list_speaker_lineup] shortcode's "event" to the CFP event slug (e.g. swinog-42) – not the event category. Anchor: #plugin-speaker-lineup
 * Inserter: true
 */
?>
<!-- wp:group {"anchor":"plugin-speaker-lineup","tagName":"section","className":"swinog-speakers-wrap swinog-plugin-wrap","align":"full","layout":{"type":"constrained","wideSize":"1280px"}} -->
<section id="plugin-speaker-lineup" class="wp-block-group alignfull swinog-speakers-wrap swinog-plugin-wrap">

	<!-- wp:group {"className":"swinog-speakers__head","align":"wide","layout":{"type":"default"}} -->
	<div class="wp-block-group alignwide swinog-speakers__head">
		<!-- wp:columns {"verticalAlignment":"bottom"} -->
		<div class="wp-block-columns are-vertically-aligned-bottom">
			<!-- wp:column {"width":"36%","verticalAlignment":"bottom"} -->
			<div class="wp-block-column is-vertically-aligned-bottom" style="flex-basis:36%">
				<!-- wp:paragraph {"className":"swinog-kicker swinog-kicker--accent"} -->
				<p class="swinog-kicker swinog-kicker--accent">Speaker line-up</p>
				<!-- /wp:paragraph -->
				<!-- wp:heading {"level":2,"className":"swinog-display-sm"} -->
				<h2 class="wp-block-heading swinog-display-sm">Who's on stage.</h2>
				<!-- /wp:heading -->
			</div>
			<!-- /wp:column -->
			<!-- wp:column {"width":"60%","verticalAlignment":"bottom"} -->
			<div class="wp-block-column is-vertically-aligned-bottom" style="flex-basis:60%">
				<!-- wp:paragraph {"className":"swinog-speakers__lead"} -->
				<p class="swinog-speakers__lead">Operators, researchers and builders from the Swiss network community. The line-up grows as talks are confirmed – check back for the full programme.</p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:column -->
		</div>
		<!-- /wp:columns -->
	</div>
	<!-- /wp:group -->

	<!-- wp:group {"align":"wide","layout":{"type":"default"}} -->
	<div class="wp-block-group alignwide">
<!-- wp:shortcode -->
[swinog_list_speaker_lineup event="swinog-42"]
<!-- /wp:shortcode -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->
