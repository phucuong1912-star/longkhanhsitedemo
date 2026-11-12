<?php
if(PHP_SAPI!=='cli')exit;
require dirname(__DIR__).'/wp-load.php';
$id=(int)get_option('lk_template_header');$nodes=json_decode(get_post_meta($id,'_elementor_data',true),true);
function lk_add_mobile_location($nodes){foreach($nodes as &$node){if(str_contains($node['settings']['css_classes']??'','lk-mobile-actions')&&!str_contains(wp_json_encode($node),'lk-nearby')){
 $button=array('id'=>'c813fe20','elType'=>'widget','widgetType'=>'button','settings'=>array('text'=>'Quanh đây','link'=>array('url'=>lk_link('search')),'selected_icon'=>array('value'=>'fas fa-location-arrow','library'=>'fa-solid'),'_css_classes'=>'lk-nearby'),'elements'=>array());array_splice($node['elements'],1,0,array($button));
 }if(!empty($node['elements']))$node['elements']=lk_add_mobile_location($node['elements']);}return $nodes;}
$nodes=lk_add_mobile_location($nodes);update_post_meta($id,'_elementor_data',wp_slash(wp_json_encode($nodes,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)));delete_post_meta($id,'_elementor_css');delete_post_meta($id,'_elementor_element_cache');echo 'Mobile location control added.',PHP_EOL;
