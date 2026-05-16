<?php
/**
 * Title: SwiNOG · Soft hero · #soft-hero
 * Slug: swinog/soft-hero
 * Categories: swinog
 * Description: Rounded gradient hero with copy left, next-meeting card right, decorative dot grid. Anchor: #soft-hero
 * Inserter: true
 */

/* ------------------------------------------------------------------
 * Dynamic "days" stat
 *
 * Scan every published Page that carries a `swinog_event_date` +
 * `swinog_event_tag` meta. Pick the closest future meeting (if any)
 * to drive a countdown, otherwise fall back to the most recent past
 * meeting and show days-since. Empty fallback: a dash.
 * ------------------------------------------------------------------ */
$soft_hero_today = (int) current_time('timestamp');
$soft_hero_next  = null;
$soft_hero_last  = null;

$soft_hero_event_pages = get_posts([
    'post_type'              => 'page',
    'post_status'            => 'publish',
    'posts_per_page'         => -1,
    'fields'                 => 'ids',
    'no_found_rows'          => true,
    'update_post_term_cache' => false,
    'meta_query'             => [
        ['key' => 'swinog_event_date', 'compare' => 'EXISTS'],
        ['key' => 'swinog_event_tag',  'compare' => 'EXISTS'],
    ],
]);
foreach ($soft_hero_event_pages as $soft_hero_pid) {
    $soft_hero_d = trim((string) get_post_meta($soft_hero_pid, 'swinog_event_date', true));
    $soft_hero_t = trim((string) get_post_meta($soft_hero_pid, 'swinog_event_tag',  true));
    if ($soft_hero_d === '' || $soft_hero_t === '') {
        continue;
    }
    $soft_hero_ts = strtotime($soft_hero_d);
    if ($soft_hero_ts === false) {
        continue;
    }
    if ($soft_hero_ts >= $soft_hero_today) {
        if ($soft_hero_next === null || $soft_hero_ts < $soft_hero_next['ts']) {
            $soft_hero_next = ['ts' => $soft_hero_ts, 'tag' => $soft_hero_t];
        }
    } else {
        if ($soft_hero_last === null || $soft_hero_ts > $soft_hero_last['ts']) {
            $soft_hero_last = ['ts' => $soft_hero_ts, 'tag' => $soft_hero_t];
        }
    }
}

