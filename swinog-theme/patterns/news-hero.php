<?php
/**
 * Title: SwiNOG · News hero
 * Slug: swinog/news-hero
 * Categories: swinog
 * Description: News-index page hero — kicker, H1, lead, RSS + list-subscribe CTAs on the right.
 * Inserter: true
 */
?>
<!-- wp:group {"tagName":"section","className":"swinog-news-hero-wrap","align":"full","layout":{"type":"constrained","wideSize":"1280px"}} -->
<section class="wp-block-group alignfull swinog-news-hero-wrap">

	<!-- wp:group {"className":"swinog-news-hero","align":"wide","layout":{"type":"default"}} -->
	<div class="wp-block-group alignwide swinog-news-hero">

		<!-- wp:columns {"verticalAlignment":"bottom"} -->
		<div class="wp-block-columns are-vertically-aligned-bottom">

			<!-- wp:column {"width":"62%","verticalAlignment":"bottom"} -->
			<div class="wp-block-column is-vertically-aligned-bottom" style="flex-basis:62%">
				<!-- wp:paragraph {"className":"swinog-kicker swinog-kicker--accent"} -->
				<p class="swinog-kicker swinog-kicker--accent">News</p>
				<!-- /wp:paragraph -->

				<!-- wp:heading {"level":1,"className":"swinog-news-hero__title"} -->
				<h1 class="wp-block-heading swinog-news-hero__title">What's happening<br />at SwiNOG.</h1>
				<!-- /wp:heading -->

				<!-- wp:paragraph {"className":"swinog-news-hero__lead"} -->
				<p class="swinog-news-hero__lead">Meeting wrap-ups, calls for talks, infrastructure changes, board notices. Roughly one post a month. Subscribe by RSS or pick it up from the list digest.</p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:column -->

			<!-- wp:column {"width":"34%","verticalAlignment":"bottom"} -->
			<div class="wp-block-column is-vertically-aligned-bottom" style="flex-basis:34%">
				<!-- wp:html -->
				<div class="swinog-news-hero__ctas">
					<a class="swinog-news-hero__cta" href="/feed/">
						<span>Subscribe via RSS</span><span class="swinog-news-hero__icon" aria-hidden="true">↗</span>
					</a>
					<a class="swinog-news-hero__cta" href="https://lists.swinog.ch/postorius/lists/swinog.lists.swinog.ch/">
						<span>Subscribe to the mailing list</span><span class="swinog-news-hero__icon" aria-hidden="true">→</span>
					</a>
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
