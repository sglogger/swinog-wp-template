<?php
/**
 * Title: SwiNOG · Announcement · #announcement
 * Slug: swinog/announcement
 * Categories: swinog
 * Description: Highlight / breaking-news box — eyebrow, headline, short text and a red call-to-action button. Edit the text and point the button at any page. Anchor: #announcement
 * Inserter: true
 */
?>
<!-- wp:group {"anchor":"announcement","tagName":"section","className":"swinog-announce-wrap","align":"full","layout":{"type":"constrained","wideSize":"1280px"}} -->
<section id="announcement" class="wp-block-group alignfull swinog-announce-wrap">

	<!-- wp:group {"className":"swinog-announce","align":"wide","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"center"}} -->
	<div class="wp-block-group alignwide swinog-announce">

		<!-- wp:group {"className":"swinog-announce__copy","layout":{"type":"default"}} -->
		<div class="wp-block-group swinog-announce__copy">
			<!-- wp:paragraph {"className":"swinog-kicker swinog-kicker--accent"} -->
			<p class="swinog-kicker swinog-kicker--accent">Call for help</p>
			<!-- /wp:paragraph -->

			<!-- wp:heading {"level":2,"className":"swinog-announce__title"} -->
			<h2 class="wp-block-heading swinog-announce__title">We're looking for a new SwiNOG logo.</h2>
			<!-- /wp:heading -->

			<!-- wp:paragraph {"className":"swinog-announce__text"} -->
			<p class="swinog-announce__text">Got design chops, or know someone who has? We'd love a fresh take on the SwiNOG identity. Send us your proposal — the community picks the winner.</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->

		<!-- wp:buttons {"className":"swinog-announce__actions"} -->
		<div class="wp-block-buttons swinog-announce__actions">
			<!-- wp:button {"className":"swinog-btn swinog-btn--accent"} -->
			<div class="wp-block-button swinog-btn swinog-btn--accent"><a class="wp-block-button__link wp-element-button" href="/contact/">Submit a logo →</a></div>
			<!-- /wp:button -->
		</div>
		<!-- /wp:buttons -->

	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->
