<?php
/**
 * Title: SwiNOG · News list (dynamic) · #news-list
 * Slug: swinog/news-list
 * Categories: swinog
 * Description: Two-column grid of recent posts. Uses core/query to pull from real Posts. Anchor: #news-list
 * Inserter: true
 */
?>
<!-- wp:group {"anchor":"news-list","tagName":"section","className":"swinog-news-list-wrap","align":"full","layout":{"type":"constrained","wideSize":"1280px"}} -->
<section id="news-list" class="wp-block-group alignfull swinog-news-list-wrap">

	<!-- wp:query {"queryId":51,"query":{"perPage":10,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","inherit":true},"align":"wide","className":"swinog-news-list"} -->
	<div class="wp-block-query alignwide swinog-news-list">

		<!-- wp:post-template {"className":"swinog-news-list__grid"} -->
			<!-- wp:group {"className":"swinog-news-card","layout":{"type":"default"}} -->
			<div class="wp-block-group swinog-news-card">
				<!-- wp:group {"className":"swinog-news-card__copy","layout":{"type":"flex","orientation":"vertical"}} -->
				<div class="wp-block-group swinog-news-card__copy">
					<!-- wp:group {"className":"swinog-news-card__meta","layout":{"type":"flex","verticalAlignment":"center","flexWrap":"wrap"}} -->
					<div class="wp-block-group swinog-news-card__meta">
						<!-- wp:post-terms {"term":"category","className":"swinog-tag swinog-tag--filled"} /-->
						<!-- wp:post-date {"format":"d.m.Y","className":"swinog-news-card__date","fontSize":"meta"} /-->
					</div>
					<!-- /wp:group -->
					<!-- wp:post-title {"level":3,"isLink":true,"className":"swinog-news-card__title"} /-->
					<!-- wp:post-excerpt {"className":"swinog-news-card__excerpt"} /-->
					<!-- wp:group {"className":"swinog-news-card__byline","layout":{"type":"flex","verticalAlignment":"center","flexWrap":"wrap"}} -->
					<div class="wp-block-group swinog-news-card__byline">
						<!-- wp:post-author-name {"className":"swinog-news-card__by","isLink":false} /-->
					</div>
					<!-- /wp:group -->
				</div>
				<!-- /wp:group -->
				<!-- wp:group {"className":"swinog-news-card__media","layout":{"type":"default"}} -->
				<div class="wp-block-group swinog-news-card__media">
					<!-- wp:post-featured-image {"isLink":true} /-->
				</div>
				<!-- /wp:group -->
			</div>
			<!-- /wp:group -->
		<!-- /wp:post-template -->

		<!-- wp:query-pagination {"className":"swinog-pagination","layout":{"type":"flex","justifyContent":"center","flexWrap":"wrap"}} -->
			<!-- wp:query-pagination-previous {"label":"← Newer"} /-->
			<!-- wp:query-pagination-numbers /-->
			<!-- wp:query-pagination-next {"label":"Older →"} /-->
		<!-- /wp:query-pagination -->

		<!-- wp:query-no-results -->
			<!-- wp:paragraph {"align":"center"} -->
			<p class="has-text-align-center">Nothing posted yet. Write one under <strong>Posts → Add new</strong>.</p>
			<!-- /wp:paragraph -->
		<!-- /wp:query-no-results -->
	</div>
	<!-- /wp:query -->
</section>
<!-- /wp:group -->
