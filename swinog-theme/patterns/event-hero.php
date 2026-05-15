<?php
/**
 * Title: SwiNOG · Event hero
 * Slug: swinog/event-hero
 * Categories: swinog
 * Description: Event detail page hero — breadcrumb, status pill, H1 + date/venue, lead, CTAs, quick-facts card.
 * Inserter: true
 */
?>
<!-- wp:group {"tagName":"section","className":"swinog-event-hero-wrap","align":"full","layout":{"type":"constrained","wideSize":"1280px"}} -->
<section class="wp-block-group alignfull swinog-event-hero-wrap">

	<!-- wp:group {"className":"swinog-event-hero","align":"wide","layout":{"type":"default"}} -->
	<div class="wp-block-group alignwide swinog-event-hero">

		<!-- wp:columns {"verticalAlignment":"bottom","className":"swinog-event-hero__grid"} -->
		<div class="wp-block-columns are-vertically-aligned-bottom swinog-event-hero__grid">

			<!-- wp:column {"width":"60%","verticalAlignment":"bottom"} -->
			<div class="wp-block-column is-vertically-aligned-bottom" style="flex-basis:60%">
				<!-- wp:html -->
				<div class="swinog-pill swinog-pill--status">
					<span class="swinog-pill__dot" aria-hidden="true"></span>
					<span>Recording &amp; slides online</span>
				</div>
				<!-- /wp:html -->

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
			<!-- /wp:column -->

			<!-- wp:column {"width":"36%","verticalAlignment":"bottom"} -->
			<div class="wp-block-column is-vertically-aligned-bottom" style="flex-basis:36%">
				<!-- wp:html -->
				<aside class="swinog-event-hero__facts swinog-soft-shadow">
					<div class="swinog-kicker">Quick facts</div>
					<dl class="swinog-facts-dl">
						<dt>Date</dt><dd>Tue 28 April 2026</dd>
						<dt>Venue</dt><dd>Kursaal Berne</dd>
						<dt>Fee</dt><dd>CHF 0 — community funded</dd>
						<dt>Talks</dt><dd>14 · single track</dd>
						<dt>Format</dt><dd>One day · long-table dinner</dd>
						<dt>Code of conduct</dt><dd><a href="/code-of-conduct/">swinog.ch/coc →</a></dd>
					</dl>
					<a class="swinog-facts-dl__cta" href="#program">View the recordings</a>
				</aside>
				<!-- /wp:html -->
			</div>
			<!-- /wp:column -->
		</div>
		<!-- /wp:columns -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->
