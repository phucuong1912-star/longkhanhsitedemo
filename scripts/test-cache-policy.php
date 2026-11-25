<?php
/** Local integration against the installed LiteSpeed Cache API; fixtures roll back. */
if (PHP_SAPI !== 'cli') exit(1);
$cases = array('guest-home','guest-listing','guest-post','ads-cookie','logged-in','auth-cookie','reset-cookie','comment-cookie','session-cookie','login','saved','following','tracking-query','review-page','add-place','dashboard','post-request','rest','protected-listing','review-submission');
$case = isset($argv[1]) ? $argv[1] : '';
if ($case === '') {
 foreach ($cases as $item) {
  $pipes = array();
  $process = proc_open(array(PHP_BINARY, __FILE__, $item), array(0=>array('pipe','r'),1=>STDOUT,2=>STDERR), $pipes, dirname(__DIR__));
  if (!is_resource($process) || proc_close($process) !== 0) exit(1);
 }
 echo count($cases) . "/" . count($cases) . " cache policy integration cases passed.\n"; exit;
}
if (!in_array($case, $cases, true)) exit(2);
set_exception_handler(function($error){fwrite(STDERR, 'FAIL ' . $error->getMessage() . "\n"); exit(1);});
define('LK_LAYOUT_EXPORTING', true); define('DISABLE_WP_CRON', true);
$_SERVER['REQUEST_METHOD'] = $case === 'post-request' ? 'POST' : 'GET';
$_SERVER['REQUEST_URI'] = '/longkhanhsite/'; $_SERVER['QUERY_STRING'] = '';
$_GET = array(); $_COOKIE = array();
foreach (array('login'=>'lk_account','saved'=>'lk_saved','following'=>'lk_feed','tracking-query'=>'utm_source') as $name=>$key) {
 if ($case === $name) { $_GET[$key] = $name === 'login' ? 'login' : 'test'; $_SERVER['QUERY_STRING'] = $key . '=' . $_GET[$key]; }
}
if ($case === 'rest') define('REST_REQUEST', true);
foreach (array('auth-cookie'=>'wordpress_logged_in_owned','reset-cookie'=>'lk_reset_owned','comment-cookie'=>'comment_author_owned','session-cookie'=>'PHPSESSID','ads-cookie'=>'lk_ads_visitor') as $name=>$cookie) if ($case === $name) $_COOKIE[$cookie] = 'owned-test-value';
require dirname(__DIR__) . '/wp-includes/plugin.php';
add_filter('pre_http_request', function(){return new WP_Error('local-cache-test');}, PHP_INT_MAX);
add_filter('pre_wp_mail', '__return_true', PHP_INT_MAX);
require dirname(__DIR__) . '/wp-load.php';
if (wp_parse_url(home_url(), PHP_URL_HOST) !== 'localhost') throw new RuntimeException('Local WordPress only');
function cache_policy_check($ok, $message) { if (!$ok) throw new RuntimeException($message); }
cache_policy_check(defined('LSCWP_V') && LSCWP_V === '7.9.4', 'Expected bundled LiteSpeed Cache version');
foreach (lk_cache_settings() as $key=>$value) cache_policy_check(apply_filters('litespeed_conf', $key) === $value, 'Effective cache setting: ' . $key);
global $wpdb;
$tables = array($wpdb->posts,$wpdb->postmeta,$wpdb->comments,$wpdb->commentmeta,$wpdb->users,$wpdb->usermeta);
foreach ($tables as $table) cache_policy_check($wpdb->get_var($wpdb->prepare('SELECT ENGINE FROM information_schema.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=%s', $table)) === 'InnoDB', 'Transactional fixtures required');
$fingerprint = function()use($wpdb,$tables){$out=array();foreach($tables as $table)$out[$table]=hash('sha256',serialize($wpdb->get_results("SELECT * FROM `$table` ORDER BY 1,2", ARRAY_A)));return $out;};
$before = $fingerprint(); $manifest = hash_file('sha256', WP_CONTENT_DIR . '/lk-layouts/manifest.json');
$wpdb->query('START TRANSACTION');
try {
 $map = get_option('lk_page_map', array());
 $query = array('page_id'=>(int) get_option('page_on_front'));
 $listing_ids = get_posts(array('post_type'=>'listing','post_status'=>'publish','posts_per_page'=>1,'fields'=>'ids','has_password'=>false));
 if (in_array($case, array('guest-listing','ads-cookie','review-submission'), true)) $query = array('post_type'=>'listing','p'=>$listing_ids[0]);
 if ($case === 'guest-post') { $ids=get_posts(array('post_type'=>'post','post_status'=>'publish','posts_per_page'=>1,'fields'=>'ids'));$query=array('p'=>$ids[0]); }
 if ($case === 'review-page') $query = array('page_id'=>$map['new-review']);
 if ($case === 'add-place') $query = array('page_id'=>$map['add-place']);
 if ($case === 'protected-listing') {
  $id=wp_insert_post(array('post_type'=>'listing','post_status'=>'publish','post_title'=>'Owned cache password test','post_password'=>'owned-password'));
  $query=array('post_type'=>'listing','p'=>$id);
 }
 $GLOBALS['wp_query'] = new WP_Query($query); $GLOBALS['post'] = get_post(get_queried_object_id());
 if ($_SERVER['QUERY_STRING'] === '') $_SERVER['REQUEST_URI'] = wp_parse_url(get_permalink(), PHP_URL_PATH);
 if ($case === 'dashboard') { $options=get_option('listingpro_options',array());$options['listing-author']=home_url('/owned-cache-dashboard/');add_filter('pre_option_listingpro_options',function()use($options){return $options;});$_SERVER['REQUEST_URI']='/longkhanhsite/owned-cache-dashboard/'; }
 if (in_array($case, array('logged-in','review-submission'), true)) {
  $user=wp_insert_user(array('user_login'=>'lk_cache_test_'.wp_generate_password(12,false),'user_pass'=>wp_generate_password(30),'role'=>'subscriber','user_email'=>'owned-cache@example.invalid'));
  cache_policy_check(!is_wp_error($user), 'Create owned subscriber');wp_set_current_user($user);
 }
 LiteSpeed\Control::cls()->set_cacheable('Owned policy integration request');
 lk_cache_guard();
 $public = in_array($case, array('guest-home','guest-listing','guest-post','ads-cookie'), true);
 $header = LiteSpeed\Control::cls()->output();
 cache_policy_check($public ? str_contains($header, 'public,max-age=300') : str_contains($header, ': no-cache'), 'Real LiteSpeed control header for ' . $case . ': ' . $header);
 if ($case === 'review-submission') {
  $purged=array();add_action('litespeed_purge_post',function($id)use(&$purged){$purged[]=(int)$id;},1);
  $redirect=function($url){throw new RuntimeException('owned-review-redirect:'.$url);};add_filter('wp_redirect',$redirect,999);
  $_POST=array('lk_nonce'=>wp_create_nonce('lk_review'),'place_id'=>$listing_ids[0],'criteria'=>array_fill_keys(array_keys(lk_criteria_labels()),4),'review'=>'Owned cache integration review','review_title'=>'Owned cache title');$_FILES=array();
  $redirected=false;try{lk_handle_review();}catch(RuntimeException $e){$redirected=str_starts_with($e->getMessage(),'owned-review-redirect:');if(!$redirected)throw $e;}
  cache_policy_check($redirected, 'Real review form redirects normally');remove_filter('wp_redirect',$redirect,999);
  $comments=get_comments(array('post_id'=>$listing_ids[0],'user_id'=>$user,'type'=>'lk_review'));
  cache_policy_check(count($comments)===1 && (string)$comments[0]->comment_approved==='1', 'Actual review is published once');
  foreach (array($listing_ids[0],$map['home'],$map['explore']) as $id) cache_policy_check(in_array((int)$id,$purged,true), 'Review purges the listing and public collection pages');
 }
} finally { $wpdb->query('ROLLBACK'); wp_cache_flush(); wp_set_current_user(0); }
cache_policy_check($before === $fingerprint() && $manifest === hash_file('sha256', WP_CONTENT_DIR . '/lk-layouts/manifest.json'), 'Existing users, reviews, content, metadata and layouts preserved');
echo 'PASS ' . $case . " (real LiteSpeed API, fixtures rolled back)\n";
