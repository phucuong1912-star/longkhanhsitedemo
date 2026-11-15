<?php
if (!defined('ABSPATH')) exit;

function lk_blog_enabled() { return (bool) get_option('lk_dynamic_blog_enabled'); }
function lk_blog_page_id() { return (int) (get_option('lk_page_map', array())['blog'] ?? 0); }
function lk_is_blog_index() { return lk_blog_enabled() && is_page(lk_blog_page_id()); }
function lk_is_blog_view() { return lk_blog_enabled() && (is_singular('post') || lk_is_blog_index()); }
function lk_blog_url() { return get_permalink(lk_blog_page_id()) ?: home_url('/blog/'); }
function lk_blog_groups() {
 return array('an-uong' => 'Cẩm nang ăn uống', 'du-lich' => 'Cẩm nang du lịch', 'giai-tri' => 'Vui chơi và giải trí');
}
function lk_blog_group() {
 $group = is_scalar($_GET['blog_group'] ?? null) ? sanitize_key(wp_unslash($_GET['blog_group'])) : '';
 return isset(lk_blog_groups()[$group]) ? $group : '';
}
function lk_blog_page_number() {
 return is_scalar($_GET['blog_page'] ?? null) ? max(1, absint($_GET['blog_page'])) : 1;
}
function lk_blog_query($group = '', $page = 1, $limit = 12, $exclude = array()) {
 $args = array('post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => $limit,
  'paged' => $page, 'ignore_sticky_posts' => true, 'orderby' => array('date' => 'DESC', 'ID' => 'DESC'),
  'post__not_in' => array_values(array_unique(array_merge(array_map('absint', (array) get_option('lk_blog_excluded_ids', array())), $exclude))));
 if ($group) $args['tax_query'] = array(array('taxonomy' => 'category', 'field' => 'slug', 'terms' => $group, 'include_children' => true));
 return new WP_Query($args);
}

// New WordPress posts use the same public URL structure as the existing articles.
add_filter('post_link', function ($url, $post) {
 if (!lk_blog_enabled() || !$post->post_name) return $url;
 return trailingslashit(lk_blog_url()) . $post->post_name . '/';
}, 20, 2);
add_filter('page_link', function ($url, $id) {
 return lk_blog_enabled() && (int) $id === lk_blog_page_id() ? home_url('/blog/') : $url;
}, 20, 2);
add_filter('body_class', function ($classes) {
 if (lk_is_blog_index() && lk_blog_group()) $classes[] = 'lk-blog-category';
 return array_unique($classes);
});
foreach (array('option_elementor_element_cache_ttl', 'default_option_elementor_element_cache_ttl') as $hook) {
 add_filter($hook, function ($value) { return did_action('wp') && lk_is_blog_index() ? 'disable' : $value; }, 50);
}
// Keep /blog/ stable if an editor later changes the page's WordPress slug.
add_action('init', function () {
 if (lk_blog_enabled() && lk_blog_page_id()) add_rewrite_rule('^blog/?$', 'index.php?page_id=' . lk_blog_page_id(), 'top');
}, 31);
add_filter('template_include', function ($template) {
 if (isset($_GET['elementor-preview']) && current_user_can('edit_post', get_queried_object_id())) return $template;
 if (lk_blog_enabled() && is_singular('post') && function_exists('lk_template')) return LK_DIR . 'templates/single-blog.php';
 return $template;
}, 110);
add_action('wp_enqueue_scripts', function () {
 if (lk_is_blog_view()) wp_enqueue_style('lk-blog', LK_URL . 'assets/blog.css', array('lk-feedback2'), (string) filemtime(LK_DIR . 'assets/blog.css'));
}, 100);

