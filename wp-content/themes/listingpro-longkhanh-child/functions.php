<?php
if (!defined('ABSPATH')) exit;
// ListingPro loads its parent functions automatically. Keep overrides here;
// never copy these files into the parent theme when updating ListingPro.
add_action('after_setup_theme', function () {
 if (defined('LK_DIR')) require_once __DIR__ . '/includes/presentation.php';
}, 20);