if ($soft_hero_next !== null) {
    $soft_hero_days  = max(0, (int) ceil(($soft_hero_next['ts'] - $soft_hero_today) / DAY_IN_SECONDS));
    $soft_hero_num   = preg_replace('/[^0-9]+/', '', $soft_hero_next['tag']);
    $soft_hero_label = $soft_hero_num !== '' ? sprintf('Until #%s', $soft_hero_num) : 'Until next';
} elseif ($soft_hero_last !== null) {
    $soft_hero_days  = max(0, (int) floor(($soft_hero_today - $soft_hero_last['ts']) / DAY_IN_SECONDS));
    $soft_hero_num   = preg_replace('/[^0-9]+/', '', $soft_hero_last['tag']);
    $soft_hero_label = $soft_hero_num !== '' ? sprintf('Days since #%s', $soft_hero_num) : 'Days since';
} else {
    $soft_hero_days  = '–';
    $soft_hero_label = 'Days';
}
?>
<!-- wp:group {"anchor":"soft-hero","tagName":"section","className":"swinog-soft-hero-wrap","align":"full","layout":{"type":"constrained","wideSize":"1280px"}} -->
<section id="soft-hero" class="wp-block-group alignfull swinog-soft-hero-wrap">

	<!-- wp:group {"className":"swinog-soft-hero","align":"wide","layout":{"type":"default"}} -->
	<div class="wp-block-group alignwide swinog-soft-hero">

		<!-- wp:columns {"className":"swinog-soft-hero__grid","verticalAlignment":"bottom"} -->
		<div class="wp-block-columns swinog-soft-hero__grid are-vertically-aligned-bottom">

			<!-- wp:column {"width":"56%","verticalAlignment":"bottom"} -->
			<div class="wp-block-column is-vertically-aligned-bottom" style="flex-basis:56%">
				<!-- wp:html -->
				<div class="swinog-pill swinog-pill--status">
					<span class="swinog-pill__dot" aria-hidden="true"></span>
					<span><strong>SwiNOG #42</strong> · In planning</span>
				</div>
				<!-- /wp:html -->

				<!-- wp:heading {"level":1,"className":"swinog-display-xl"} -->
				<h1 class="wp-block-heading swinog-display-xl">A community for the people who run Swiss networks.</h1>
				<!-- /wp:heading -->

				<!-- wp:paragraph {"className":"swinog-lead"} -->
				<p class="swinog-lead">An informal forum for the people who design and operate the Swiss Internet. We meet every 3–4 months in Berne and keep the mailing list humming between meetings. Non-political, not a lobby, no membership fees.</p>
				<!-- /wp:paragraph -->

				<!-- wp:buttons {"className":"swinog-cta-row"} -->
				<div class="wp-block-buttons swinog-cta-row">
					<!-- wp:button {"className":"swinog-btn swinog-btn--primary"} -->
					<div class="wp-block-button swinog-btn swinog-btn--primary"><a class="wp-block-button__link wp-element-button" href="https://lists.swinog.ch/postorius/lists/swinog.lists.swinog.ch/">Join the mailing list →</a></div>
					<!-- /wp:button -->
					<!-- wp:button {"className":"swinog-btn swinog-btn--secondary"} -->
					<div class="wp-block-button swinog-btn swinog-btn--secondary"><a class="wp-block-button__link wp-element-button" href="/meetings/swinog41/">Watch SwiNOG #41 talks</a></div>
					<!-- /wp:button -->
				</div>
				<!-- /wp:buttons -->

				<!-- wp:html -->
				<div class="swinog-hero-stats">
					<span><strong>41</strong> meetings since 2000</span>
					<span>Founded <strong>24 Feb 2000</strong></span>
					<span><strong>Always free</strong> to attend</span>
				</div>
				<!-- /wp:html -->
			</div>
			<!-- /wp:column -->

			<!-- wp:column {"width":"40%","verticalAlignment":"bottom"} -->
			<div class="wp-block-column is-vertically-aligned-bottom" style="flex-basis:40%">
				<!-- wp:html -->
				<div class="swinog-soft-event-card swinog-soft-shadow">
					<div class="swinog-soft-event-card__head">
						<div class="swinog-soft-event-card__title">
							<span class="swinog-soft-event-card__dot" aria-hidden="true"></span>
							<span>Next meeting</span>
						</div>
						<span class="swinog-mono swinog-soft-event-card__no">#42</span>
					</div>
					<div class="swinog-soft-event-card__body">
						<div class="swinog-soft-event-card__when">Date to be announced</div>
						<div class="swinog-soft-event-card__where">Berne</div>
						<div class="swinog-soft-event-card__stats">
							<div class="swinog-stat"><div class="swinog-stat__k">Talks at #41</div><div class="swinog-stat__v">14</div></div>
							<div class="swinog-stat"><div class="swinog-stat__k"><?php echo esc_html($soft_hero_label); ?></div><div class="swinog-stat__v"><?php echo esc_html((string) $soft_hero_days); ?><span class="swinog-stat__suf">d</span></div></div>
							<div class="swinog-stat"><div class="swinog-stat__k">CFP for #42</div><div class="swinog-stat__v swinog-stat__v--small">Opening soon</div></div>
							<div class="swinog-stat"><div class="swinog-stat__k">Cadence</div><div class="swinog-stat__v swinog-stat__v--small">~3 / yr</div></div>
						</div>
						<a class="swinog-soft-event-card__cta" href="/cfp/">
							<span>Submit a talk for SwiNOG #42</span><span aria-hidden="true">→</span>
						</a>
					</div>
				</div>
				<!-- /wp:html -->
			</div>
			<!-- /wp:column -->
		</div>
		<!-- /wp:columns -->

		<!-- wp:html -->
		<svg class="swinog-soft-hero__dots" width="360" height="360" viewBox="0 0 360 360" aria-hidden="true" focusable="false">
			<defs>
				<pattern id="swinog-hero-dots" width="14" height="14" patternUnits="userSpaceOnUse">
					<circle cx="2" cy="2" r="1.1" fill="currentColor" />
				</pattern>
			</defs>
			<rect width="360" height="360" fill="url(#swinog-hero-dots)" />
		</svg>
		<!-- /wp:html -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->
