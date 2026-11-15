<?php
/** php wp-content/plugins/long-khanh-directory/tools/setup-blog.php */
if (PHP_SAPI !== 'cli') { http_response_code(403); exit; }
if (!defined('ABSPATH')) require dirname(__DIR__, 4) . '/wp-load.php';
if (!function_exists('lk_blog_enabled')) throw new RuntimeException('Activate the directory plugin before setting up Blog.');
if (!class_exists('DOMDocument')) throw new RuntimeException('The PHP DOM extension is required to preserve legacy article bodies.');
$page_id = lk_blog_page_id();
if (!$page_id || get_post_type($page_id) !== 'page' || get_post_status($page_id) !== 'publish') throw new RuntimeException('The published Blog page was not found.');
$nodes = json_decode(get_post_meta($page_id, '_elementor_data', true), true);
if (!is_array($nodes)) throw new RuntimeException('The original Blog Elementor document is missing.');
$terms = array();
foreach (lk_blog_groups() as $slug => $label) {
 $term = get_term_by('slug', $slug, 'category');
 if ($term) $terms[$slug] = $term->term_id;
 else {
  $created = wp_insert_term($label, 'category', array('slug' => $slug));
  if (is_wp_error($created)) throw new RuntimeException($created->get_error_message());
  $terms[$slug] = $created['term_id'];
 }
}
// Add categories to the original articles using their existing Blog sections.
// Preserve all original categories and all post/Elementor content.
$assigned = array();
foreach ($nodes as $node) {
 $classes = $node['settings']['css_classes'] ?? '';
 if (strpos($classes, 'lk-blog-row') === false) continue;
 $group = '';
 foreach ($terms as $slug => $term_id) if (strpos($classes, 'lk-blog-group-' . $slug) !== false) $group = $slug;
 if (!$group) continue;
 $walk = function ($items) use (&$walk, $terms, $group, &$assigned) {
  foreach ($items as $item) {
   if (($item['widgetType'] ?? '') === 'heading') {
    $slug = basename(untrailingslashit((string) wp_parse_url($item['settings']['link']['url'] ?? '', PHP_URL_PATH)));
    $post = $slug ? get_page_by_path($slug, OBJECT, 'post') : null;
    if ($post) {
     $result = wp_set_post_terms($post->ID, array((int) $terms[$group]), 'category', true);
     if (is_wp_error($result)) throw new RuntimeException($result->get_error_message());
     $assigned[$post->ID] = $group;
    }
   }
   if (!empty($item['elements'])) $walk($item['elements']);
  }
 };
 $walk($node['elements'] ?? array());
}
// Hide only the five untouched installer/demo articles that were never on this
// Blog. Leave their posts in WordPress and include every newly published post.
if (get_option('lk_blog_excluded_ids', null) === null) {
 $samples = array(1 => 'Chào tất cả mọi người!', 26746 => 'Hello world!', 50 => 'Excited news about arrival fashion.', 47 => 'Reduce Unwanted Wrinkles', 44 => 'Chemex coffee for two people');
 $excluded = array();
 foreach ($samples as $id => $title) if (get_post_type($id) === 'post' && get_post_field('post_title', $id) === $title) $excluded[] = $id;
 update_option('lk_blog_excluded_ids', $excluded, false);
}
update_option('lk_dynamic_blog_enabled', 1, false);
update_option('lk_dynamic_blog_schema', 1, false);
add_rewrite_rule('^blog/?$', 'index.php?page_id=' . $page_id, 'top');
flush_rewrite_rules(false);
delete_post_meta($page_id, '_elementor_element_cache');
do_action('litespeed_purge_post', $page_id);
echo wp_json_encode(array('enabled' => true, 'blog_page_id' => $page_id, 'categories' => $terms, 'legacy_articles_assigned' => $assigned,
 'excluded_demo_ids' => get_option('lk_blog_excluded_ids'), 'original_blog_document_unchanged' => true), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), PHP_EOL;
