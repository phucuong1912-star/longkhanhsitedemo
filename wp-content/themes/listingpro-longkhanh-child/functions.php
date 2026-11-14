<?php
if (!defined('ABSPATH')) exit;
// ListingPro loads its parent functions automatically. Keep overrides here;
// never copy these files into the parent theme when updating ListingPro.
add_action('after_setup_theme', function () {
 if (defined('LK_DIR')) require_once __DIR__ . '/includes/presentation.php';
 if (defined('LK_DIR')) require_once __DIR__ . '/includes/feedback2.php';
}, 20);

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
