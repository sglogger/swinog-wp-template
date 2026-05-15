<?php
/**
 * Title: SwiNOG · Core team · #about-board
 * Slug: swinog/about-board
 * Categories: swinog
 * Description: Avatar cards for visible Core Team members + dashed "open seat" rotating-role cards. Anchor: #about-board
 * Inserter: true
 */
?>
<!-- wp:group {"anchor":"about-board","tagName":"section","className":"swinog-board-wrap","align":"full","layout":{"type":"constrained","wideSize":"1280px"}} -->
<section id="about-board" class="wp-block-group alignfull swinog-board-wrap">

	<!-- wp:group {"className":"swinog-board__head","align":"wide","layout":{"type":"default"}} -->
	<div class="wp-block-group alignwide swinog-board__head">
		<!-- wp:columns {"verticalAlignment":"bottom"} -->
		<div class="wp-block-columns are-vertically-aligned-bottom">
			<!-- wp:column {"width":"36%","verticalAlignment":"bottom"} -->
			<div class="wp-block-column is-vertically-aligned-bottom" style="flex-basis:36%">
				<!-- wp:paragraph {"className":"swinog-kicker swinog-kicker--accent"} -->
				<p class="swinog-kicker swinog-kicker--accent">Core team</p>
				<!-- /wp:paragraph -->
				<!-- wp:heading {"level":2,"className":"swinog-display-sm"} -->
				<h2 class="wp-block-heading swinog-display-sm">Volunteers who keep this running.</h2>
				<!-- /wp:heading -->
			</div>
			<!-- /wp:column -->
			<!-- wp:column {"width":"60%","verticalAlignment":"bottom"} -->
			<div class="wp-block-column is-vertically-aligned-bottom" style="flex-basis:60%">
				<!-- wp:paragraph {"className":"swinog-board__lead"} -->
				<p class="swinog-board__lead">SwiNOG itself isn't a legal entity — the Core Team has set up a small association just for organising meetings. People rotate in and out of roles, but these are the folks you'll most often see at the front of the room.</p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:column -->
		</div>
		<!-- /wp:columns -->
	</div>
	<!-- /wp:group -->

	<!-- wp:html -->
	<div class="swinog-board__grid">
		<article class="swinog-board-card">
			<div class="swinog-board-card__avatar" aria-hidden="true">SR</div>
			<div>
				<div class="swinog-board-card__name">Simon Ryf</div>
				<div class="swinog-board-card__role">Chair · welcome &amp; agenda</div>
				<div class="swinog-board-card__org">SwiNOG · seen at #41 opening</div>
			</div>
		</article>
		<article class="swinog-board-card">
			<div class="swinog-board-card__avatar" aria-hidden="true">SG</div>
			<div>
				<div class="swinog-board-card__name">Steven Glogger</div>
				<div class="swinog-board-card__role">Treasurer · financial overview</div>
				<div class="swinog-board-card__org">SwiNOG · #40 financials, #41 closing</div>
			</div>
		</article>
		<article class="swinog-board-card">
			<div class="swinog-board-card__avatar" aria-hidden="true">MS</div>
			<div>
				<div class="swinog-board-card__name">Max Stucchi</div>
				<div class="swinog-board-card__role">Programme · routing security</div>
				<div class="swinog-board-card__org">SwiNOG · ASPA talk #41</div>
			</div>
		</article>
		<article class="swinog-board-card swinog-board-card--open">
			<div class="swinog-board-card__avatar swinog-board-card__avatar--open" aria-hidden="true">?</div>
			<div>
				<div class="swinog-board-card__name">Rotating role</div>
				<div class="swinog-board-card__role">CFP review &amp; programme</div>
				<div class="swinog-board-card__org">Talk to <a href="mailto:swinog-core@swinog.ch">swinog-core</a> if you want in.</div>
			</div>
		</article>
		<article class="swinog-board-card swinog-board-card--open">
			<div class="swinog-board-card__avatar swinog-board-card__avatar--open" aria-hidden="true">?</div>
			<div>
				<div class="swinog-board-card__name">Rotating role</div>
				<div class="swinog-board-card__role">Antispam WG coordinator</div>
				<div class="swinog-board-card__org">Operates the <code>swinog-antispam</code> list.</div>
			</div>
		</article>
		<article class="swinog-board-card swinog-board-card--open">
			<div class="swinog-board-card__avatar swinog-board-card__avatar--open" aria-hidden="true">?</div>
			<div>
				<div class="swinog-board-card__name">Rotating role</div>
				<div class="swinog-board-card__role">Sponsor liaison</div>
				<div class="swinog-board-card__org">Quietly keeps the lights on every year.</div>
			</div>
		</article>
	</div>
	<!-- /wp:html -->
</section>
<!-- /wp:group -->
