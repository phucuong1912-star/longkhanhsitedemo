<?php
/** Render the real WordPress style queue without changing content or layouts. */
if (PHP_SAPI !== 'cli') exit(1);
set_exception_handler(function ($error) { fwrite(STDERR, 'FAIL ' . $error->getMessage() . "\n"); exit(1); });
define('LK_LAYOUT_EXPORTING', true);
define('DISABLE_WP_CRON', true);
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/longkhanhsite/';
require dirname(__DIR__) . '/wp-includes/plugin.php';
add_filter('pre_http_request', function () { return new WP_Error('local-font-test'); }, PHP_INT_MAX);
add_filter('pre_wp_mail', '__return_true', PHP_INT_MAX);
require dirname(__DIR__) . '/wp-load.php';
if (wp_parse_url(home_url(), PHP_URL_HOST) !== 'localhost') throw new RuntimeException('Local WordPress only');
function font_check($ok, $message) {
 if (!$ok) throw new RuntimeException($message);
 echo "PASS $message\n";
}
$GLOBALS['wp_query'] = new WP_Query(array('page_id' => (int) get_option('page_on_front')));
$GLOBALS['post'] = get_post(get_queried_object_id());
font_check(lk_use_local_site_fonts(), 'Managed homepage uses local fonts');
ob_start(); wp_head(); $head = ob_get_clean();
font_check(str_contains($head, '/assets/fonts/quicksand.css'), 'Real homepage head includes bundled font CSS');
font_check(!preg_match('~<link[^>]+(?:fonts\.googleapis\.com|seo-font-compat\.css)~i', $head), 'Real homepage head has no remote text font styles');
font_check(str_contains($head, 'font-awesome'), 'Icon stylesheet remains available');
wp_enqueue_style('elementor-gf-roboto', 'https://fonts.googleapis.com/css?family=Roboto');
ob_start(); wp_print_styles('elementor-gf-roboto'); $late = ob_get_clean();
font_check(!str_contains($late, 'fonts.googleapis.com'), 'Late Elementor font stylesheet is suppressed');
font_check(apply_filters('elementor/frontend/print_google_fonts', true) === false, 'Elementor font discovery is disabled on managed pages');
$manifest = json_decode(file_get_contents(get_stylesheet_directory() . '/assets/fonts/SOURCES.json'), true);
foreach ($manifest['files'] as $asset) {
 $path = get_stylesheet_directory() . '/assets/fonts/' . $asset['file'];
 font_check(hash_file('sha256', $path) === $asset['sha256'], 'Bundled asset checksum ' . $asset['file']);
}
$GLOBALS['wp_query'] = new WP_Query(array('p' => -1));
$GLOBALS['post'] = null;
font_check(!lk_use_local_site_fonts(), 'Unmanaged pages retain existing font handling');
font_check(apply_filters('elementor/frontend/print_google_fonts', true) === true, 'Unmanaged Elementor font discovery is unchanged');
font_check(str_contains(apply_filters('style_loader_tag', '<link href="https://fonts.googleapis.com/css?family=Roboto">', 'elementor-gf-roboto', 'https://fonts.googleapis.com/css?family=Roboto'), 'fonts.googleapis.com'), 'Unmanaged font tags remain available');
$GLOBALS['wp_query'] = new WP_Query(array('page_id' => (int) get_option('page_on_front')));
define('WP_ADMIN', true);
font_check(!lk_use_local_site_fonts(), 'Admin requests retain existing font handling');
echo "Local font integration passed.\n";
