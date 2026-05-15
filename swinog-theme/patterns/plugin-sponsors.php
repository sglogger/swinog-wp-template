<?php
/**
 * Title: SwiNOG · Plugin · Sponsors · #plugin-sponsors
 * Slug: swinog/plugin-sponsors
 * Categories: swinog-events
 * Description: Plugin-driven sponsor grid grouped by tier. Edit the "event" slug; "layout=list" for a flat grid. Anchor: #plugin-sponsors
 * Inserter: true
 */
?>
<!-- wp:group {"anchor":"plugin-sponsors","tagName":"section","className":"swinog-tiers-wrap swinog-plugin-wrap","align":"full","layout":{"type":"constrained","wideSize":"1280px"}} -->
<section id="plugin-sponsors" class="wp-block-group alignfull swinog-tiers-wrap swinog-plugin-wrap">

	<!-- wp:group {"className":"swinog-tiers__head","align":"wide","layout":{"type":"default"}} -->
	<div class="wp-block-group alignwide swinog-tiers__head">
		<!-- wp:paragraph {"className":"swinog-kicker swinog-kicker--accent"} -->
		<p class="swinog-kicker swinog-kicker--accent">Sponsors</p>
		<!-- /wp:paragraph -->
		<!-- wp:heading {"level":2,"className":"swinog-display-sm"} -->
		<h2 class="wp-block-heading swinog-display-sm">For this meeting.</h2>
		<!-- /wp:heading -->
	</div>
	<!-- /wp:group -->

<!-- wp:shortcode -->
[swinog_sponsor event="swinog-41" layout="tiers"]
<!-- /wp:shortcode -->
</section>
<!-- /wp:group -->
