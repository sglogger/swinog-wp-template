<?php
/**
 * Title: SwiNOG · Archive filters · #archive-filters
 * Slug: swinog/archive-filters
 * Categories: swinog
 * Description: Year-range and topic pill filters for the meetings archive. Anchor: #archive-filters
 * Inserter: true
 */
?>
<!-- wp:group {"anchor":"archive-filters","tagName":"section","className":"swinog-filters-wrap","align":"full","layout":{"type":"constrained","wideSize":"1280px"}} -->
<section id="archive-filters" class="wp-block-group alignfull swinog-filters-wrap">

	<!-- wp:html -->
	<div class="swinog-filters">
		<span class="swinog-filters__label">Years</span>
		<a class="swinog-chip swinog-chip--active" href="?year=all">All</a>
		<a class="swinog-chip" href="?year=2025-26">2025–26</a>
		<a class="swinog-chip" href="?year=2020-24">2020–24</a>
		<a class="swinog-chip" href="?year=2010-19">2010–19</a>
		<a class="swinog-chip" href="?year=2000-09">2000–09</a>
		<span class="swinog-filters__divider" aria-hidden="true"></span>
		<span class="swinog-filters__label">Topics</span>
		<a class="swinog-chip" href="?topic=bgp">BGP</a>
		<a class="swinog-chip" href="?topic=ipv6">IPv6</a>
		<a class="swinog-chip" href="?topic=rpki">RPKI</a>
		<a class="swinog-chip" href="?topic=peering">Peering</a>
		<a class="swinog-chip" href="?topic=automation">Automation</a>
		<a class="swinog-chip" href="?topic=optics">Optics</a>
		<a class="swinog-chip" href="?topic=scion">SCION</a>
		<a class="swinog-chip" href="?topic=outages">Outages</a>
	</div>
	<!-- /wp:html -->
</section>
<!-- /wp:group -->
