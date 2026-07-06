# Changelog

All notable changes to the SwiNOG theme are documented here.
The version is read from `Version:` in `style.css`; pushing a bump to `main`
tags `v<version>` and publishes a GitHub release via
`.github/workflows/release-theme.yml`.

## 0.8.7

- Venue map: geocoding no longer fails on multi-line postal addresses. The
  Nominatim lookup now tries fallback queries in order — full address on one
  line, then street + town (last two lines), then the venue name (first line) —
  so addresses like "Enter Technikwelt Solothurn / Stiftung Enter /
  Gewerbestrasse 4 / 4552 Derendingen" resolve again. The geocode query is also
  properly URL-encoded now. Re-save the event page once to regenerate a missing
  map PNG.

## 0.8.6

- Header (SoftBar): the sticky top bar is now gently rounded (`16px` corners)
  instead of a full pill — buttons and pills elsewhere keep the pill radius.

## 0.8.5

- Footer: reduced the padding inside the footer box (`40px 48px` → `24px 32px`)
  so the content is more compact on all sides.

## 0.8.4

- Mobile hamburger menu: submenus now stack **below** their parent (not beside
  it) as a single column, with child rows indented and muted so the hierarchy
  stays readable.
- Homepage soft-feature cards: removed the doubled-up gap between the icon and
  the kicker (e.g. "03 / Archive") — the kicker now sits right under the icon.
- Footer: added a right-aligned **"Footer · copyright row (right)"** widget area,
  so the copyright bar can carry a second column (legal/imprint/social links).
- Homepage: roughly **halved the vertical space between sections** (section wrap
  paddings + the inter-section block-gap reduced).

## 0.8.3

- Mobile hamburger menu: every item (top level + submenus) is now a full-width
  block aligned to the same left edge with row separators, so the menu reads as
  one aligned column instead of indented/ragged rows.

## 0.8.2

- "Recent talks" now uses `align:full` like the other homepage sections (white
  card centred at 1280), so its gutter matches them at every width — fixes the
  larger mobile inset.
- Single news post: tighter gap between the byline and the featured image / body
  (14px), and the article card's horizontal padding is reduced on mobile so the
  text isn't squeezed into the middle of the box.

## 0.8.1

- **Single presentation page** (`single-stgl_presentation.html`): drops the
  WordPress-author avatar/byline for a meta-driven `swinog/presentation-byline`
  block — presenter name · company plus red **Watch the video** / **View slides**
  buttons when those exist.
- **Breadcrumb** on presentations links the meeting term to its `/meetings/…`
  Page (`swinog_meeting_url_for_term`) rather than the taxonomy archive.
- **Search** now matches the presenter name/company meta (`posts_search`
  filter), and the search results layout is centred in the content container.
- **Archive template** (`archive.html`) for category/tag/date/author archives,
  styled like `/news/` (hero + news-card grid) instead of the bare fallback.
- **No-orphan-gap**: when the page title is hidden (or on the news index /
  archives) the first block sits tight under the breadcrumb (`swinog-no-page-title`).
- **Single news post** shows its featured image as a rounded hero when set; the
  featured news card hides the image box when there is no image.
- New patterns: **`swinog/link-library`** (external-link directory cards),
  **`swinog/announcement`** (highlight box + red CTA), **`swinog/press-list`**
  (PDF media-coverage list).
- "More from SwiNOG" cards: whole card clickable + a **Read more →** cue on
  truncated excerpts.
- **Live next-meeting countdown**: the SoftHero "days" stat is computed at render
  time (`render_block` filter), so it stays correct whether the hero is referenced
  via `wp:pattern` or inserted into a page.
- **Contact forms** restyled to the palette — both Dialog Contact Form (`.dcf-*`)
  and Contact Form 7 (`.wpcf7-*`): rounded inputs, accent focus ring, red submit.
- News card dates now render `d.m.Y`.
- **Mobile polish**: Recent-talks grid collapses to one column and aligns its
  gutter with the other sections; footer logo capped; tighter news byline→body
  gap; reduced front-page hero top spacing; and a **hamburger menu** (the header
  CTAs are hidden on mobile and the classic menu drops down from a toggle).

## 0.8.0

- Header navigation now uses classic **Appearance → Menus** menus: register a
  `primary` nav location, render the header via `wp_nav_menu()` mapped onto the
  block-navigation classes, and add hover/focus **dropdowns** for submenus.
  Customizer "Primary navigation" lists classic menus.
- Homepage follows the selected page: `front-page.html` renders
  `core/post-content`, and the SwiNOG landing is now a reusable
  `swinog/homepage` pattern (so picking a different homepage page works).
- "Recent talks" (`swinog/agenda`) editor preview respects `align` via
  `useBlockProps()` (was constrained to content width in the editor).
- Meetings overview rows lead with the full **page title** instead of a parsed
  "#NN", with aligned columns and a single-line `talks · attendees · sponsors`
  count.
- New **Attendees** event-detail field (`swinog_event_attendees`), shown in the
  hero quick-facts and inline in the meetings overview when set.
- "Hide the page title (H1)" now only hides the standalone `core/post-title`;
  the event hero title always renders.
- Hide the sponsor tier label when no level is set (empty `.stgl-sponsor-tier`).
- Cache-bust `tokens.css` / editor JS by file mtime when `WP_DEBUG` is on, so
  style changes show without a hard refresh (theme version in production).

## 0.7.0

- Add self-hosted theme updates from GitHub releases (`inc/github-updater.php`).
  WordPress now offers "update available" and installs the release ZIP directly
  — no Git Updater or other plugin required. Optional `SWINOG_GITHUB_TOKEN`
  raises the GitHub API rate limit / supports a private repo.
- Add `.github/workflows/release-theme.yml`: on a `style.css` version bump to
  `main`, auto-tag `v<version>`, build `swinog-theme.zip`, and publish a release.
- Add `Update URI` header to `style.css`.

## 0.6.0

- Baseline block theme: templates, parts, patterns, `theme.json` tokens,
  wp-swinog-events plugin integration.
