<?php
/** Render the installed listing template; no content or form submissions. */
if (PHP_SAPI !== 'cli') exit(1);
set_exception_handler(function ($error) { fwrite(STDERR, 'FAIL ' . $error->getMessage() . "\n"); exit(1); });
define('LK_LAYOUT_EXPORTING', true);
define('DISABLE_WP_CRON', true);
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/longkhanhsite/';
require dirname(__DIR__) . '/wp-includes/plugin.php';
add_filter('pre_http_request', function () { return new WP_Error('local-listing-assets-test'); }, PHP_INT_MAX);
add_filter('pre_wp_mail', '__return_true', PHP_INT_MAX);
require dirname(__DIR__) . '/wp-load.php';
if (wp_parse_url(home_url(), PHP_URL_HOST) !== 'localhost') throw new RuntimeException('Local WordPress only');
function listing_assets_check($ok, $message) {
 if (!$ok) throw new RuntimeException($message);
 echo "PASS $message\n";
}
$listing = lk_posts()[0] ?? null;
listing_assets_check((bool) $listing, 'Existing public listing is available');
$manifest = hash_file('sha256', WP_CONTENT_DIR . '/lk-layouts/manifest.json');
$template = (int) get_option('lk_template_listing');
$layout = get_post_meta($template, '_elementor_data', true);
$GLOBALS['wp_query'] = new WP_Query(array('post_type' => 'listing', 'p' => $listing->ID));
$_SERVER['REQUEST_URI'] = wp_parse_url(get_permalink($listing), PHP_URL_PATH);
// Reproduce the legacy script present in production, regardless of the local
// dashboard URL setting that controls ListingPro's conditional enqueue.
wp_enqueue_script('datelocale', 'https://sandbox.listingprowp.com/datepicker-locales/datepicker-vi.js', array('jquery-ui'));
ob_start();
include get_stylesheet_directory() . '/single-lk-listing.php';
$html = ob_get_clean();
listing_assets_check(!preg_match('~<script\b[^>]*src=[\'"][^\'"]*sandbox\.listingprowp\.com~i', $html), 'Real listing template has no sandbox parser-blocking script');
listing_assets_check(str_contains($html, '/assets/fonts/quicksand.css'), 'Listing retains same-origin text font');
listing_assets_check(str_contains($html, 'data-save='), 'Save control is rendered');
listing_assets_check(str_contains($html, 'id="lk-review-form"') && str_contains($html, 'name="lk_nonce"'), 'Review form and nonce remain rendered');
listing_assets_check(str_contains($html, 'id="lk-login"'), 'Account dialog remains rendered');
listing_assets_check(str_contains($html, 'lk-location-leaflet-js'), 'Local map library remains available');
listing_assets_check($manifest === hash_file('sha256', WP_CONTENT_DIR . '/lk-layouts/manifest.json') && $layout === get_post_meta($template, '_elementor_data', true), 'Layout package and listing document are unchanged');
// The native dashboard is outside the managed canvas and still owns its datepicker.
$GLOBALS['wp_query'] = new WP_Query(array('p' => -1));
$GLOBALS['post'] = null;
wp_enqueue_script('datelocale');
lk_dequeue_unused_legacy_scripts();
listing_assets_check(wp_script_is('datelocale', 'enqueued'), 'Unmanaged dashboard can still enqueue its date locale');
if (in_array('--fixture', $argv, true)) file_put_contents(dirname(__DIR__) . '/.codex-tools/listing-browser-fixture.json', wp_json_encode(array('url' => get_permalink($listing), 'id' => $listing->ID)));
echo "Listing asset integration passed.\n";
