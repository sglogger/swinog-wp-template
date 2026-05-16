<?php
/**
 * Title: SwiNOG · Sponsor contact · #sponsor-contact
 * Slug: swinog/sponsor-contact
 * Categories: swinog
 * Description: Dark contact CTA with two buttons + bank-details card with IBAN and a thank-you note. Anchor: #sponsor-contact
 * Inserter: true
 */
?>
<!-- wp:group {"anchor":"sponsor-contact","tagName":"section","className":"swinog-darkcta-wrap","align":"full","layout":{"type":"constrained","wideSize":"1280px"}} -->
<section id="sponsor-contact" class="wp-block-group alignfull swinog-darkcta-wrap">

	<!-- wp:html -->
	<div class="swinog-darkcta swinog-darkcta--solid">
		<div class="swinog-darkcta__copy">
			<p class="swinog-darkcta__kicker">Contact us</p>
			<h2 class="swinog-darkcta__h">Have a coffee. Settle it in an email.</h2>
			<p class="swinog-darkcta__lead">Email <a href="mailto:swinog-core@swinog.ch">swinog-core@swinog.ch</a> with the tier you're interested in, or with a custom amount and what you'd like in return. We typically reply inside a working day.</p>
			<div class="swinog-darkcta__row">
				<a class="swinog-darkcta__btn swinog-darkcta__btn--accent" href="mailto:swinog-core@swinog.ch">swinog-core@swinog.ch →</a>
				<a class="swinog-darkcta__btn swinog-darkcta__btn--ghost" href="#sponsor-packages">Back to packages</a>
			</div>
		</div>
		<div class="swinog-bank">
			<p class="swinog-bank__h">Bank details</p>
			<dl class="swinog-bank__dl">
				<dt>Bank</dt><dd>PostFinance, Nordring 8, 3030 Bern</dd>
				<dt>Account no.</dt><dd class="swinog-bank__mono">85-670591-4</dd>
				<dt>Holder</dt><dd>SwiNOG Organisation, 8000 Zurich</dd>
				<dt>IBAN</dt><dd class="swinog-bank__mono">CH85 0900 0000 8567 0591 4</dd>
			</dl>
			<div class="swinog-bank__note"><strong>Thank you.</strong> Without sponsors, SwiNOG meetings would not be free for the engineers who run Swiss networks.</div>
		</div>
	</div>
	<!-- /wp:html -->

</section>
<!-- /wp:group -->
