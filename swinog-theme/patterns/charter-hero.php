<?php
/**
 * Title: SwiNOG · Charter hero · #charter-hero
 * Slug: swinog/charter-hero
 * Categories: swinog
 * Description: Charter page hero — gradient card with kicker, big H1, lead, and an "at a glance" facts card. Anchor: #charter-hero
 * Inserter: true
 */
?>
<!-- wp:group {"anchor":"charter-hero","tagName":"section","className":"swinog-page-hero-wrap","align":"full","layout":{"type":"constrained","wideSize":"1280px"}} -->
<section id="charter-hero" class="wp-block-group alignfull swinog-page-hero-wrap">

	<!-- wp:group {"className":"swinog-page-hero","align":"wide","layout":{"type":"default"}} -->
	<div class="wp-block-group alignwide swinog-page-hero">

		<!-- wp:html -->
		<div class="swinog-page-hero__grid">
			<div class="swinog-page-hero__copy">
				<p class="swinog-kicker swinog-kicker--accent">Charter · since 2000</p>
				<h1 class="swinog-page-hero__title">The shortest possible explanation of what we are.</h1>
				<p class="swinog-page-hero__lead">The Swiss Network Operators Group is an informal group of people who deal with the technology and operation of the Swiss Internet. It was established in early 2000 and has met, roughly twice a year, ever since.</p>
			</div>
			<aside class="swinog-stat-card">
				<p class="swinog-stat-card__h">At a glance</p>
				<dl class="swinog-stat-card__dl">
					<dt>Founded</dt><dd>2000 (community)</dd>
					<dt>Incorporated</dt><dd>2009 (association)</dd>
					<dt>Form</dt><dd>Swiss Verein</dd>
					<dt>Members</dt><dd>800+ on the list</dd>
					<dt>Meetings</dt><dd>Twice a year</dd>
					<dt>Fee</dt><dd>CHF 0 to attend</dd>
				</dl>
			</aside>
		</div>
		<svg class="swinog-page-hero__dots" width="320" height="320" viewBox="0 0 320 320" aria-hidden="true">
			<defs>
				<pattern id="charter-hero-dots" width="14" height="14" patternUnits="userSpaceOnUse">
					<circle cx="2" cy="2" r="1.1" fill="currentColor"/>
				</pattern>
			</defs>
			<rect width="320" height="320" fill="url(#charter-hero-dots)" />
		</svg>
		<!-- /wp:html -->

	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->
