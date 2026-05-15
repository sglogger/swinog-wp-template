<?php
/**
 * Title: SwiNOG · Soft hero · #soft-hero
 * Slug: swinog/soft-hero
 * Categories: swinog
 * Description: Rounded gradient hero with copy left, next-meeting card right, decorative dot grid. Anchor: #soft-hero
 * Inserter: true
 */
?>
<!-- wp:group {"anchor":"soft-hero","tagName":"section","className":"swinog-soft-hero-wrap","align":"full","layout":{"type":"constrained","wideSize":"1280px"}} -->
<section id="soft-hero" class="wp-block-group alignfull swinog-soft-hero-wrap">

	<!-- wp:group {"className":"swinog-soft-hero","align":"wide","layout":{"type":"default"}} -->
	<div class="wp-block-group alignwide swinog-soft-hero">

		<!-- wp:columns {"className":"swinog-soft-hero__grid","verticalAlignment":"bottom"} -->
		<div class="wp-block-columns swinog-soft-hero__grid are-vertically-aligned-bottom">

			<!-- wp:column {"width":"56%","verticalAlignment":"bottom"} -->
			<div class="wp-block-column is-vertically-aligned-bottom" style="flex-basis:56%">
				<!-- wp:html -->
				<div class="swinog-pill swinog-pill--status">
					<span class="swinog-pill__dot" aria-hidden="true"></span>
					<span><strong>SwiNOG #42</strong> · In planning</span>
				</div>
				<!-- /wp:html -->

				<!-- wp:heading {"level":1,"className":"swinog-display-xl"} -->
				<h1 class="wp-block-heading swinog-display-xl">A community for the people who run Swiss networks.</h1>
				<!-- /wp:heading -->

				<!-- wp:paragraph {"className":"swinog-lead"} -->
				<p class="swinog-lead">An informal forum for the people who design and operate the Swiss Internet. We meet every 3–4 months in Berne and keep the mailing list humming between meetings. Non-political, not a lobby, no membership fees.</p>
				<!-- /wp:paragraph -->

				<!-- wp:buttons {"className":"swinog-cta-row"} -->
				<div class="wp-block-buttons swinog-cta-row">
					<!-- wp:button {"className":"swinog-btn swinog-btn--primary"} -->
					<div class="wp-block-button swinog-btn swinog-btn--primary"><a class="wp-block-button__link wp-element-button" href="https://lists.swinog.ch/postorius/lists/swinog.lists.swinog.ch/">Join the mailing list →</a></div>
					<!-- /wp:button -->
					<!-- wp:button {"className":"swinog-btn swinog-btn--secondary"} -->
					<div class="wp-block-button swinog-btn swinog-btn--secondary"><a class="wp-block-button__link wp-element-button" href="/meetings/swinog41/">Watch SwiNOG #41 talks</a></div>
					<!-- /wp:button -->
				</div>
				<!-- /wp:buttons -->

				<!-- wp:html -->
				<div class="swinog-hero-stats">
					<span><strong>41</strong> meetings since 2000</span>
					<span>Founded <strong>24 Feb 2000</strong></span>
					<span><strong>Always free</strong> to attend</span>
				</div>
				<!-- /wp:html -->
			</div>
			<!-- /wp:column -->

			<!-- wp:column {"width":"40%","verticalAlignment":"bottom"} -->
			<div class="wp-block-column is-vertically-aligned-bottom" style="flex-basis:40%">
				<!-- wp:html -->
				<div class="swinog-soft-event-card swinog-soft-shadow">
					<div class="swinog-soft-event-card__head">
						<div class="swinog-soft-event-card__title">
							<span class="swinog-soft-event-card__dot" aria-hidden="true"></span>
							<span>Next meeting</span>
						</div>
						<span class="swinog-mono swinog-soft-event-card__no">#42</span>
					</div>
					<div class="swinog-soft-event-card__body">
						<div class="swinog-soft-event-card__when">Date to be announced</div>
						<div class="swinog-soft-event-card__where">Berne</div>
						<div class="swinog-soft-event-card__stats">
							<div class="swinog-stat"><div class="swinog-stat__k">Talks at #41</div><div class="swinog-stat__v">14</div></div>
							<div class="swinog-stat"><div class="swinog-stat__k">Days since #41</div><div class="swinog-stat__v">16<span class="swinog-stat__suf">d</span></div></div>
							<div class="swinog-stat"><div class="swinog-stat__k">CFP for #42</div><div class="swinog-stat__v swinog-stat__v--small">Opening soon</div></div>
							<div class="swinog-stat"><div class="swinog-stat__k">Cadence</div><div class="swinog-stat__v swinog-stat__v--small">~3 / yr</div></div>
						</div>
						<a class="swinog-soft-event-card__cta" href="/cfp/">
							<span>Submit a talk for SwiNOG #42</span><span aria-hidden="true">→</span>
						</a>
					</div>
				</div>
				<!-- /wp:html -->
			</div>
			<!-- /wp:column -->
		</div>
		<!-- /wp:columns -->

		<!-- wp:html -->
		<svg class="swinog-soft-hero__dots" width="360" height="360" viewBox="0 0 360 360" aria-hidden="true" focusable="false">
			<defs>
				<pattern id="swinog-hero-dots" width="14" height="14" patternUnits="userSpaceOnUse">
					<circle cx="2" cy="2" r="1.1" fill="currentColor" />
				</pattern>
			</defs>
			<rect width="360" height="360" fill="url(#swinog-hero-dots)" />
		</svg>
		<!-- /wp:html -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->
