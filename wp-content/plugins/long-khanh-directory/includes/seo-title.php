<?php
/** Shared SEO title branding; no option or content migrations on deployment. */
if (!defined('ABSPATH')) exit;

function lk_seo_brand_name() {
 return 'Thổ Địa Tiền Hải';
}

/** Native Yoast template for new installations; existing custom titles are preserved. */
function lk_seo_yoast_defaults($defaults, $option_name) {
 if ($option_name === 'wpseo_titles') {
  $defaults['title-post'] = '%%title%% %%page%% | ' . lk_seo_brand_name();
  $defaults['website_name'] = lk_seo_brand_name();
 }
 return $defaults;
}
add_filter('wpseo_defaults', 'lk_seo_yoast_defaults', 10, 2);

// SEO Repair Kit also supplied a public font stylesheet. Keep that exact rule
// after switching providers so installing Yoast does not alter visible text.
add_action('plugins_loaded', function() {
 if (!defined('WPSEO_VERSION') || defined('SEO_REPAIR_KIT_VERSION')) return;
 add_action('wp_enqueue_scripts', function() {
  wp_enqueue_style('lk-seo-font-compat', LK_URL . 'assets/seo-font-compat.css', array(), '1.0.0');
 });
});

function lk_seo_title_parts($parts) {
 if (is_admin() || is_feed()) return $parts;
 if (is_front_page() && !lk_auth_mode()) $parts['title'] = lk_seo_brand_name();
 if (isset($parts['site'])) $parts['site'] = lk_seo_brand_name();
 return $parts;
}
add_filter('document_title_parts', 'lk_seo_title_parts', 100);

/** Keep the page/custom SEO title, replacing only a separate site-name segment. */
function lk_seo_brand_title($title) {
 if (is_admin() || is_feed() || !is_string($title) || trim($title) === '') return $title;
 $brand = lk_seo_brand_name();
 $names = array_filter(array(get_option('blogname'), 'Địa Điểm Tiền Hải'));
 $names = array_unique(array_merge($names, array_map('esc_html', $names)));
 $segments = preg_split('/(\s+(?:[|–—»«-]|&#(?:8211|8212|187|171);|&(?:ndash|mdash|raquo|laquo);)\s+)/u', $title, -1, PREG_SPLIT_DELIM_CAPTURE);
 if ($segments === false) return $title;
 foreach (array_unique(array(0, count($segments) - 1)) as $index) {
  if (in_array(trim($segments[$index]), $names, true)) $segments[$index] = $brand;
 }
 $title = implode('', $segments);
 if (mb_stripos($title, $brand) === false) $title .= ' | ' . $brand;
 return $title;
}
// pre_get_document_title may short-circuit WordPress's later title filters.
add_filter('pre_get_document_title', 'lk_seo_brand_title', 100);
add_filter('document_title', 'lk_seo_brand_title', 100);
// Yoast's documented API also covers its own title presenter when installed.
add_filter('wpseo_title', 'lk_seo_brand_title', 100);
