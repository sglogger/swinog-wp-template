<?php
/**
 * Title: SwiNOG · Plugin · Agenda · #plugin-agenda
 * Slug: swinog/plugin-agenda
 * Categories: swinog-events
 * Description: Plugin-driven agenda. Edit the [swinog_list_agenda] shortcode's "event" slug to match the SwiNOG term (e.g. swinog-41). Anchor: #plugin-agenda
 * Inserter: true
 */
?>
<!-- wp:group {"anchor":"plugin-agenda","tagName":"section","className":"swinog-program-wrap swinog-plugin-wrap","align":"full","layout":{"type":"constrained","wideSize":"1280px"}} -->
<section id="plugin-agenda" class="wp-block-group alignfull swinog-program-wrap swinog-plugin-wrap">

	<!-- wp:group {"className":"swinog-program","align":"wide","layout":{"type":"default"}} -->
	<div class="wp-block-group alignwide swinog-program">

		<!-- wp:group {"className":"swinog-program__head","layout":{"type":"flex","justifyContent":"space-between","verticalAlignment":"bottom","flexWrap":"wrap"}} -->
		<div class="wp-block-group swinog-program__head">
			<!-- wp:group {"layout":{"type":"flex","orientation":"vertical"}} -->
			<div class="wp-block-group">
				<!-- wp:paragraph {"className":"swinog-kicker swinog-kicker--accent"} -->
				<p class="swinog-kicker swinog-kicker--accent">Program</p>
				<!-- /wp:paragraph -->
				<!-- wp:heading {"level":2,"className":"swinog-display-sm"} -->
				<h2 class="wp-block-heading swinog-display-sm">One day. Single track. Real talks.</h2>
				<!-- /wp:heading -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:group -->

<!-- wp:shortcode -->
[swinog_list_agenda event="swinog-41"]
<!-- /wp:shortcode -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->
