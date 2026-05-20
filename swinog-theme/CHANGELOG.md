# Changelog

All notable changes to the SwiNOG theme are documented here.
The version is read from `Version:` in `style.css`; pushing a bump to `main`
tags `v<version>` and publishes a GitHub release via
`.github/workflows/release-theme.yml`.

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