// Keep historical card bylines/dates, without rewriting their source documents.
function lk_blog_legacy_byline($post) {
 static $bylines;
 if ($bylines === null) {
  $bylines = array();
  $nodes = json_decode(get_post_meta(lk_blog_page_id(), '_elementor_data', true), true);
  $walk = function ($items) use (&$walk, &$bylines) {
   foreach ((array) $items as $node) {
    if (strpos($node['settings']['css_classes'] ?? '', 'lk-blog-card') !== false) {
     $slug = ''; $text = '';
     foreach (($node['elements'] ?? array()) as $widget) {
      if (($widget['widgetType'] ?? '') === 'heading') $slug = basename(untrailingslashit((string) wp_parse_url($widget['settings']['link']['url'] ?? '', PHP_URL_PATH)));
      if (($widget['widgetType'] ?? '') === 'text-editor') $text = trim(wp_strip_all_tags($widget['settings']['editor'] ?? ''));
     }
     if ($slug && $text) $bylines[$slug] = $text;
    }
    if (!empty($node['elements'])) $walk($node['elements']);
   }
  };
  $walk($nodes);
 }
 return $bylines[$post->post_name] ?? (get_the_author_meta('display_name', $post->post_author) . ' · ' . get_the_date('d/m/Y', $post));
}
function lk_blog_widget_node($key, $type, $settings) {
 return array('id' => substr(md5($key), 0, 8), 'elType' => 'widget', 'widgetType' => $type, 'settings' => $settings, 'elements' => array());
}
function lk_blog_card_node($post, $row_id) {
 $key = 'blog-' . $row_id . '-' . $post->ID;
 $link = array('url' => get_permalink($post));
 $image = array('id' => (int) get_post_thumbnail_id($post), 'url' => get_the_post_thumbnail_url($post, 'large') ?: lk_asset('no-image'));
 return array('id' => substr(md5($key), 0, 8), 'elType' => 'column', 'isInner' => false,
  'settings' => array('_column_size' => 33, 'css_classes' => 'lk-blog-card'), 'elements' => array(
   lk_blog_widget_node($key . '-image', 'image', array('image' => $image, 'image_size' => 'large', 'link_to' => 'custom', 'link' => $link)),
   lk_blog_widget_node($key . '-title', 'heading', array('title' => esc_html(get_the_title($post)), 'header_size' => 'h3', 'link' => $link)),
   lk_blog_widget_node($key . '-meta', 'text-editor', array('editor' => '<p>' . esc_html(lk_blog_legacy_byline($post)) . '</p>'))));
}
function lk_blog_pagination_node($query, $group, $page) {
 if ($query->max_num_pages < 2) return null;
 $url = $group ? add_query_arg('blog_group', $group, lk_blog_url()) : lk_blog_url();
 $links = paginate_links(array('base' => add_query_arg('blog_page', '%#%', $url), 'format' => '', 'total' => $query->max_num_pages,
  'current' => $page, 'prev_text' => '« Trước', 'next_text' => 'Sau »', 'type' => 'list'));
 return array('id' => substr(md5('blog-pagination-' . $group), 0, 8), 'elType' => 'section',
  'settings' => array('css_classes' => 'lk-section lk-blog-pagination', 'layout' => 'boxed'), 'elements' => array(
   array('id' => substr(md5('blog-pagination-col-' . $group), 0, 8), 'elType' => 'column', 'settings' => array('_column_size' => 100),
    'elements' => array(lk_blog_widget_node('blog-pagination-widget-' . $group, 'text-editor', array('editor' => '<nav aria-label="Phân trang bài viết">' . $links . '</nav>'))))));
}

