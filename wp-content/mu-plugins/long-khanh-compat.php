<?php
/**
 * Plugin Name: Tiền Hải - Compatibility bridge
 * Description: Bridge CubeWP/Elementor compatibility without editing vendor files or stored content.
 */
if (!defined('ABSPATH')) exit;
add_action('plugins_loaded',function(){
 if(function_exists('cubewp_render_blocks')){
  remove_filter('render_block','cubewp_render_blocks',10);
  add_filter('render_block','lk_compat_render_blocks',10,3);
 }
},99);
function lk_compat_render_blocks($content,$block,$instance=null){
 return cubewp_render_blocks($content,$block,$instance);
}

// An older CubeWP module may remain on a host that preserves existing files.
// Its controls call an optional user-fields helper even on post-only pages.
// Elementor reads every registered tag's controls when rendering its config,
// so this unused, unsupported tag can otherwise crash the whole frontend.
add_action('elementor/dynamic_tags/register','lk_compat_cubewp_optional_tags',100);
function lk_compat_cubewp_optional_tags($manager){
 if(!function_exists('get_user_fields_by_type'))$manager->unregister('cubewp-business-hours-tag');
}
