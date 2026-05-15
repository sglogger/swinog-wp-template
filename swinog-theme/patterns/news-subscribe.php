<?php
/**
 * Title: SwiNOG · News subscribe · #news-subscribe
 * Slug: swinog/news-subscribe
 * Categories: swinog
 * Description: Inline tonal subscribe card for the news index — left blurb, right email form. Anchor: #news-subscribe
 * Inserter: true
 */
?>
<!-- wp:group {"anchor":"news-subscribe","tagName":"section","className":"swinog-news-subscribe-wrap","align":"full","layout":{"type":"constrained","wideSize":"1280px"}} -->
<section id="news-subscribe" class="wp-block-group alignfull swinog-news-subscribe-wrap">

	<!-- wp:html -->
	<div class="swinog-news-subscribe">
		<div class="swinog-news-subscribe__copy">
			<p class="swinog-kicker">Stay in the loop</p>
			<h3 class="swinog-news-subscribe__title">One e-mail per post. No noise.</h3>
			<p class="swinog-news-subscribe__lead">Subscribe to <code>swinog-announce@lists.swinog.ch</code> for new posts and meeting announcements, or read the same content via RSS.</p>
		</div>
		<form class="swinog-news-subscribe__form" action="https://lists.swinog.ch/postorius/lists/swinog-announce.lists.swinog.ch/" method="get">
			<label class="swinog-visually-hidden" for="swinog-news-email">Your email address</label>
			<input id="swinog-news-email" name="email" type="email" required placeholder="you@operator.ch" autocomplete="email" />
			<button type="submit">Subscribe</button>
		</form>
	</div>
	<!-- /wp:html -->
</section>
<!-- /wp:group -->