// Replace only the article rows at render time. The original Elementor document,
// hero, headings, buttons and card content remain stored intact for rollback.
add_filter('elementor/frontend/builder_content_data', function ($nodes, $document_id) {
 if (!lk_blog_enabled() || (int) $document_id !== lk_blog_page_id()) return $nodes;
 $result = array(); $selected = lk_blog_group();
 foreach ($nodes as $node) {
  $classes = $node['settings']['css_classes'] ?? '';
  if ($selected && strpos($classes, 'lk-blog-hero') === false && strpos($classes, 'lk-blog-group-' . $selected) === false) continue;
  $featured = strpos($classes, 'lk-blog-featured') !== false;
  $row = strpos($classes, 'lk-blog-row') !== false;
  if (!$featured && !$row) { $result[] = $node; continue; }
  $group = '';
  foreach (lk_blog_groups() as $slug => $label) if (strpos($classes, 'lk-blog-group-' . $slug) !== false) $group = $slug;
  $paginated = $featured || ($selected && $group === $selected);
  $page = $paginated ? lk_blog_page_number() : 1;
  $query = lk_blog_query($group, $page, $paginated ? 12 : 6);
  $node['elements'] = array();
  foreach ($query->posts as $post) $node['elements'][] = lk_blog_card_node($post, $node['id']);
  if (!$node['elements']) $node['elements'][] = array('id' => substr(md5('blog-empty-' . $node['id']), 0, 8), 'elType' => 'column',
   'settings' => array('_column_size' => 100), 'elements' => array(lk_blog_widget_node('empty-' . $node['id'], 'text-editor', array('editor' => '<p class="lk-blog-empty">Chưa có bài viết.</p>'))));
  $result[] = $node;
  if ($paginated && ($pagination = lk_blog_pagination_node($query, $group, $page))) $result[] = $pagination;
 }
 return $result;
}, 50, 2);
add_filter('elementor/element/is_dynamic_content', function ($dynamic) { return lk_is_blog_index() ? true : $dynamic; }, 50);

