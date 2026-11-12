<?php
if(PHP_SAPI!=='cli')exit;
require dirname(__DIR__).'/wp-load.php';
function lk_fix_nodes($nodes){
 foreach($nodes as &$node){
  if(in_array($node['elType'],array('section','column'),true)&&isset($node['settings']['_css_classes'])){$node['settings']['css_classes']=$node['settings']['_css_classes'];unset($node['settings']['_css_classes']);}
  if(!empty($node['elements']))$node['elements']=lk_fix_nodes($node['elements']);
 }return $nodes;
}
$posts=get_posts(array('post_type'=>array('page','post','listing','elementor_library'),'post_status'=>'any','meta_key'=>'_lk_managed','meta_value'=>1,'posts_per_page'=>-1));
foreach($posts as $post){$data=json_decode(get_post_meta($post->ID,'_elementor_data',true),true);if(!$data)continue;update_post_meta($post->ID,'_elementor_data',wp_slash(wp_json_encode(lk_fix_nodes($data),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)));delete_post_meta($post->ID,'_elementor_css');delete_post_meta($post->ID,'_elementor_element_cache');}
Elementor\Plugin::$instance->files_manager->clear_cache();
echo 'Repaired '.count($posts).' Elementor documents. Active: '.get_option('stylesheet').'; parent: '.get_option('template').PHP_EOL;
