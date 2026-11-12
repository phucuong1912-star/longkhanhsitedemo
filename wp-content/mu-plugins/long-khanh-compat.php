<?php
/**
 * Plugin Name: Long Khánh - Compatibility bridge
 * Description: Accept both legacy and current render_block hook signatures without editing WordPress or CubeWP vendor files.
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
