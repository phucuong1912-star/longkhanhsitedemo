<?php
/** Run once per database: php wp-content/plugins/long-khanh-directory/tools/rebrand-tien-hai.php */
if (PHP_SAPI !== 'cli') { http_response_code(403); exit; }
require dirname(__DIR__, 4) . '/wp-load.php';

$th_asset_urls = array();
$th_asset_urls[plugins_url('long-khanh-directory/assets/images/blog-slogan.svg')] = plugins_url('long-khanh-directory/assets/images/tien-hai-blog-slogan.svg');
// Logo attachments are managed by the site owner; preserve their chosen image.

function th_rebrand_text($text) {
 return preg_replace_callback('/Long\s*Khánh|Long Khanh/iu', function ($match) {
  $old = $match[0];
  $new = mb_stripos($old, 'á') !== false ? (preg_match('/\s/u', $old) ? 'Tiền Hải' : 'TiềnHải') : 'Tien Hai';
  if ($old === mb_strtoupper($old)) return mb_strtoupper($new);
  if ($old === mb_strtolower($old)) return mb_strtolower($new);
  return $new;
 }, $text);
}

// Decode stored arrays and Elementor JSON before replacing to preserve lengths
// in PHP serialization and handle both escaped and literal Vietnamese text.
function th_rebrand_value($value, $typography = false, $key = '') {
 global $th_asset_urls;
 if (is_array($value)) {
  foreach ($value as $child_key => $child) $value[$child_key] = th_rebrand_value($child, $typography, (string) $child_key);
  return $value;
 }
 if (!is_string($value)) return $value;
 if (is_serialized($value)) {
  $decoded = unserialize($value, array('allowed_classes' => false));
  // Leave third-party object settings intact.
  if (is_array($decoded) || is_scalar($decoded)) return serialize(th_rebrand_value($decoded, $typography, $key));
  return $value;
 }
 $json = json_decode($value, true);
 if (is_array($json)) {
  $updated = th_rebrand_value($json, $typography);
  return $updated === $json ? $value : wp_json_encode($updated, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
 }
 if ($typography && preg_match('/(?:font_family|font-family)$/', $key) && $value !== '' && !preg_match('/awesome|icon/i', $value)) return 'Quicksand';
 return th_rebrand_text(strtr($value, $th_asset_urls));
}

global $wpdb;
$counts = array();
$tables = array(
 'posts' => array('ID', array('post_title', 'post_content', 'post_excerpt')),
 'postmeta' => array('meta_id', array('meta_value')),
 'options' => array('option_id', array('option_value')),
 'terms' => array('term_id', array('name')),
 'term_taxonomy' => array('term_taxonomy_id', array('description')),
 'termmeta' => array('meta_id', array('meta_value')),
 'comments' => array('comment_ID', array('comment_content', 'comment_author')),
 'commentmeta' => array('meta_id', array('meta_value')),
 'links' => array('link_id', array('link_name', 'link_description', 'link_notes')),
);
foreach ($tables as $property => $definition) {
 list($id_column, $columns) = $definition;
 $table = $wpdb->$property;
 $counts[$property] = 0;
 $cursor = 0;
 do {
  $rows = $wpdb->get_results($wpdb->prepare("SELECT * FROM `$table` WHERE `$id_column` > %d ORDER BY `$id_column` LIMIT 200", $cursor), ARRAY_A);
  foreach ($rows as $row) {
   $cursor = (int) $row[$id_column];
   $updates = array();
   // Only font settings belonging to the public page builders are migrated.
   $typography = ($property === 'postmeta' && in_array($row['meta_key'], array('_elementor_data', '_elementor_page_settings'), true))
    || ($property === 'options' && $row['option_name'] === 'listingpro_options');
   foreach ($columns as $column) {
    $updated = th_rebrand_value($row[$column], $typography);
    if ($updated !== $row[$column]) $updates[$column] = $updated;
   }
   if (!$updates) continue;
   if ($wpdb->update($table, $updates, array($id_column => $cursor)) === false) {
    fwrite(STDERR, "Rebrand failed: $property row $cursor\n"); exit(1);
   }
   $counts[$property]++;
  }
 } while (count($rows) === 200);
}
wp_cache_flush();
if (function_exists('lk_invalidate_public_index')) lk_invalidate_public_index();
if (class_exists('Elementor\\Plugin')) Elementor\Plugin::$instance->files_manager->clear_cache();
echo wp_json_encode(array('updated' => $counts, 'site_name' => get_option('blogname')), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), PHP_EOL;
