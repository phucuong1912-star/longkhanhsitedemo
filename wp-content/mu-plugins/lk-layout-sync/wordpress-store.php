<?php
final class LK_Layout_WordPress_Store {
 private $lock_name; private $touched = array(); private $resolved = array();
 function home() { return rtrim(home_url(), '/'); }
 function site() { return rtrim(site_url(), '/'); }
 function resolve($key, $scope) {
  if (!in_array($scope['type'], array('page', 'elementor_library'), true)) throw new RuntimeException('Protected content type refused.');
  $value = get_option($scope['option']);
  $id = (int) (isset($scope['key']) ? ($value[$scope['key']] ?? 0) : $value);
  if (!$id || get_post_type($id) !== $scope['type'] || get_post_status($id) === 'trash' || (!empty($scope['managed']) && !get_post_meta($id, '_lk_managed', true))) throw new RuntimeException('Missing or unexpected layout document: ' . $key);
  $this->resolved[$id] = true; return $id;
 }
 function state() { return (array) get_option('lk_layout_sync_state', array()); }
 function save_state($state) {
  update_option('lk_layout_sync_state', $state, false);
  if (LK_Layout_Package::hash($this->state()) !== LK_Layout_Package::hash($state)) throw new RuntimeException('Cannot save the layout baseline.');
 }
 function read($id, $key) { return array('exists' => metadata_exists('post', $id, $key), 'value' => get_post_meta($id, $key, true)); }
 private function guard($id, $key) {
  if (empty($this->resolved[$id]) || !in_array(get_post_type($id), array('page', 'elementor_library'), true) || !in_array($key, LK_Layout_Package::META, true)) throw new RuntimeException('Attempt to modify protected content refused.');
  $this->touched[$id] = $id;
 }
 function write($id, $key, $value) {
  $this->guard($id, $key);
  update_post_meta($id, $key, wp_slash($value));
  if (LK_Layout_Package::value_hash($key, get_post_meta($id, $key, true)) !== LK_Layout_Package::value_hash($key, $value)) throw new RuntimeException('Layout metadata verification failed.');
 }
 function remove($id, $key) {
  $this->guard($id, $key); delete_post_meta($id, $key);
  if (metadata_exists('post', $id, $key)) throw new RuntimeException('Cannot remove obsolete layout metadata.');
 }
 function lock() {
  global $wpdb;
  $this->lock_name = 'lk-layout-' . substr(hash('sha256', DB_NAME . $wpdb->prefix), 0, 32);
  return (string) $wpdb->get_var($wpdb->prepare('SELECT GET_LOCK(%s,0)', $this->lock_name)) === '1';
 }
 function unlock() { global $wpdb; $wpdb->get_var($wpdb->prepare('SELECT RELEASE_LOCK(%s)', $this->lock_name)); }
 function begin($changes) {
  global $wpdb;
  foreach (array($wpdb->postmeta, $wpdb->options) as $table) {
   $row = $wpdb->get_row($wpdb->prepare('SHOW TABLE STATUS WHERE Name = %s', $table));
   if (!$row || strcasecmp($row->Engine, 'InnoDB') !== 0) throw new RuntimeException('Transactional InnoDB tables are required for layout sync.');
  }
  if ($wpdb->query('START TRANSACTION') === false) throw new RuntimeException('Cannot start the layout transaction.');
  try {
   foreach (array_unique(array_column($changes, 'id')) as $id) {
    if ($wpdb->query($wpdb->prepare("SELECT meta_id FROM $wpdb->postmeta WHERE post_id = %d FOR UPDATE", $id)) === false) throw new RuntimeException('Cannot lock layout metadata.');
    wp_cache_delete($id, 'post_meta');
   }
  } catch (Throwable $error) { $this->abort(); throw $error; }
 }
 function commit() { global $wpdb; if ($wpdb->query('COMMIT') === false) throw new RuntimeException('Cannot commit the layout transaction.'); }
 function abort() {
  global $wpdb; $wpdb->query('ROLLBACK');
  wp_cache_delete('lk_layout_sync_state', 'options'); wp_cache_delete('notoptions', 'options');
  foreach ($this->touched as $id) clean_post_cache($id);
 }
 function backup($data) {
  $id = 'lk_layout_backup_' . str_replace('-', '', wp_generate_uuid4());
  if (!add_option($id, $data, '', false)) throw new RuntimeException('Cannot save the layout backup.');
  return $id;
 }
 function load_backup($id) {
  if (!preg_match('/^lk_layout_backup_[a-f0-9]{32}$/D', $id)) throw new RuntimeException('Invalid backup ID.');
  $backup = get_option($id);
  if (!is_array($backup)) throw new RuntimeException('Layout backup not found.');
  return $backup;
 }
 function asset($key, $asset, $source) {
  $uploads = wp_upload_dir();
  if ($uploads['error']) throw new RuntimeException('The uploads directory is not writable.');
  $relative = 'lk-layouts/' . $asset['file']; $path = $uploads['basedir'] . '/' . $relative;
  $url = $uploads['baseurl'] . '/' . $relative;
  if (!is_dir(dirname($path)) && !wp_mkdir_p(dirname($path))) throw new RuntimeException('Cannot create the managed layout media directory.');
  if (!is_file($path)) {
   $temp = $path . '.' . wp_generate_password(8, false) . '.tmp';
   if (!copy($source, $temp) || !hash_equals($key, hash_file('sha256', $temp)) || !rename($temp, $path)) throw new RuntimeException('Cannot stage a layout image.');
  }
  if (!hash_equals($key, hash_file('sha256', $path))) throw new RuntimeException('A destination layout asset was modified.');
  $ids = get_posts(array('post_type' => 'attachment', 'post_status' => 'inherit', 'posts_per_page' => 1, 'fields' => 'ids', 'meta_key' => '_lk_layout_asset_sha', 'meta_value' => $key));
  $id = $ids ? (int) $ids[0] : 0;
  if ($id && wp_normalize_path(get_attached_file($id)) !== wp_normalize_path($path)) throw new RuntimeException('A managed layout attachment was changed on the destination.');
  if (!$id) {
   $mime = wp_check_filetype($asset['file'])['type'] ?: 'image/svg+xml';
   if (strpos($mime, 'image/') !== 0) throw new RuntimeException('Only layout images may be imported.');
   $id = wp_insert_attachment(array('post_title' => sanitize_text_field($asset['title']), 'post_status' => 'inherit', 'post_mime_type' => $mime), $path, 0, true);
   if (is_wp_error($id)) throw new RuntimeException('Cannot register a layout image.');
   update_attached_file($id, $path);
   update_post_meta($id, '_wp_attachment_image_alt', sanitize_text_field($asset['alt']));
   wp_update_attachment_metadata($id, array('file' => $relative, 'width' => (int) $asset['width'], 'height' => (int) $asset['height'], 'sizes' => array()));
   update_post_meta($id, '_lk_layout_asset_sha', $key);
  }
  // Existing uploads/attachment records are never reused by local numeric ID,
  // overwritten, resized, or deleted. These are separate managed attachments.
  return array('id' => $id, 'url' => $url);
 }
 function invalidate($ids) {
  foreach ($ids as $id) {
   clean_post_cache($id); delete_post_meta($id, '_elementor_css'); delete_post_meta($id, '_elementor_element_cache');
   do_action('litespeed_purge_post', $id);
  }
  if (class_exists(\Elementor\Plugin::class)) \Elementor\Plugin::$instance->files_manager->clear_cache();
 }
}
