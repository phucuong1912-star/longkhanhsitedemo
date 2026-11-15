<?php
/** Export only allowlisted Elementor metadata from the local WordPress DB. */
if (PHP_SAPI !== 'cli') { http_response_code(403); exit; }
define('LK_LAYOUT_EXPORTING', true);
$root = dirname(__DIR__, 2);
try {
require $root . '/wp-load.php';
$host = wp_parse_url(home_url(), PHP_URL_HOST);
if (!in_array($host, array('localhost', '127.0.0.1', '::1'), true) && wp_get_environment_type() !== 'local') throw new RuntimeException('Layout export is allowed only on a local WordPress installation.');
$directory = WP_CONTENT_DIR . '/lk-layouts';
$policy = lk_layout_policy(); $store = new LK_Layout_WordPress_Store();
$documents = array(); $ids = array(); $assets = array(); $files = array(); $uploads = wp_upload_dir();
$check = in_array('--check', $argv, true);
foreach ($policy['documents'] as $key => $scope) $ids[$store->resolve($key, $scope)] = $key;
$url_map = array(rtrim(home_url(), '/') => '{{home}}', rtrim(site_url(), '/') => '{{site}}');
// Prefer the home token when home and site happen to use the same origin.
$url_map[rtrim(home_url(), '/')] = '{{home}}';
function layout_export_media($id) {
 global $assets, $files, $uploads;
 $path = get_attached_file($id); $resolved = $path ? realpath($path) : false; $base = realpath($uploads['basedir']);
 if (!$resolved || !$base || strpos(str_replace('\\', '/', $resolved), rtrim(str_replace('\\', '/', $base), '/') . '/') !== 0) throw new RuntimeException('A layout image is missing or outside uploads: attachment ' . $id);
 $extension = strtolower(pathinfo($resolved, PATHINFO_EXTENSION));
 if (!in_array($extension, array('png', 'jpg', 'jpeg', 'webp', 'gif', 'avif', 'svg'), true)) throw new RuntimeException('Only image attachments may be included in a layout.');
 $hash = hash_file('sha256', $resolved); $meta = wp_get_attachment_metadata($id);
 $assets[$hash] = array('file' => $hash . '.' . $extension, 'title' => get_the_title($id), 'alt' => get_post_meta($id, '_wp_attachment_image_alt', true), 'width' => (int) ($meta['width'] ?? 0), 'height' => (int) ($meta['height'] ?? 0));
 $files[$hash . '.' . $extension] = $resolved;
 return $hash;
}
function layout_export_value($value, $name = '') {
 global $ids, $url_map;
 if (is_array($value)) {
  if (!isset($value['elType']) && isset($value['id']) && is_numeric($value['id']) && get_post_type((int) $value['id']) === 'attachment') {
   $asset = layout_export_media((int) $value['id']); $value['id'] = '{{attachment:' . $asset . '}}';
   if (isset($value['url'])) $value['url'] = '{{asset:' . $asset . '}}';
  }
  foreach ($value as $key => &$child) $child = layout_export_value($child, (string) $key);
  return $value;
 }
 if (in_array($name, array('template_id', 'popup_id'), true) && is_numeric($value) && (int) $value) {
  if (!isset($ids[(int) $value])) throw new RuntimeException('Referenced template must be added to scopes.json before exporting.');
  return '{{document:' . $ids[(int) $value] . '}}';
 }
 if (!is_string($value)) return $value;
 // Embedded content images also need portable attachment URLs, not local IDs.
 if (strpos($value, wp_upload_dir()['baseurl']) !== false) {
  preg_match_all('~https?://[^\s"\'<>\)]+~u', $value, $matches);
  foreach ($matches[0] as $url) {
   if (strpos(html_entity_decode($url), rtrim(wp_upload_dir()['baseurl'], '/') . '/') !== 0) continue;
   $id = attachment_url_to_postid(html_entity_decode($url));
   if (!$id) $id = attachment_url_to_postid(preg_replace('/-\d+x\d+(?=\.[a-z0-9]+$)/i', '', html_entity_decode($url)));
   if (!$id) throw new RuntimeException('An embedded uploads URL could not be mapped to an attachment: ' . $url);
   $value = str_replace($url, '{{asset:' . layout_export_media($id) . '}}', $value);
  }
 }
 // Gutenberg/editor image classes can contain IDs as well as URLs.
 $value = preg_replace_callback('/\bwp-image-(\d+)\b/', function ($match) {
  if (get_post_type((int) $match[1]) !== 'attachment') throw new RuntimeException('An embedded image ID cannot be mapped to a local attachment.');
  return 'wp-image-{{attachment:' . layout_export_media((int) $match[1]) . '}}';
 }, $value);
 $value = strtr($value, $url_map);
 if (preg_match('~https?://(?:localhost|127\.0\.0\.1|\[::1\])\b~i', $value)) throw new RuntimeException('Unmapped localhost URL in a layout.');
 return $value;
}
foreach ($policy['documents'] as $key => $scope) {
 $id = $store->resolve($key, $scope); $meta = array();
 foreach (LK_Layout_Package::META as $name) {
  if (!metadata_exists('post', $id, $name)) continue;
  $value = get_post_meta($id, $name, true);
  if ($name === '_elementor_data') $value = json_decode($value ?: '[]', true, 512, JSON_THROW_ON_ERROR);
  $meta[$name] = layout_export_value($value);
 }
 $documents[$key] = array('type' => $scope['type'], 'meta' => $meta);
}
$payload = array('version' => 1, 'documents' => $documents, 'assets' => $assets);
$package = $payload; $package['release'] = LK_Layout_Package::hash($payload);
$json = LK_Layout_Package::json($package) . "\n";
if ($check) {
 if (!is_file($directory . '/manifest.json') || file_get_contents($directory . '/manifest.json') !== $json) { fwrite(STDERR, "Local Elementor changes are not exported. Run php scripts/elementor/export.php and commit the package.\n"); exit(1); }
 foreach ($files as $name => $source) if (!is_file($directory . '/assets/' . $name) || hash_file('sha256', $directory . '/assets/' . $name) !== hash_file('sha256', $source)) { fwrite(STDERR, "A layout asset is not committed/exported: $name\n"); exit(1); }
 echo "Elementor package matches the local database.\n"; exit;
}
if (!wp_mkdir_p($directory . '/assets')) throw new RuntimeException('Cannot create the layout package directory.');
foreach ($files as $name => $source) {
 $target = $directory . '/assets/' . $name;
 if (!is_file($target) && !copy($source, $target)) throw new RuntimeException('Cannot package an image.');
}
file_put_contents($directory . '/manifest.json', $json, LOCK_EX);
echo wp_json_encode(array('release' => $package['release'], 'documents' => count($documents), 'images' => count($assets), 'articles_exported' => 0)), PHP_EOL;
} catch (Throwable $error) { fwrite(STDERR, 'Layout export failed: ' . $error->getMessage() . "\n"); exit(1); }
