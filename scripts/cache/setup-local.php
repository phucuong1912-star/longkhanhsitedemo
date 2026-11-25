<?php
/** Activate/configure the bundled LiteSpeed Cache only in local WordPress. */
if (PHP_SAPI !== 'cli') { http_response_code(403); exit; }
define('LK_LAYOUT_EXPORTING', true);
define('DISABLE_WP_CRON', true);
require dirname(__DIR__, 2) . '/wp-includes/plugin.php';
add_filter('pre_http_request', function(){ return new WP_Error('local-cache-setup'); }, PHP_INT_MAX);
add_filter('pre_wp_mail', '__return_true', PHP_INT_MAX);
require dirname(__DIR__, 2) . '/wp-load.php';
try {
 if (!in_array(wp_parse_url(home_url(), PHP_URL_HOST), array('localhost', '127.0.0.1', '::1'), true)) throw new RuntimeException('Local WordPress only.');
 if (is_multisite()) throw new RuntimeException('Single-site setup only.');
 require_once ABSPATH . 'wp-admin/includes/plugin.php';
 $plugin = 'litespeed-cache/litespeed-cache.php';
 $problem = validate_plugin_requirements($plugin);
 if (is_wp_error($problem)) throw new RuntimeException($problem->get_error_message());
 global $wpdb;
 $backup = array('active_plugins'=>get_option('active_plugins'), 'cache_options'=>$wpdb->get_results("SELECT option_name,option_value,autoload FROM {$wpdb->options} WHERE option_name LIKE 'litespeed.%'", ARRAY_A));
 $directory = dirname(__DIR__, 2) . '/.codex-tools';
 $stamp = gmdate('Ymd-His') . '-' . wp_generate_password(6, false);
 if (!is_dir($directory) || file_put_contents($directory . '/cache-local-before-' . $stamp . '.json', wp_json_encode($backup, JSON_PRETTY_PRINT)) === false) throw new RuntimeException('Cannot save local backup.');
 foreach (array('wp-config.php', '.htaccess') as $file) {
  if (file_exists(ABSPATH . $file) && !copy(ABSPATH . $file, $directory . '/cache-local-before-' . $stamp . '-' . $file)) throw new RuntimeException('Cannot back up local runtime configuration.');
 }
 $problem = activate_plugin($plugin);
 if (is_wp_error($problem)) throw new RuntimeException($problem->get_error_message());
 do_action('litespeed_save_conf', lk_cache_settings());
 echo 'LiteSpeed Cache ' . LSCWP_V . " activated locally with the versioned conservative profile.\n";
 echo "Public TTL: 300 seconds; authenticated users, forms and query requests bypass cache.\n";
 echo "CSS/JS optimization, guest mode, object cache, ESI and crawler remain disabled.\n";
 echo "Actual cache HIT/MISS must be verified over HTTP on OpenLiteSpeed; CLI cache control is not engine verification.\n";
} catch (Throwable $error) {
 fwrite(STDERR, $error->getMessage() . "\n"); exit(1);
}
