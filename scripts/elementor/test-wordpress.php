<?php
/** Local-only integration: real WordPress metadata transaction, injected failure, no remote calls. */
if (PHP_SAPI !== 'cli') { http_response_code(403); exit; }
define('LK_LAYOUT_EXPORTING', true);
require dirname(__DIR__, 2) . '/wp-load.php';
if (!in_array(wp_parse_url(home_url(), PHP_URL_HOST), array('localhost', '127.0.0.1', '::1'), true)) { fwrite(STDERR, "This integration test only runs on localhost.\n"); exit(1); }

final class Layout_Failing_WordPress_Store {
 private $store; private $calls = 0; public $backup_id;
 function __construct() { $this->store = new LK_Layout_WordPress_Store(); }
 function __call($method, $args) { return call_user_func_array(array($this->store, $method), $args); }
 function write($id, $key, $value) {
  if (++$this->calls === 2) throw new RuntimeException('Intentional integration test failure');
  $this->store->write($id, $key, $value);
 }
 function remove($id, $key) {
  if (++$this->calls === 2) throw new RuntimeException('Intentional integration test failure');
  $this->store->remove($id, $key);
 }
 function backup($data) { $this->backup_id = $this->store->backup($data); return $this->backup_id; }
}
function layout_db_fingerprint() {
 global $wpdb;
 $result = array();
 foreach (array($wpdb->posts, $wpdb->postmeta, $wpdb->comments, $wpdb->commentmeta, $wpdb->users, $wpdb->usermeta, $wpdb->terms, $wpdb->term_taxonomy, $wpdb->term_relationships) as $table) {
  $result[$table] = hash('sha256', serialize($wpdb->get_results("SELECT * FROM `$table` ORDER BY 1, 2", ARRAY_A)));
 }
 return $result;
}
$store = new Layout_Failing_WordPress_Store(); $policy = lk_layout_policy();
$small_policy = array('documents' => array('header' => $policy['documents']['header']));
$package = array('version' => 1, 'documents' => array('header' => array('type' => 'elementor_library', 'meta' => array('_elementor_data' => array(array('id' => 'layout-transaction-test', 'elType' => 'section', 'settings' => array(), 'elements' => array()))))), 'assets' => array());
$package['release'] = LK_Layout_Package::hash($package);
$before = layout_db_fingerprint(); $state_before = get_option('lk_layout_sync_state', false); $failed = false;
try {
 $plan = lk_layout_engine()->plan(lk_layout_package());
 echo 'Real destination mapping validated: ' . count($plan['ids']) . " documents (read-only plan).\n";
 $engine = new LK_Layout_Engine($store, $small_policy, lk_layout_directory());
 try { $engine->apply($package); throw new RuntimeException('Injected failure did not occur.'); }
 catch (RuntimeException $error) { if ($error->getMessage() !== 'Intentional integration test failure') throw $error; }
 if ($before !== layout_db_fingerprint() || $state_before !== get_option('lk_layout_sync_state', false)) throw new RuntimeException('The database did not return to its original state.');
 $guard_store = new LK_Layout_WordPress_Store();
 $post = get_posts(array('post_type' => 'post', 'post_status' => 'any', 'posts_per_page' => 1, 'fields' => 'ids'));
 if (!$post) throw new RuntimeException('Need an existing local article to verify the protected-type guard.');
 try { $guard_store->write((int) $post[0], '_elementor_data', '[]'); throw new RuntimeException('Protected article guard did not fire.'); }
 catch (RuntimeException $error) { if (strpos($error->getMessage(), 'protected content') === false) throw $error; }
 echo "PASS real InnoDB transaction rollback, unchanged posts/metadata/comments/users/taxonomies and article-write guard.\n";
} catch (Throwable $error) { $failed = true; fwrite(STDERR, $error->getMessage() . "\n"); }
finally {
 // Remove only the backup created by this failed, local test. No content deletion.
 if ($store->backup_id) delete_option($store->backup_id);
}
exit($failed ? 1 : 0);
