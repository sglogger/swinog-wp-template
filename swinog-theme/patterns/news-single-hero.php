<?php
/**
 * Title: SwiNOG · News single hero
 * Slug: swinog/news-single-hero
 * Categories: swinog
 * Description: Article header — tag chip, date + read-time, big serif-friendly H1, byline avatar + name.
 * Inserter: true
 */
?>
<!-- wp:group {"tagName":"header","className":"swinog-news-single-hero-wrap","align":"full","layout":{"type":"constrained","contentSize":"780px","wideSize":"1280px"}} -->
<header class="wp-block-group alignfull swinog-news-single-hero-wrap">

	<!-- wp:group {"className":"swinog-news-single-hero","layout":{"type":"default"}} -->
	<div class="wp-block-group swinog-news-single-hero">

		<!-- wp:group {"className":"swinog-news-single-hero__meta","layout":{"type":"flex","verticalAlignment":"center","flexWrap":"wrap"}} -->
		<div class="wp-block-group swinog-news-single-hero__meta">
			<!-- wp:post-terms {"term":"category","className":"swinog-tag swinog-tag--filled"} /-->
			<!-- wp:post-date {"format":"j F Y","fontSize":"meta","className":"swinog-news-single-hero__date"} /-->
		</div>
		<!-- /wp:group -->

		<!-- wp:post-title {"level":1,"className":"swinog-news-single-hero__title"} /-->

		<!-- wp:group {"className":"swinog-news-single-hero__byline","layout":{"type":"flex","verticalAlignment":"center"}} -->
		<div class="wp-block-group swinog-news-single-hero__byline">
			<!-- wp:avatar {"size":48,"className":"swinog-news-single-hero__avatar"} /-->
			<!-- wp:post-author {"showAvatar":false,"className":"swinog-news-single-hero__author"} /-->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</header>
<!-- /wp:group -->
