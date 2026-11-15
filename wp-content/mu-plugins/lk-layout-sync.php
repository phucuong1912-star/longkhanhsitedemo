<?php
/**
 * Plugin Name: Tiền Hải - Versioned Elementor Layout Sync
 * Description: Apply committed layout-only packages after a branch workflow finishes transferring code.
 */
if (!defined('ABSPATH')) exit;
require_once __DIR__ . '/lk-layout-sync/engine.php';
require_once __DIR__ . '/lk-layout-sync/wordpress-store.php';

function lk_layout_directory() { return WP_CONTENT_DIR . '/lk-layouts'; }
function lk_layout_policy() { return json_decode(file_get_contents(__DIR__ . '/lk-layout-sync/scopes.json'), true, 512, JSON_THROW_ON_ERROR); }
function lk_layout_package() { return json_decode(file_get_contents(lk_layout_directory() . '/manifest.json'), true, 512, JSON_THROW_ON_ERROR); }
function lk_layout_engine() { return new LK_Layout_Engine(new LK_Layout_WordPress_Store(), lk_layout_policy(), lk_layout_directory()); }
function lk_layout_publish_status($status) {
 $path = lk_layout_directory() . '/applied.json';
 $temp = $path . '.tmp-' . wp_generate_password(8, false);
 if (file_put_contents($temp, wp_json_encode($status, JSON_UNESCAPED_SLASHES), LOCK_EX) === false || !rename($temp, $path)) throw new RuntimeException('Cannot publish the layout deployment status.');
}
function lk_layout_apply_ready() {
 if (defined('LK_LAYOUT_EXPORTING') || (defined('WP_CLI') && WP_CLI) || wp_doing_ajax() || wp_doing_cron()) return;
 $directory = lk_layout_directory();
 if (!is_file($directory . '/ready.json') || !is_file($directory . '/manifest.json')) return;
 $ready = null;
 try {
  $ready = json_decode(file_get_contents($directory . '/ready.json'), true, 512, JSON_THROW_ON_ERROR);
  $package = lk_layout_package();
  if (($ready['release'] ?? '') !== $package['release']) return; // Files are still being transferred.
  LK_Layout_Package::validate_ready($ready, $package, lk_layout_policy(), home_url());
  $state = (array) get_option('lk_layout_sync_state', array());
  if (($state['release'] ?? '') === $package['release']) {
   $previous = is_file($directory . '/applied.json') ? json_decode(file_get_contents($directory . '/applied.json'), true) : array();
   if (($previous['commit'] ?? '') === ($ready['commit'] ?? '') && ($previous['status'] ?? '') === 'applied') return;
   // Retry cache invalidation if a prior request committed metadata but failed
   // before publishing its status; also refresh on a code-only deployment.
   lk_layout_engine()->refresh($package);
  } else $state = lk_layout_engine()->apply($package);
  delete_option('lk_layout_sync_error');
  lk_layout_publish_status(array('status' => 'applied', 'release' => $package['release'], 'commit' => $ready['commit'], 'environment' => $ready['environment'], 'documents' => count($package['documents'])));
 } catch (LK_Layout_Busy $busy) {
  // A concurrent warmup/visitor must not mark an in-progress import as failed.
  return;
 } catch (Throwable $error) {
  update_option('lk_layout_sync_error', array('message' => $error->getMessage(), 'release' => $ready['release'] ?? '', 'time' => time()), false);
  // Public status contains no database details, document data, or backup.
  try { lk_layout_publish_status(array_merge(array('status' => 'error', 'release' => $ready['release'] ?? '', 'commit' => $ready['commit'] ?? '', 'environment' => $ready['environment'] ?? '', 'message' => 'Layout sync stopped; see Tools > Layout Sync in WordPress.'), LK_Layout_Package::public_error($error->getMessage()))); } catch (Throwable $ignored) {}
 }
}
add_action('wp_loaded', 'lk_layout_apply_ready', 9999);
add_action('admin_menu', function () {
 add_management_page('Đồng bộ giao diện', 'Layout Sync', 'manage_options', 'lk-layout-sync', function () {
  if (!current_user_can('manage_options')) return;
  $state = (array) get_option('lk_layout_sync_state', array()); $error = (array) get_option('lk_layout_sync_error', array());
  echo '<div class="wrap"><h1>Đồng bộ giao diện Elementor</h1><p>Chỉ chỉnh sửa tại local và triển khai bằng GitHub Actions. Bài viết và dữ liệu kinh doanh không nằm trong gói đồng bộ.</p><p>Release: <code>' . esc_html($state['release'] ?? 'Chưa áp dụng') . '</code></p>';
  if ($error) echo '<div class="notice notice-error"><p>' . esc_html($error['message']) . '</p></div>';
  if (!empty($state['backup'])) {
   echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '"><input type="hidden" name="action" value="lk_layout_rollback"><input type="hidden" name="backup" value="' . esc_attr($state['backup']) . '">';
   wp_nonce_field('lk_layout_rollback'); echo '<p><button class="button">Hoàn tác lần đồng bộ giao diện gần nhất</button></p></form>';
  }
  echo '</div>';
 });
});
add_action('admin_post_lk_layout_rollback', function () {
 if (!current_user_can('manage_options')) wp_die('Không có quyền.');
 check_admin_referer('lk_layout_rollback');
 try { lk_layout_engine()->rollback(sanitize_text_field(wp_unslash($_POST['backup'] ?? ''))); lk_layout_publish_status(array('status' => 'rolled_back')); }
 catch (Throwable $error) { wp_die(esc_html($error->getMessage())); }
 wp_safe_redirect(admin_url('tools.php?page=lk-layout-sync')); exit;
});
if (defined('WP_CLI') && WP_CLI) {
 WP_CLI::add_command('lk-layout plan', function () { $plan = lk_layout_engine()->plan(lk_layout_package()); WP_CLI::log(wp_json_encode(array('documents' => count($plan['ids']), 'metadata_changes' => count($plan['changes'])))); });
 // Applying is driven by the workflow's final release marker and HTTP warmup.
 // CLI exposes only a read-only plan and an explicit recovery command.
 WP_CLI::add_command('lk-layout rollback', function ($args) { lk_layout_engine()->rollback($args[0] ?? ''); WP_CLI::success('Layout metadata restored.'); });
}
