<?php
/**
 * Title: SwiNOG · Event hero
 * Slug: swinog/event-hero
 * Categories: swinog
 * Description: Event detail page hero. Left column is editable core blocks (pill, title, meta line, lead, CTAs — edit the text and button URLs directly). Right column is the SwiNOG · Event Quick Facts dynamic block, which auto-fills Date / Venue / Fee / Talks / Format / recording CTA from the SwiNOG · Event details meta box.
 * Inserter: true
 */
?>
<!-- wp:group {"tagName":"section","className":"swinog-event-hero-wrap","align":"wide","layout":{"type":"default"}} -->
<section class="wp-block-group alignwide swinog-event-hero-wrap">

	<!-- wp:group {"className":"swinog-event-hero","layout":{"type":"default"}} -->
	<div class="wp-block-group swinog-event-hero">

		<!-- wp:group {"className":"swinog-event-hero__copy","layout":{"type":"default"}} -->
		<div class="wp-block-group swinog-event-hero__copy">

			<!-- wp:swinog/event-pill /-->

			<!-- wp:heading {"level":1,"className":"swinog-event-hero__title"} -->
			<h1 class="wp-block-heading swinog-event-hero__title">SwiNOG #41</h1>
			<!-- /wp:heading -->

			<!-- wp:paragraph {"className":"swinog-event-hero__meta"} -->
			<p class="swinog-event-hero__meta">28 April 2026 · <strong>Kursaal Berne</strong></p>
			<!-- /wp:paragraph -->

			<!-- wp:paragraph {"className":"swinog-event-hero__lead"} -->
			<p class="swinog-event-hero__lead">One day of operator talks, hallway track, and one long evening at a long table. Single track. Free to attend. Fourteen talks across routing, peering, automation, transceivers, AI ops and policy. Videos and slides are linked from each entry below.</p>
			<!-- /wp:paragraph -->

			<!-- wp:buttons {"className":"swinog-cta-row"} -->
			<div class="wp-block-buttons swinog-cta-row">
				<!-- wp:button {"className":"swinog-btn swinog-btn--primary"} -->
				<div class="wp-block-button swinog-btn swinog-btn--primary"><a class="wp-block-button__link wp-element-button" href="#program">Jump to program →</a></div>
				<!-- /wp:button -->
				<!-- wp:button {"className":"swinog-btn swinog-btn--secondary"} -->
				<div class="wp-block-button swinog-btn swinog-btn--secondary"><a class="wp-block-button__link wp-element-button" href="https://lists.swinog.ch/postorius/lists/swinog.lists.swinog.ch/">Subscribe for #42</a></div>
				<!-- /wp:button -->
				<!-- wp:button {"className":"swinog-btn swinog-btn--ghost"} -->
				<div class="wp-block-button swinog-btn swinog-btn--ghost"><a class="wp-block-button__link wp-element-button" href="/meetings/swinog41.ics">Add to calendar</a></div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->

		</div>
		<!-- /wp:group -->

		<!-- wp:swinog/event-quickfacts /-->

	</div>
	<!-- /wp:group -->

</section>
<!-- /wp:group -->
