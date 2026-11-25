<?php
/** Local integration check against the installed Yoast API and existing posts. */
if (PHP_SAPI !== 'cli') exit(1);
define('LK_LAYOUT_EXPORTING', true);
define('DISABLE_WP_CRON', true);
require dirname(__DIR__) . '/wp-includes/plugin.php';
add_filter('pre_http_request', function(){ return new WP_Error('local-seo-test'); }, PHP_INT_MAX);
add_filter('pre_wp_mail', '__return_true', PHP_INT_MAX);
require dirname(__DIR__) . '/wp-load.php';
if (wp_parse_url(home_url(), PHP_URL_HOST) !== 'localhost') throw new RuntimeException('Local only.');
function seo_title_check($ok, $label) {
 if (!$ok) throw new RuntimeException($label);
 echo 'PASS ' . $label . "\n";
}
global $wpdb;
$fingerprint = function() use ($wpdb) {
 $data = array();
 foreach (array($wpdb->posts, $wpdb->postmeta) as $table) $data[$table] = hash('sha256', serialize($wpdb->get_results("SELECT * FROM `$table` ORDER BY 1,2", ARRAY_A)));
 return $data;
};
$before = $fingerprint();
$layout = hash_file('sha256', WP_CONTENT_DIR . '/lk-layouts/manifest.json');
seo_title_check(defined('WPSEO_VERSION') && function_exists('wpseo_replace_vars'), 'Real Yoast SEO plugin is loaded');
seo_title_check(!in_array('seo-repair-kit/seo-repair-kit.php', get_option('active_plugins'), true), 'SEO Repair Kit remains installed without competing SEO output');
$css_rules = function($path) { return trim(preg_replace('#/\*.*?\*/#s', '', str_replace("\r\n", "\n", file_get_contents($path)))); };
seo_title_check($css_rules(LK_DIR . 'assets/seo-font-compat.css') === $css_rules(WP_PLUGIN_DIR . '/seo-repair-kit/public/css/seo-repair-kit-public.css'), 'Legacy public font CSS rules are preserved exactly');
seo_title_check(WPSEO_Options::get('title-post') === '%%title%% %%page%% | Thổ Địa Tiền Hải', 'Native Yoast post title template is configured');
$posts = get_posts(array('post_type'=>'post', 'post_status'=>'publish', 'posts_per_page'=>-1));
seo_title_check(count($posts) > 0, 'Existing published posts are available');
foreach ($posts as $post) {
 $raw = get_post_meta($post->ID, '_yoast_wpseo_title', true);
 if ($raw === '') $raw = WPSEO_Options::get('title-post');
 $title = apply_filters('wpseo_title', wpseo_replace_vars($raw, $post));
 seo_title_check(str_contains($title, 'Thổ Địa Tiền Hải') && substr_count($title, 'Thổ Địa Tiền Hải') === 1, 'Published post ' . $post->ID . ' has the brand exactly once');
}
$custom = 'Cẩm nang riêng cho du khách';
seo_title_check(apply_filters('wpseo_title', $custom) === $custom . ' | Thổ Địa Tiền Hải', 'Custom SEO title wording is preserved while adding the brand');
seo_title_check(apply_filters('wpseo_title', 'Quán ăn &#8211; Địa Điểm Tiền Hải') === 'Quán ăn &#8211; Thổ Địa Tiền Hải', 'Encoded SEO separators do not retain the old site name');
seo_title_check(apply_filters('wpseo_title', 'Quán ăn | Thổ Địa Tiền Hải') === 'Quán ăn | Thổ Địa Tiền Hải', 'Already branded titles are unchanged');
seo_title_check(lk_seo_brand_title('') === '', 'Empty WordPress pre-title keeps the native fallback');
seo_title_check($before === $fingerprint() && $layout === hash_file('sha256', WP_CONTENT_DIR . '/lk-layouts/manifest.json'), 'Rendering titles does not change posts, metadata or the Elementor package');
