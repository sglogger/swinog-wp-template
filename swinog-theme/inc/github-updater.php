<?php
/**
 * Self-hosted theme updates from GitHub releases.
 *
 * Lets WordPress show "update available" for this theme and install it from
 * the ZIP attached to the latest GitHub release — no Git Updater plugin
 * needed. Pairs with .github/workflows/release-theme.yml, which tags
 * v<version> from style.css and uploads swinog-theme.zip on every release.
 *
 * Optional: define SWINOG_GITHUB_TOKEN in wp-config.php to raise the GitHub
 * API rate limit or read updates from a private repo.
 *
 * @package SwiNOG
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

final class Swinog_GitHub_Theme_Updater
{
    private const REPO       = 'sglogger/swinog-wp-template';
    private const ASSET      = 'swinog-theme.zip';
    private const CACHE_KEY  = 'swinog_github_release';
    private const CACHE_TTL  = 6 * HOUR_IN_SECONDS;

    private string $slug;
    private string $version;

    private function __construct()
    {
        $theme         = wp_get_theme(get_template());
        $this->slug    = get_template();
        $this->version = (string) $theme->get('Version');

        add_filter('pre_set_site_transient_update_themes', [$this, 'inject_update']);
        add_filter('themes_api', [$this, 'theme_info'], 10, 3);
        add_filter('upgrader_source_selection', [$this, 'fix_source_dir'], 10, 4);
        add_action('upgrader_process_complete', [$this, 'clear_cache'], 10, 0);
    }

    public static function boot(): void
    {
        // Update checks only run in the admin, via cron, or under WP-CLI.
        if (is_admin() || wp_doing_cron() || (defined('WP_CLI') && WP_CLI)) {
            new self();
        }
    }

    /**
     * Add this theme to the list of available updates when the latest
     * release tag is newer than the installed version.
     *
     * @param object $transient
     * @return object
     */
    public function inject_update($transient)
    {
        if (!is_object($transient)) {
            $transient = new stdClass();
        }

        // Honour the "Check again" button on the Updates screen.
        if (!empty($_GET['force-check'])) {
            $this->clear_cache();
        }

        $release = $this->get_latest_release();
        if ($release === null) {
            return $transient;
        }

        $remote_version = ltrim((string) ($release['tag_name'] ?? ''), 'vV');
        $package        = $this->find_package($release);

        if ($remote_version === '' || $package === '') {
            return $transient;
        }

        if (version_compare($remote_version, $this->version, '>')) {
            $transient->response[$this->slug] = [
                'theme'       => $this->slug,
                'new_version' => $remote_version,
                'url'         => (string) ($release['html_url'] ?? ''),
                'package'     => $package,
            ];
        } else {
            // Record that we checked so WP doesn't re-query wordpress.org.
            $transient->no_update[$this->slug] = [
                'theme'       => $this->slug,
                'new_version' => $this->version,
                'url'         => (string) ($release['html_url'] ?? ''),
                'package'     => '',
            ];
        }

        return $transient;
    }

    /**
     * Populate the "View version X details" modal on the Themes screen.
     *
     * @param mixed  $result
     * @param string $action
     * @param object $args
     * @return mixed
     */
    public function theme_info($result, $action, $args)
    {
        if ($action !== 'theme_information' || empty($args->slug) || $args->slug !== $this->slug) {
            return $result;
        }

        $release = $this->get_latest_release();
        if ($release === null) {
            return $result;
        }

        $theme = wp_get_theme($this->slug);

        $info               = new stdClass();
        $info->name         = $theme->get('Name');
        $info->slug         = $this->slug;
        $info->version      = ltrim((string) ($release['tag_name'] ?? ''), 'vV');
        $info->author       = $theme->get('Author');
        $info->homepage     = $theme->get('ThemeURI');
        $info->download_link = $this->find_package($release);
        $info->sections     = [
            'changelog' => wp_kses_post(wpautop((string) ($release['body'] ?? 'No release notes.'))),
        ];

        return $info;
    }

    /**
     * GitHub release ZIPs may extract to a folder that doesn't match the
     * theme slug. Rename it so WordPress overwrites the right directory.
     *
     * @param string $source
     * @param string $remote_source
     * @param object $upgrader
     * @param array  $hook_extra
     * @return string|WP_Error
     */
    public function fix_source_dir($source, $remote_source, $upgrader, $hook_extra = [])
    {
        if (empty($hook_extra['theme']) || $hook_extra['theme'] !== $this->slug) {
            return $source;
        }

        global $wp_filesystem;

        $desired = trailingslashit($remote_source) . $this->slug;
        if (untrailingslashit($source) === $desired) {
            return $source;
        }

        if ($wp_filesystem->move($source, $desired)) {
            return trailingslashit($desired);
        }

        return new WP_Error(
            'swinog_rename_failed',
            __('Could not rename the downloaded theme folder.', 'swinog')
        );
    }

    public function clear_cache(): void
    {
        delete_site_transient(self::CACHE_KEY);
    }

    /**
     * @return array<string,mixed>|null
     */
    private function get_latest_release(): ?array
    {
        $cached = get_site_transient(self::CACHE_KEY);
        if (is_array($cached)) {
            return $cached;
        }

        $args = [
            'timeout' => 10,
            'headers' => [
                'Accept'     => 'application/vnd.github+json',
                'User-Agent' => 'SwiNOG-Theme-Updater',
            ],
        ];
        if (defined('SWINOG_GITHUB_TOKEN') && SWINOG_GITHUB_TOKEN) {
            $args['headers']['Authorization'] = 'Bearer ' . SWINOG_GITHUB_TOKEN;
        }

        $response = wp_remote_get(
            'https://api.github.com/repos/' . self::REPO . '/releases/latest',
            $args
        );

        if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
            // Cache the miss briefly so a flaky API doesn't hammer every load.
            set_site_transient(self::CACHE_KEY, ['tag_name' => '', 'assets' => []], MINUTE_IN_SECONDS * 15);
            return null;
        }

        $data = json_decode(wp_remote_retrieve_body($response), true);
        if (!is_array($data)) {
            return null;
        }

        set_site_transient(self::CACHE_KEY, $data, self::CACHE_TTL);

        return $data;
    }

    /**
     * Resolve the download URL: prefer the uploaded swinog-theme.zip asset
     * (whose top folder is correctly named), fall back to the source zipball.
     *
     * @param array<string,mixed> $release
     */
    private function find_package(array $release): string
    {
        if (!empty($release['assets']) && is_array($release['assets'])) {
            foreach ($release['assets'] as $asset) {
                if (($asset['name'] ?? '') === self::ASSET && !empty($asset['browser_download_url'])) {
                    return (string) $asset['browser_download_url'];
                }
            }
        }

        return '';
    }
}

Swinog_GitHub_Theme_Updater::boot();
