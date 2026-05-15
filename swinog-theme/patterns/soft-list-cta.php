<?php
/**
 * Title: SwiNOG · Mailing-list CTA
 * Slug: swinog/soft-list-cta
 * Categories: swinog
 * Description: Full-width dark gradient block, outline pill, email signup form, corner accent glow.
 * Inserter: true
 */
?>
<!-- wp:group {"tagName":"section","className":"swinog-soft-list-wrap","align":"full","layout":{"type":"constrained","wideSize":"1280px"}} -->
<section class="wp-block-group alignfull swinog-soft-list-wrap">

	<!-- wp:html -->
	<div class="swinog-soft-list">
		<div class="swinog-soft-list__copy">
			<div class="swinog-pill swinog-pill--outline">The mailing list</div>
			<h2 class="swinog-display-md">Stay close to your peers.</h2>
			<p class="swinog-soft-list__lead">
				Subscribe to <strong>swinog@lists.swinog.ch</strong> and read what the rest of the Swiss
				operator community is working on — between meetings. Hosted on Mailman 3, archived publicly
				on Hyperkitty. Quiet on quiet weeks; loud when something catches fire.
			</p>
			<div class="swinog-soft-list__stats">
				<span><strong>swinog</strong> · main list</span>
				<span><strong>swinog-antispam</strong> · WG</span>
				<span><strong>swinog-jobs</strong> · postings</span>
			</div>
		</div>
		<form class="swinog-soft-list__form" action="https://lists.swinog.ch/postorius/lists/swinog.lists.swinog.ch/" method="get">
			<label class="swinog-visually-hidden" for="swinog-list-email">Your email address</label>
			<input id="swinog-list-email" name="email" type="email" required placeholder="you@operator.ch" autocomplete="email" />
			<button type="submit">Subscribe</button>
		</form>
		<div class="swinog-soft-list__glow" aria-hidden="true"></div>
	</div>
	<!-- /wp:html -->
</section>
<!-- /wp:group -->
