<?php
/**
 * Title: SwiNOG · News related · #news-related
 * Slug: swinog/news-related
 * Categories: swinog
 * Description: Three related-post cards at the foot of a single news post. Dynamic via core/query. Anchor: #news-related
 * Inserter: true
 */
?>
<!-- wp:group {"anchor":"news-related","tagName":"section","className":"swinog-news-related-wrap","align":"full","layout":{"type":"constrained","wideSize":"1280px"}} -->
<section id="news-related" class="wp-block-group alignfull swinog-news-related-wrap">

	<!-- wp:group {"className":"swinog-news-related","align":"wide","layout":{"type":"default"}} -->
	<div class="wp-block-group alignwide swinog-news-related">

		<!-- wp:paragraph {"className":"swinog-kicker swinog-kicker--accent"} -->
		<p class="swinog-kicker swinog-kicker--accent">More from SwiNOG</p>
		<!-- /wp:paragraph -->

		<!-- wp:heading {"level":2,"className":"swinog-h4"} -->
		<h2 class="wp-block-heading swinog-h4">Recent posts.</h2>
		<!-- /wp:heading -->

		<!-- wp:query {"queryId":62,"query":{"perPage":3,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","inherit":false}} -->
		<div class="wp-block-query">
			<!-- wp:post-template {"className":"swinog-news-related__grid"} -->
				<!-- wp:group {"className":"swinog-news-card","layout":{"type":"flex","orientation":"vertical"}} -->
				<div class="wp-block-group swinog-news-card">
					<!-- wp:group {"className":"swinog-news-card__meta","layout":{"type":"flex","flexWrap":"wrap"}} -->
					<div class="wp-block-group swinog-news-card__meta">
						<!-- wp:post-terms {"term":"category","className":"swinog-tag swinog-tag--filled"} /-->
						<!-- wp:post-date {"format":"Y-m-d","fontSize":"meta"} /-->
					</div>
					<!-- /wp:group -->
					<!-- wp:post-title {"level":3,"isLink":true,"className":"swinog-news-card__title"} /-->
					<!-- wp:post-excerpt {"className":"swinog-news-card__excerpt","excerptLength":24} /-->
				</div>
				<!-- /wp:group -->
			<!-- /wp:post-template -->

			<!-- wp:query-no-results -->
				<!-- wp:paragraph -->
				<p>No other posts yet.</p>
				<!-- /wp:paragraph -->
			<!-- /wp:query-no-results -->
		</div>
		<!-- /wp:query -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->
