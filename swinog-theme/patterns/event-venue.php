<?php
/**
 * Title: SwiNOG · Event venue · #event-venue
 * Slug: swinog/event-venue
 * Categories: swinog
 * Description: Two-column venue card — editable copy + facts list on the left, auto-generated OpenStreetMap PNG on the right (filled by the SwiNOG · Event details meta box). Anchor: #event-venue
 * Inserter: true
 */
?>
<!-- wp:group {"anchor":"event-venue","tagName":"section","className":"swinog-venue-wrap","align":"full","layout":{"type":"constrained","wideSize":"1280px"}} -->
<section id="event-venue" class="wp-block-group alignfull swinog-venue-wrap">

	<!-- wp:group {"className":"swinog-venue","layout":{"type":"default"}} -->
	<div class="wp-block-group swinog-venue">

		<!-- wp:group {"className":"swinog-venue__copy","layout":{"type":"default"}} -->
		<div class="wp-block-group swinog-venue__copy">
			<!-- wp:paragraph {"className":"swinog-kicker swinog-kicker--accent"} -->
			<p class="swinog-kicker swinog-kicker--accent">Venue</p>
			<!-- /wp:paragraph -->

			<!-- wp:heading {"level":2,"className":"swinog-display-sm"} -->
			<h2 class="wp-block-heading swinog-display-sm">Kursaal Berne.</h2>
			<!-- /wp:heading -->

			<!-- wp:paragraph -->
			<p>Kornhausstrasse 3, 3013 Berne. Six minutes on foot from Bern HB, or one stop on tram 9. A/V is taken care of. So is the coffee.</p>
			<!-- /wp:paragraph -->

			<!-- wp:html -->
			<dl class="swinog-venue__dl">
				<dt>From Bern HB</dt><dd>6 min walk · 1 stop tram 9</dd>
				<dt>Wifi</dt><dd>SwiNOG-41 · eduroam</dd>
				<dt>Accessibility</dt><dd>Step-free · loop-equipped</dd>
				<dt>Recommended hotels</dt><dd><a href="/meetings/swinog41/#hotels">Bern HB area →</a></dd>
				<dt>Travel</dt><dd>SBB direct from ZRH, GVA, BSL</dd>
			</dl>
			<!-- /wp:html -->
		</div>
		<!-- /wp:group -->

		<!-- wp:swinog/venue-map /-->

	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->
