<?php
/**
 * Title: SwiNOG · Archive hero
 * Slug: swinog/archive-hero
 * Categories: swinog
 * Description: Past-meetings hero — breadcrumb, kicker, big H1, lead, inline search, 2×2 stat grid.
 * Inserter: true
 */
?>
<!-- wp:group {"tagName":"section","className":"swinog-archive-hero-wrap","align":"full","layout":{"type":"constrained","wideSize":"1280px"}} -->
<section class="wp-block-group alignfull swinog-archive-hero-wrap">

	<!-- wp:group {"className":"swinog-archive-hero","align":"wide","layout":{"type":"default"}} -->
	<div class="wp-block-group alignwide swinog-archive-hero">

		<!-- wp:columns {"verticalAlignment":"bottom"} -->
		<div class="wp-block-columns are-vertically-aligned-bottom">

			<!-- wp:column {"width":"58%","verticalAlignment":"bottom"} -->
			<div class="wp-block-column is-vertically-aligned-bottom" style="flex-basis:58%">
				<!-- wp:paragraph {"className":"swinog-kicker swinog-kicker--accent"} -->
				<p class="swinog-kicker swinog-kicker--accent">Archive</p>
				<!-- /wp:paragraph -->

				<!-- wp:heading {"level":1,"className":"swinog-archive-hero__title"} -->
				<h1 class="wp-block-heading swinog-archive-hero__title">41 meetings.<br />Twenty-five years of operating the Swiss Internet.</h1>
				<!-- /wp:heading -->

				<!-- wp:paragraph {"className":"swinog-archive-hero__lead"} -->
				<p class="swinog-archive-hero__lead">Every talk since SwiNOG #1 (Berne, 18 October 2000) has slides. Most have recordings since 2014. Search by topic, filter by year, or just scroll — there's a lot of operational lore in here.</p>
				<!-- /wp:paragraph -->

				<!-- wp:html -->
				<form class="swinog-archive-hero__search" action="/" method="get" role="search">
					<label class="swinog-visually-hidden" for="swinog-archive-q">Search talks, speakers, topics</label>
					<input id="swinog-archive-q" name="s" type="search" placeholder="Search talks, speakers, topics…" />
					<button type="submit">Search →</button>
				</form>
				<!-- /wp:html -->
			</div>
			<!-- /wp:column -->

			<!-- wp:column {"width":"38%","verticalAlignment":"bottom"} -->
			<div class="wp-block-column is-vertically-aligned-bottom" style="flex-basis:38%">
				<!-- wp:html -->
				<div class="swinog-archive-hero__stats">
					<div class="swinog-stat-card"><div class="swinog-stat-card__v">41</div><div class="swinog-stat-card__k">Meetings</div></div>
					<div class="swinog-stat-card"><div class="swinog-stat-card__v">412</div><div class="swinog-stat-card__k">Talks indexed</div></div>
					<div class="swinog-stat-card"><div class="swinog-stat-card__v">~7.8k</div><div class="swinog-stat-card__k">Attendees (cumulative)</div></div>
					<div class="swinog-stat-card"><div class="swinog-stat-card__v">25</div><div class="swinog-stat-card__k">Years</div></div>
				</div>
				<!-- /wp:html -->
			</div>
			<!-- /wp:column -->
		</div>
		<!-- /wp:columns -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->
