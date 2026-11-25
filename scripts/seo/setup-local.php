<?php
/** Install the already bundled Yoast plugin in LOCAL WordPress only. */
if (PHP_SAPI !== 'cli') { http_response_code(403); exit; }
define('LK_LAYOUT_EXPORTING', true);
define('DISABLE_WP_CRON', true);
require dirname(__DIR__, 2) . '/wp-includes/plugin.php';
add_filter('pre_http_request', function(){ return new WP_Error('local-seo-setup', 'External HTTP is disabled during local SEO setup.'); }, PHP_INT_MAX);
add_filter('pre_wp_mail', '__return_true', PHP_INT_MAX);
require dirname(__DIR__, 2) . '/wp-load.php';

try {
 if (!in_array(wp_parse_url(home_url(), PHP_URL_HOST), array('localhost', '127.0.0.1', '::1'), true)) throw new RuntimeException('This setup is local-only. Use the documented master -> prod process for deployment.');
 if (is_multisite()) throw new RuntimeException('This setup supports single-site WordPress only.');
 if (!function_exists('lk_seo_brand_name')) throw new RuntimeException('Activate the directory plugin first.');
 require_once ABSPATH . 'wp-admin/includes/plugin.php';
 $yoast = 'wordpress-seo/wp-seo.php';
 $previous = 'seo-repair-kit/seo-repair-kit.php';
 if (!file_exists(WP_PLUGIN_DIR . '/' . $yoast)) throw new RuntimeException('The bundled Yoast plugin is missing.');
 $problem = validate_plugin_requirements($yoast);
 if (is_wp_error($problem)) throw new RuntimeException($problem->get_error_message());

 // Back up only the plugin list and the two options this setup manages.
 // SEO Repair Kit's settings and all content stay in their existing tables.
 $backup = array('active_plugins'=>get_option('active_plugins'), 'wpseo_titles'=>get_option('wpseo_titles'), 'wpseo'=>get_option('wpseo'));
 $backup_path = dirname(__DIR__, 2) . '/.codex-tools/seo-local-before-' . gmdate('Ymd-His') . '-' . wp_generate_password(6, false) . '.json';
 if (!is_dir(dirname($backup_path)) || file_put_contents($backup_path, wp_json_encode($backup, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) === false) throw new RuntimeException('Cannot save the local configuration backup.');
 $was_active = is_plugin_active($previous);
 if ($was_active) deactivate_plugins($previous);
 $problem = activate_plugin($yoast);
 if (is_wp_error($problem)) {
  if ($was_active) activate_plugin($previous);
  throw new RuntimeException($problem->get_error_message());
 }
 WPSEO_Options::set('title-post', '%%title%% %%page%% | ' . lk_seo_brand_name());
 WPSEO_Options::set('website_name', lk_seo_brand_name());
 echo "Yoast activated locally; SEO Repair Kit deactivated with its data retained.\n";
 echo 'Post SEO template: ' . WPSEO_Options::get('title-post') . "\n";
 echo "No post content, titles or slugs were rewritten; plugin activation may rebuild caches.\n";
} catch (Throwable $error) {
 fwrite(STDERR, $error->getMessage() . "\n"); exit(1);
}
