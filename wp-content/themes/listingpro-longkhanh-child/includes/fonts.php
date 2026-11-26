<?php
/** Public branded pages use the bundled Quicksand font without remote CSS. */
if (!defined('ABSPATH')) exit;

function lk_use_local_site_fonts() {
 return !is_admin() && function_exists('lk_is_site') && lk_is_site();
}

function lk_redundant_site_font($handle, $href = '') {
 // This stylesheet contains only a Figtree import and text font declarations.
 // branding.css already overrides those declarations with Quicksand.
 if ($handle === 'lk-seo-font-compat') return true;
 if (wp_parse_url($href, PHP_URL_HOST) !== 'fonts.googleapis.com') return false;
 return in_array($handle, array('font-rock-salt', 'font-quicksand', 'redux-google-fonts-listingpro_options'), true)
  || strpos($handle, 'elementor-gf-') === 0;
}

add_action('wp_enqueue_scripts', function () {
 if (!lk_use_local_site_fonts()) return;
 $styles = wp_styles();
 foreach ($styles->queue as $handle) {
  $src = $styles->registered[$handle]->src ?? '';
  if (lk_redundant_site_font($handle, $src)) wp_dequeue_style($handle);
 }
}, PHP_INT_MAX);

// Elementor may discover fonts while rendering nested header/footer documents,
// after wp_enqueue_scripts. Keep late font output out of the critical path too.
add_filter('elementor/frontend/print_google_fonts', function ($enabled) {
 return lk_use_local_site_fonts() ? false : $enabled;
});
add_filter('style_loader_tag', function ($html, $handle, $href) {
 return lk_use_local_site_fonts() && lk_redundant_site_font($handle, $href) ? '' : $html;
}, PHP_INT_MAX, 3);
