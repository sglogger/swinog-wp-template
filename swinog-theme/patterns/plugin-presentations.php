<?php
/**
 * Title: SwiNOG · Plugin · Presentations (with slides + video) · #plugin-presentations
 * Slug: swinog/plugin-presentations
 * Categories: swinog-events
 * Description: Plugin-driven presentations table — title, presenter, company, slides + video links. Edit the "event" slug. Anchor: #plugin-presentations
 * Inserter: true
 */
?>
<!-- wp:group {"anchor":"plugin-presentations","tagName":"section","className":"swinog-program-wrap swinog-plugin-wrap","align":"full","layout":{"type":"constrained","wideSize":"1280px"}} -->
<section id="plugin-presentations" class="wp-block-group alignfull swinog-program-wrap swinog-plugin-wrap">

	<!-- wp:group {"className":"swinog-program","align":"wide","layout":{"type":"default"}} -->
	<div class="wp-block-group alignwide swinog-program">

		<!-- wp:group {"className":"swinog-program__head","layout":{"type":"flex","justifyContent":"space-between","verticalAlignment":"bottom","flexWrap":"wrap"}} -->
		<div class="wp-block-group swinog-program__head">
			<!-- wp:group {"layout":{"type":"flex","orientation":"vertical"}} -->
			<div class="wp-block-group">
				<!-- wp:paragraph {"className":"swinog-kicker swinog-kicker--accent"} -->
				<p class="swinog-kicker swinog-kicker--accent">Presentations</p>
				<!-- /wp:paragraph -->
				<!-- wp:heading {"level":2,"className":"swinog-display-sm"} -->
				<h2 class="wp-block-heading swinog-display-sm">Talks, slides &amp; recordings.</h2>
				<!-- /wp:heading -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:group -->

<!-- wp:shortcode -->
[swinog_list_presentations event="swinog-41"]
<!-- /wp:shortcode -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->
