<?php
/**
 * Plugin Name: Tiền Hải - Conservative LiteSpeed Cache policy
 * Description: Versioned cache settings and exclusion/purge rules for account and review flows.
 */
if (!defined('ABSPATH')) exit;

function lk_cache_settings() {
 static $settings;
 if ($settings === null) {
  $profile = json_decode(file_get_contents(__DIR__ . '/lk-cache-policy/settings.json'), true);
  if (!is_array($profile) || ($profile['version'] ?? null) !== 1 || !is_array($profile['settings'] ?? null)) {
   // Fail closed for caching without taking the public WordPress site down.
   return $settings = array('cache' => false);
  }
  $settings = $profile['settings'];
 }
 return $settings;
}

// MU plugins run before regular plugins. These documented overrides carry the
// policy across environments without copying local database options or IDs.
if (!defined('LITESPEED_CONF')) define('LITESPEED_CONF', true);
foreach (lk_cache_settings() as $key => $value) {
 $name = 'LITESPEED_CONF__' . strtoupper(str_replace('-', '__', $key));
 if (!defined($name)) define($name, $value);
}

/** Return a reason to bypass full-page cache, using this environment's mappings. */
function lk_cache_bypass_reason() {
 if (!in_array(strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET'), array('GET', 'HEAD'), true)) return 'request method';
 if (!empty($_SERVER['QUERY_STRING']) || !empty($_GET)) return 'query parameters';
 if (is_user_logged_in()) return 'authenticated user';
 if (is_admin() || wp_doing_ajax() || (defined('REST_REQUEST') && REST_REQUEST)) return 'WordPress endpoint';
 if (!empty($_SERVER['HTTP_AUTHORIZATION']) || !empty($_SERVER['REDIRECT_HTTP_AUTHORIZATION']) || isset($_SERVER['PHP_AUTH_USER'])) return 'authorization';
 foreach (array_keys($_COOKIE) as $name) {
  if (preg_match('/^(?:wordpress_logged_in_|wordpress_sec_|wordpress_[a-f0-9]{32}$|wp-postpass_|comment_author_|lk_reset_|PHPSESSID$)/', $name)) return 'session cookie';
 }
 if (function_exists('lk_auth_mode') && lk_auth_mode()) return 'account form';
 if (is_preview() || is_search() || is_404() || is_feed()) return 'non-public response';
 $map = get_option('lk_page_map', array());
 foreach (array('new-review', 'add-place', 'profile') as $key) {
  if (!empty($map[$key]) && is_page((int) $map[$key])) return 'mapped form page';
 }
 $options = get_option('listingpro_options', array());
 $dashboard = $options['listing-author'] ?? '';
 if (is_string($dashboard) && $dashboard !== '') {
  $path = wp_parse_url($dashboard, PHP_URL_PATH);
  $current = wp_parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
  if ($path && $path !== '/' && untrailingslashit($path) === untrailingslashit((string) $current)) return 'account dashboard';
 }
 if (is_singular(array('post', 'listing'))) {
  $post = get_queried_object();
  return $post instanceof WP_Post && $post->post_status === 'publish' && $post->post_password === '' ? '' : 'protected content';
 }
 if (is_front_page()) return '';
 if (is_page()) {
  foreach (array('home', 'blog', 'about', 'privacy', 'termsofuse') as $key) {
   if (!empty($map[$key]) && is_page((int) $map[$key])) return '';
  }
 }
 if (is_category() || is_tag() || is_tax(array('listing-category', 'location', 'features', 'list-tags'))) return '';
 return 'page outside initial public cache scope';
}

function lk_cache_guard() {
 $reason = lk_cache_bypass_reason();
 if ($reason === '') return;
 if (!defined('DONOTCACHEPAGE')) define('DONOTCACHEPAGE', true);
 do_action('litespeed_control_set_nocache', 'Tiền Hải: ' . $reason);
 if (!headers_sent()) nocache_headers();
}
add_action('init', function() {
 // Runs early for POST/admin/AJAX/OAuth before redirects or form handlers exit.
 if (!in_array(strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET'), array('GET', 'HEAD'), true) || !empty($_GET) || is_user_logged_in() || is_admin()) lk_cache_guard();
}, 20);
add_action('template_redirect', 'lk_cache_guard', 1);
add_action('litespeed_control_finalize', 'lk_cache_guard', PHP_INT_MAX);

function lk_cache_purge_collections() {
 $map = get_option('lk_page_map', array());
 foreach (array('home', 'explore') as $key) if (!empty($map[$key])) do_action('litespeed_purge_post', (int) $map[$key]);
}
function lk_cache_review_changed($comment_id) {
 $comment = get_comment($comment_id);
 if (!$comment || $comment->comment_type !== 'lk_review') return;
 do_action('litespeed_purge_post', (int) $comment->comment_post_ID);
 lk_cache_purge_collections();
}
add_action('wp_insert_comment', 'lk_cache_review_changed', 100);
add_action('edit_comment', 'lk_cache_review_changed', 100);
add_action('delete_comment', 'lk_cache_review_changed', 100);
add_action('transition_comment_status', function($new, $old, $comment) {
 if ($new !== $old) lk_cache_review_changed($comment->comment_ID);
}, 100, 3);
foreach (array('added_comment_meta', 'updated_comment_meta', 'deleted_comment_meta') as $hook) {
 add_action($hook, function($meta_id, $comment_id, $key) {
  if (in_array($key, array('lk_rating', 'lk_criteria', 'lk_review_title', 'lk_anonymous', 'lk_photo_ids'), true)) lk_cache_review_changed($comment_id);
 }, 100, 3);
}
add_action('save_post_listing', 'lk_cache_purge_collections', 100);
