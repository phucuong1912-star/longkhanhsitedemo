<?php
if (!defined('ABSPATH')) exit;
// ListingPro loads its parent functions automatically. Keep overrides here;
// never copy these files into the parent theme when updating ListingPro.
add_action('after_setup_theme', function () {
 if (defined('LK_DIR')) require_once __DIR__ . '/includes/presentation.php';
 if (defined('LK_DIR')) require_once __DIR__ . '/includes/feedback2.php';
 if (defined('LK_DIR')) require_once __DIR__ . '/includes/feedback-final.php';
}, 20);

// Keep public typography consistent across ListingPro and Elementor templates.
add_action('wp_enqueue_scripts', function () {
 wp_enqueue_style('th-quicksand', 'https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap', array(), null);
 $path = get_stylesheet_directory() . '/assets/branding.css';
 wp_enqueue_style('th-branding', get_stylesheet_directory_uri() . '/assets/branding.css', array('th-quicksand'), (string) filemtime($path));
}, 100);

add_filter('wp_resource_hints', function ($urls, $relation) {
 if ($relation === 'preconnect') {
  $urls[] = 'https://fonts.googleapis.com';
  $urls[] = array('href' => 'https://fonts.gstatic.com', 'crossorigin' => 'anonymous');
 }
 return $urls;
}, 10, 2);

// Serve admin dependencies individually. After a host migration, load-scripts.php
// can keep a year-long cached bundle built before the vendor files were restored.
add_action('admin_init', function () {
 $GLOBALS['concatenate_scripts'] = false;
});

// Load child overrides after ListingPro's generated stylesheet (priority 11).
add_action('wp_enqueue_scripts', function () {
 $path = get_stylesheet_directory() . '/assets/parent-overrides.css';
 wp_enqueue_style('lk-parent-overrides', get_stylesheet_directory_uri() . '/assets/parent-overrides.css', array('LP_dynamic_php_css'), (string) filemtime($path));
}, 20);
