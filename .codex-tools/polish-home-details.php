<?php
if(PHP_SAPI!=='cli')exit;
require dirname(__DIR__).'/wp-load.php';
function lk_home_details($nodes){foreach($nodes as &$node){
 $classes=$node['settings']['css_classes']??$node['settings']['_css_classes']??'';
 if(str_contains($classes,'lk-category-card')||str_contains($classes,'lk-region-card')){
  $query=array();foreach($node['elements'] as $widget)if(($widget['widgetType']??'')==='heading')parse_str(wp_parse_url($widget['settings']['link']['url']??'',PHP_URL_QUERY)?:'',$query);
  if(!empty($query['categories'])&&!str_contains($classes,'lk-category-'.$query['categories']))$node['settings']['css_classes'].=' lk-category-'.sanitize_html_class($query['categories']);
  if(!empty($query['regions']))foreach($node['elements'] as &$widget)if(($widget['widgetType']??'')==='text-editor'){$widget['settings']['lk_binding']='region-count';$widget['settings']['lk_region']=$query['regions'];}unset($widget);
 }
 if(($node['widgetType']??'')==='button'&&str_contains($classes,'lk-nearby'))$node['settings']['selected_icon']=array('value'=>'fas fa-map-marker-alt','library'=>'fa-solid');
 if(!empty($node['elements']))$node['elements']=lk_home_details($node['elements']);
 }return $nodes;}
foreach(array(get_option('page_on_front'),get_option('lk_template_header')) as $id){$data=json_decode(get_post_meta($id,'_elementor_data',true),true);$data=lk_home_details($data);update_post_meta($id,'_elementor_data',wp_slash(wp_json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)));delete_post_meta($id,'_elementor_css');delete_post_meta($id,'_elementor_element_cache');}
echo 'Home overlays, mobile icon and live region counts updated.',PHP_EOL;
