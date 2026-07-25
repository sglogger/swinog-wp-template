# SwiNOG theme

A WordPress **block theme** (full-site editing) for the SwiNOG community —
calm, modern, content-led: rounded cards, generous whitespace, IBM Plex Sans/Mono
+ Newsreader, and a single warm-red accent (`#d83a2c`). No bundler, no page
builder; composition is `theme.json` tokens + block patterns + HTML templates.

- **Requires:** WordPress 6.5+, PHP 8.0+
- **Companion plugin (recommended):** [`wp-swinog-events`](../wp-swinog-events/)
  **≥ 1.0.10** — owns the events/sponsors data model, including the agenda entry
  types. The theme integrates with it and falls back to standalone
  `meeting`/`talk` CPTs when it's absent.

## Updates

The theme self-updates from this repo's **GitHub Releases** — no Git Updater or
other plugin needed (`inc/github-updater.php`). WordPress shows "update available"
under Dashboard → Updates and installs the release ZIP directly.

- Releases are cut automatically: pushing a `Version:` bump in `style.css` to
  `main` triggers `.github/workflows/release-theme.yml`, which tags `v<version>`,
  builds `swinog-theme.zip`, and publishes the release.
- Optional: define `SWINOG_GITHUB_TOKEN` in `wp-config.php` to raise the GitHub
  API rate limit / support a private repo.

## Local development

A three-service `docker-compose` (db + WordPress + phpMyAdmin) lives at the repo
root. The theme is bind-mounted into the container.

```bash
docker compose up -d
docker compose exec wordpress wp <cmd> --allow-root   # if WP-CLI is installed
```

While developing, `tokens.css` / editor JS are cache-busted by file mtime when
`WP_DEBUG` is on, and setting `define('WP_DEVELOPMENT_MODE','theme')` disables the
pattern/template cache so new patterns and templates appear without a version bump.

## Structure

```
theme.json                      design tokens (palette, type scale, spacing, radii, fonts)
style.css                       theme header (Version drives releases) + Update URI
functions.php                   theme supports, asset enqueue, nav location, CPT fallback
assets/css/tokens.css           everything theme.json can't express (gradients, shadows, plugin restyles)
assets/js/                      editor-only helpers
templates/*.html                page/post/archive/search compositions
parts/header.html, footer.html  SoftBar header + SoftFoot footer
patterns/*.php                  section patterns (registered as `swinog/*`)
blocks/*/                       server-rendered blocks (block.json + render.php)
inc/                            customizer, page options, blocks, plugin integration, updater
```

### Templates

`front-page` (renders the selected homepage Page's content), `home` (news index),
`single`, `single-stgl_presentation`, `archive`, `search`, `page`, `page-event`,
`page-about`, `page-meetings-archive`, `page-no-title`, `taxonomy-stgl_presentation_cat`,
`404`, `index`.

### Patterns (category "SwiNOG · Sections")

Homepage/landing: `homepage`, `soft-hero`, `soft-features`, `soft-agenda`,
`soft-list-cta`, `soft-sponsors`. News: `news-hero`, `news-list`, `news-related`,
`news-single-hero`, `news-subscribe`. Events: `event-hero`, `event-info-strip`,
`event-program`, `event-speakers`, `event-sponsor-tiers`, `event-venue`. Archive:
`archive-hero`, `archive-filters`, `archive-timeline`, `archive-cta`. Charter /
About / Sponsor families. Utility: **`link-library`** (link directory),
**`announcement`** (highlight box + red CTA), **`press-list`** (PDF clippings).
Plugin wrappers: `plugin-agenda`, `plugin-presentations`, `plugin-recent-talks`,
`plugin-sponsors`.

### Server-rendered blocks

- `swinog/agenda` — "Recent talks" grid from the events plugin (event-tag filter).
- `swinog/breadcrumbs` — context-aware trail; per-page hideable.
- `swinog/event-hero`, `event-quickfacts`, `event-title`, `event-meta-line`,
  `event-pill` — event-page hero pieces, from `swinog_event_*` page meta.
- `swinog/presentation-byline` — presenter + video/slides buttons on a single talk.
- `swinog/venue`, `venue-map` — cached OSM venue map.
- `swinog/header-ctas`, `footer-widgets`, `footer-copyright` (in `inc/blocks.php`).

## Plugin integration (`wp-swinog-events`)

- CPTs `stgl_presentation` (talks) + `stgl_sponsor`, taxonomy
  `stgl_presentation_cat` (one term per meeting, e.g. `swinog-41`).
- The plugin's `.stgl-*` output is restyled to the SwiNOG palette in `tokens.css`.
- The theme overrides `[swinog_list_all_events]` / `[stgl_childpages]`,
  `[swinog_list_presentations]`, `[swinog_list_agenda]` with section-styled
  renderers, and extends front-end **search** to match presenter name/company meta.
- The agenda's type label (Talk / Keynote / Break / Transportation / Social /
  Other) is **owned by the plugin**, not the theme: `swinog_resolve_program_type()`
  in `inc/swinog-events-integration.php` calls
  `Stgl\SwinogEvents\Installer::resolve_presentation_type( $post_id )` (plugin
  **≥ 1.0.10**), which reads the per-entry `stgl_presenter_type` meta and the
  editable slug→label table in the `stgl_swinog_presentation_types` option
  (*Presentations → Settings*). An entry without an explicit type resolves to
  the default, `Talk`. Never rebuild that table in the theme — it's editable in
  the backend and a copy would drift. Without the plugin (or on an older
  version) the label column renders empty instead of a wrong "Talk".
  The slug also lands on the markup as `swinog-program__row--<slug>` and
  `type-<slug>`, which is what the row tinting in `tokens.css` keys off.
- Event landing pages are regular Pages carrying `swinog_event_*` meta (date,
  location, tag, pill, fee, talks, attendees, format, recording URL).

## Editor options

- **Customizer → SwiNOG · Header**: pick the classic menu for the header, two CTA
  buttons (label/URL/style), branding toggles. **SwiNOG · Footer**: column count.
- **Page sidebar → SwiNOG · Event details**: date, venue, address, tag, pill, fee,
  talks, **attendees**, format, recording URL.
- **Page sidebar**: hide page title (H1) / hide breadcrumbs per page.

## Conventions

- Header navigation uses classic **Appearance → Menus** menus (assigned to the
  `primary` location), rendered via `wp_nav_menu()` mapped onto block-navigation
  classes, with hover/focus dropdowns and a mobile hamburger.
- Newsreader (serif) is used **only** inside the single news-post body.
- The soft elevation shadow is reserved for the next-meeting / quick-facts card;
  every other card uses `1px solid var(--wp--preset--color--rule)`.
- Fonts are self-hosted via `theme.json` `fontFace` (no Google Fonts at runtime).

See [`CHANGELOG.md`](CHANGELOG.md) for release notes.