function lk_blog_dom($html) {
 $dom = new DOMDocument('1.0', 'UTF-8');
 $previous = libxml_use_internal_errors(true);
 $dom->loadHTML('<?xml encoding="UTF-8"><div id="lk-blog-document">' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
 libxml_clear_errors(); libxml_use_internal_errors($previous);
 return $dom;
}
function lk_blog_inner_html($dom, $node) {
 $html = '';
 foreach ($node->childNodes as $child) $html .= $dom->saveHTML($child);
 return $html;
}
function lk_blog_article_body($post) {
 $data = get_post_meta($post->ID, '_elementor_data', true);
 if ($data && class_exists(\Elementor\Plugin::class)) {
  // The display helper intentionally returns empty for the current post.
  // This shared single template owns rendering, so use the content API directly.
  $html = Elementor\Plugin::$instance->frontend->get_builder_content($post->ID, true);
  if ($html && class_exists('DOMDocument')) {
   $dom = lk_blog_dom($html); $xpath = new DOMXPath($dom);
   $article = $xpath->query('//*[contains(concat(" ",normalize-space(@class)," ")," lk-article ") and contains(concat(" ",normalize-space(@class)," ")," elementor-column ")]')->item(0);
   if ($article) {
    $wrap = $xpath->query('./div[contains(concat(" ",normalize-space(@class)," ")," elementor-widget-wrap ")]', $article)->item(0) ?: $article;
    $metadata = '';
    $remove = array();
    foreach ($xpath->query('.//*[contains(concat(" ",normalize-space(@class)," ")," lk-article-meta ")]', $wrap) as $node) { $metadata = trim($node->textContent); $remove[] = $node; }
    foreach ($xpath->query('.//*[contains(concat(" ",normalize-space(@class)," ")," elementor-widget-heading ")][.//h1]', $wrap) as $node) $remove[] = $node;
    foreach ($remove as $node) if ($node->parentNode) $node->parentNode->removeChild($node);
    return array('html' => '<div class="elementor elementor-' . (int) $post->ID . ' lk-blog-legacy-content">' . lk_blog_inner_html($dom, $wrap) . '</div>', 'meta' => $metadata, 'legacy' => true, 'full_document' => false);
   }
  }
  // Nonstandard legacy designs remain readable without flattening or data loss.
  if ($html) return array('html' => $html, 'meta' => '', 'legacy' => true, 'full_document' => true);
 }
 return array('html' => apply_filters('the_content', get_the_content(null, false, $post)), 'meta' => '', 'legacy' => false, 'full_document' => false);
}
function lk_blog_table_of_contents($html) {
 if (!class_exists('DOMDocument')) return array('html' => $html, 'items' => array());
 $dom = lk_blog_dom($html); $xpath = new DOMXPath($dom); $items = array(); $used = array();
 foreach ($xpath->query('//*[@id]') as $node) $used[$node->getAttribute('id')] = true;
 foreach ($xpath->query('//h2|//h3') as $index => $heading) {
  $text = trim($heading->textContent); if ($text === '') continue;
  $id = $heading->getAttribute('id');
  if (!$id) {
   $id = 'bai-viet-muc-' . ($index + 1);
   while (isset($used[$id])) $id .= '-noi-dung';
   $heading->setAttribute('id', $id); $used[$id] = true;
  }
  $items[] = array('id' => $id, 'text' => $text, 'level' => $heading->tagName);
 }
 return array('html' => lk_blog_inner_html($dom, $dom->getElementById('lk-blog-document')), 'items' => $items);
}
function lk_blog_related_posts($post_id) {
 $exclude = array_merge(array($post_id), array_map('absint', (array) get_option('lk_blog_excluded_ids', array())));
 $categories = wp_get_post_categories($post_id);
 $args = array('post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => 4, 'post__not_in' => $exclude,
  'ignore_sticky_posts' => true, 'orderby' => array('date' => 'DESC', 'ID' => 'DESC'));
 $posts = $categories ? get_posts(array_merge($args, array('category__in' => $categories))) : array();
 if (count($posts) < 4) $posts = array_merge($posts, get_posts(array_merge($args, array('posts_per_page' => 4 - count($posts), 'post__not_in' => array_merge($exclude, wp_list_pluck($posts, 'ID'))))));
 return $posts;
}

function lk_blog_purge($post_id) {
 if (!lk_blog_enabled() || get_post_type($post_id) !== 'post' || wp_is_post_revision($post_id)) return;
 delete_post_meta(lk_blog_page_id(), '_elementor_element_cache');
 do_action('litespeed_purge_post', lk_blog_page_id());
 do_action('litespeed_purge_url', lk_blog_url());
 foreach (lk_blog_groups() as $slug => $label) do_action('litespeed_purge_url', add_query_arg('blog_group', $slug, lk_blog_url()));
}
add_action('save_post_post', 'lk_blog_purge', 100);
add_action('rest_after_insert_post', function ($post) { lk_blog_purge($post->ID); }, 100);
add_action('updated_post_meta', function ($meta_id, $post_id, $key) { if ($key === '_thumbnail_id') lk_blog_purge($post_id); }, 100, 3);
add_action('trashed_post', 'lk_blog_purge');
add_action('untrashed_post', 'lk_blog_purge');
add_action('before_delete_post', 'lk_blog_purge');
add_action('set_object_terms', function ($id, $terms, $tt_ids, $taxonomy) { if ($taxonomy === 'category') lk_blog_purge($id); }, 100, 4);
add_action('admin_notices', function () {
 $screen = get_current_screen();
 if (!lk_blog_enabled() || !$screen || $screen->base !== 'post' || $screen->post_type !== 'post') return;
 if (get_post_meta(get_the_ID(), '_elementor_data', true)) echo '<div class="notice notice-info"><p>Bài cũ này đang giữ đầy đủ nội dung và bố cục đã dựng. Với bài mới, dùng Bài viết → Viết bài mới; bài đã xuất bản sẽ tự hiện trên Blog.</p></div>';
 else echo '<div class="notice notice-info"><p>Đăng bài tại đây: nhập tiêu đề, nội dung, ảnh đại diện và chọn chuyên mục. Bài đã xuất bản sẽ tự hiện trên Blog và dùng giao diện chung; không cần dựng trang Elementor.</p></div>';
});
