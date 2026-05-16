<?php
/**
 * Title: SwiNOG · Sponsor hero · #sponsor-hero
 * Slug: swinog/sponsor-hero
 * Categories: swinog
 * Description: Sponsor page hero — gradient card, H1, lead, two CTAs and a "who's in the room" facts card. Anchor: #sponsor-hero
 * Inserter: true
 */
?>
<!-- wp:group {"anchor":"sponsor-hero","tagName":"section","className":"swinog-page-hero-wrap","align":"full","layout":{"type":"constrained","wideSize":"1280px"}} -->
<section id="sponsor-hero" class="wp-block-group alignfull swinog-page-hero-wrap">

	<!-- wp:group {"className":"swinog-page-hero","align":"wide","layout":{"type":"default"}} -->
	<div class="wp-block-group alignwide swinog-page-hero">

		<!-- wp:html -->
		<div class="swinog-page-hero__grid">
			<div class="swinog-page-hero__copy">
				<p class="swinog-kicker swinog-kicker--accent">Sponsoring · 2026 edition</p>
				<h1 class="swinog-page-hero__title">Sponsor a room full of the people who actually run Swiss networks.</h1>
				<p class="swinog-page-hero__lead">SwiNOG relies on the support of the community for everything — presentations, loaner equipment, and the venue itself. Your sponsorship keeps the meeting free for the engineers who attend, and puts you in front of them at the same time.</p>
				<div class="swinog-page-hero__ctas">
					<a class="swinog-page-hero__btn swinog-page-hero__btn--ink" href="#sponsor-packages">See packages →</a>
					<a class="swinog-page-hero__btn swinog-page-hero__btn--light" href="#sponsor-contact">Talk to us</a>
				</div>
			</div>
			<aside class="swinog-stat-card">
				<p class="swinog-stat-card__h">Who's in the room</p>
				<dl class="swinog-stat-card__dl">
					<dt>Attendees</dt><dd>~100 / meeting</dd>
					<dt>List reach</dt><dd>800+ subscribers</dd>
					<dt>Audience</dt><dd>Engineers, architects, C-level</dd>
					<dt>Frequency</dt><dd>Bi-annual</dd>
					<dt>Entry-level</dt><dd>From CHF 2'900</dd>
					<dt>Acknowledgement</dt><dd>In perpetuity on site</dd>
				</dl>
			</aside>
		</div>
		<svg class="swinog-page-hero__dots" width="320" height="320" viewBox="0 0 320 320" aria-hidden="true">
			<defs>
				<pattern id="sponsor-hero-dots" width="14" height="14" patternUnits="userSpaceOnUse">
					<circle cx="2" cy="2" r="1.1" fill="currentColor"/>
				</pattern>
			</defs>
			<rect width="320" height="320" fill="url(#sponsor-hero-dots)" />
		</svg>
		<!-- /wp:html -->

	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->
