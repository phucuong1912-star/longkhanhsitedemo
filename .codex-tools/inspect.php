<?php
if (PHP_SAPI !== 'cli') { http_response_code(403); exit; }
require dirname(__DIR__) . '/wp-load.php';
global $wpdb, $wp_version;
$out = array(
 'php' => PHP_VERSION, 'wordpress' => $wp_version,
 'theme' => get_option('stylesheet'), 'template' => get_option('template'),
 'front' => get_option('page_on_front'), 'show_on_front' => get_option('show_on_front'),
 'permalinks' => get_option('permalink_structure'), 'active_plugins' => get_option('active_plugins'),
 'pages' => $wpdb->get_results("SELECT ID,post_title,post_name,post_status,post_type FROM {$wpdb->posts} WHERE post_type IN ('page','elementor_library') AND post_status != 'auto-draft' ORDER BY ID", ARRAY_A),
 'counts' => $wpdb->get_results("SELECT post_type,post_status,COUNT(*) AS count FROM {$wpdb->posts} GROUP BY post_type,post_status", ARRAY_A),
 'taxonomies' => array_keys(get_taxonomies()),
 'elementor' => array('version'=>defined('ELEMENTOR_VERSION')?ELEMENTOR_VERSION:null,'experiments'=>get_option('elementor_experiment-e_optimized_markup'),'kit'=>get_option('elementor_active_kit')),
);
echo wp_json_encode($out, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES), PHP_EOL;
