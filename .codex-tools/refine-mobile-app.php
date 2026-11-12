<?php
if(PHP_SAPI!=='cli')exit;
require dirname(__DIR__).'/wp-load.php';
$users=get_users(array('role'=>'administrator','number'=>1));wp_set_current_user($users[0]->ID);
$media=get_option('lk_media_map');
function lk_app_id($key){return substr(md5('responsive-app-'.$key),0,8);}
function lk_app_image($doc,$key,$classes,$hide=array()){
 global $media;return array('id'=>lk_app_id($doc.$key),'elType'=>'widget','widgetType'=>'image','settings'=>array_merge(array('image'=>array('id'=>$media[$key]['id'],'url'=>$media[$key]['url']),'image_size'=>'full','_css_classes'=>$classes),$hide),'elements'=>array());
}
function lk_refine_app_nodes($nodes,$doc){foreach($nodes as &$node){
 $classes=$node['settings']['css_classes']??'';
 if(in_array('lk-app-panel',preg_split('/\s+/',trim($classes)),true)){
  foreach(array_keys($node['settings']) as $key)if(str_starts_with($key,'background_'))unset($node['settings'][$key]);
  $node['settings']['background_background']='classic';$node['settings']['background_color']='#d9ecdc';
  $node['settings']['gap']='no';
  $copy=&$node['elements'][0];$copy['settings']['css_classes']='lk-app-panel-copy';
  $copy['settings']['_column_size']=55;$copy['settings']['_inline_size_mobile']=100;$copy['settings']['_inline_size_tablet']=100;
  $copy['settings']['padding']=array('unit'=>'px','top'=>'34','right'=>'40','bottom'=>'34','left'=>'40','isLinked'=>false);
  $copy['settings']['padding_tablet']=$copy['settings']['padding_mobile']=array('unit'=>'px','top'=>'10','right'=>'10','bottom'=>'10','left'=>'10','isLinked'=>true);
  foreach($copy['elements'] as &$heading)if(($heading['widgetType']??'')==='heading'){$heading['settings']['typography_typography']='custom';$heading['settings']['typography_font_weight']='700';$heading['settings']['typography_line_height']=array('unit'=>'em','size'=>1.5715);}unset($heading);
  foreach($copy['elements'] as &$child)if(str_contains($child['settings']['css_classes']??'','lk-store-links')){
   $child['elements'][0]['settings']['css_classes']='lk-qr-column';
   $child['elements'][0]['settings']['hide_mobile']='hidden-mobile';$child['elements'][0]['settings']['hide_tablet']='hidden-tablet';
   $child['elements'][1]['settings']['css_classes']='lk-badges';
   $child['elements'][1]['settings']['_inline_size_mobile']=100;$child['elements'][1]['settings']['_inline_size_tablet']=100;
  }unset($child,$copy);
  $node['elements'][1]=array('id'=>lk_app_id($doc.'art'),'elType'=>'column','settings'=>array('_column_size'=>45,'_inline_size_mobile'=>100,'_inline_size_tablet'=>100,'css_classes'=>'lk-app-art'),'elements'=>array(
   lk_app_image($doc,'app-desktop','lk-app-desktop',array('hide_tablet'=>'hidden-tablet','hide_mobile'=>'hidden-mobile')),
   lk_app_image($doc,'app-mobile','lk-app-mobile',array('hide_desktop'=>'hidden-desktop')),
  ));
 }
 if(!empty($node['elements']))$node['elements']=lk_refine_app_nodes($node['elements'],$doc);
 }return $nodes;}
foreach(array(get_option('page_on_front'),get_option('lk_page_map')['download']) as $doc){
 $raw=get_post_meta($doc,'_elementor_data',true);if(!get_post_meta($doc,'_lk_before_mobile_app_20261112',true))update_post_meta($doc,'_lk_before_mobile_app_20261112',wp_slash($raw));
 $nodes=lk_refine_app_nodes(json_decode($raw,true),$doc);
 update_post_meta($doc,'_elementor_data',wp_slash(wp_json_encode($nodes,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)));delete_post_meta($doc,'_elementor_css');delete_post_meta($doc,'_elementor_element_cache');
 echo 'Native app images and responsive store row: '.$doc,PHP_EOL;
}
Elementor\Plugin::$instance->files_manager->clear_cache();
