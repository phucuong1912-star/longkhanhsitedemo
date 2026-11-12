<?php
if(PHP_SAPI!=='cli')exit;
require dirname(__DIR__).'/wp-load.php';
$users=get_users(array('role'=>'administrator','number'=>1));wp_set_current_user($users[0]->ID);
$file=LK_DIR.'assets/images/manifest.json';$manifest=json_decode(file_get_contents($file),true);$manifest['review-illustration']=array('filename'=>'review-illustration.svg','source'=>'https://diadiemlongkhanh.com/images/ddlk-review.svg');file_put_contents($file,wp_json_encode($manifest,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
$id=(int)get_option('lk_template_listing');$nodes=json_decode(get_post_meta($id,'_elementor_data',true),true);$rating=array();
function lk_rating_anchors($nodes){foreach($nodes as &$node){
 $classes=$node['settings']['css_classes']??$node['settings']['_css_classes']??'';
 if(str_contains($classes,'lk-benefits'))$node['settings']['_element_id']='benefits';
 if(($node['widgetType']??'')==='image-gallery'&&($node['settings']['lk_binding']??'')==='gallery')$node['settings']['_element_id']='photos';
 if(($node['id']??'')==='lktabs03'){$items=array();foreach(array('photos'=>'Hình ảnh','detail'=>'Tổng quan','benefits'=>'Tiện ích','reviews'=>'Đánh giá','related'=>'Lân cận') as $anchor=>$label)$items[]=array('_id'=>'lk-tab-'.$anchor,'text'=>$label,'link'=>array('url'=>'#'.$anchor),'selected_icon'=>array('value'=>'','library'=>''));$node['settings']['icon_list']=$items;}
 if(!empty($node['elements']))$node['elements']=lk_rating_anchors($node['elements']);
 }return $nodes;}
$nodes=lk_rating_anchors($nodes);
foreach($nodes as &$section){
 if(str_contains($section['settings']['css_classes']??'','lk-detail-info')){
  $column=&$section['elements'][0];$old=array_values(array_filter($column['elements'],function($node){return ($node['widgetType']??'')!=='progress';}));$button=null;$new=array();
  foreach($old as $node){if(($node['widgetType']??'')==='button')$button=$node;else$new[]=$node;}
  foreach(lk_criteria_labels() as $key=>$label)$new[]=array('id'=>substr(md5('lk-criteria-'.$key),0,8),'elType'=>'widget','widgetType'=>'progress','settings'=>array('title'=>$label,'title_display'=>'yes','title_tag'=>'span','percent'=>array('size'=>0,'unit'=>'%'),'display_percentage'=>'show','inner_text'=>'','lk_binding'=>'criteria-'.$key,'_css_classes'=>'lk-rating-progress'),'elements'=>array());
  if($button)$new[]=$button;$column['elements']=$new;$rating=array_values(array_filter($new,function($node){return in_array($node['widgetType']??'',array('text-editor','progress'),true);}));unset($column);
 }
}unset($section);
foreach($nodes as &$section){
 $encoded=wp_json_encode($section);if(!str_contains($encoded,'"lk-reviews"')||count($section['elements'])!==1)continue;
 $section['settings']['css_classes'].=' lk-review-columns';$section['elements'][0]['settings']['_column_size']=66;
 $copy=$rating;foreach($copy as &$widget)$widget['id']=substr(md5('review-summary-'.$widget['id']),0,8);unset($widget);
 $section['elements'][]=array('id'=>'cd471fea','elType'=>'column','settings'=>array('_column_size'=>34,'css_classes'=>'lk-panel lk-review-summary'),'elements'=>$copy);
}unset($section);
update_post_meta($id,'_elementor_data',wp_slash(wp_json_encode($nodes,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)));delete_post_meta($id,'_elementor_css');delete_post_meta($id,'_elementor_element_cache');Elementor\Plugin::$instance->files_manager->clear_cache();echo 'Native rating bars and shared summary updated.',PHP_EOL;
$reference=json_decode(file_get_contents(LK_DIR.'assets/images/reference-content.json'),true);
foreach(get_option('lk_place_ids') as $slug=>$listing){if(get_post_meta($listing,'_lk_features_completed',true))continue;foreach($reference['places'][$slug]['benefits']??array() as $benefit){if($benefit['name']!=='Có giao hàng')continue;$term=term_exists('Có giao hàng','features');if(!$term)$term=wp_insert_term('Có giao hàng','features');if(!is_wp_error($term)){$term_id=(int)(is_array($term)?$term['term_id']:$term);wp_set_object_terms($listing,array($term_id),'features',true);update_term_meta($term_id,'_lk_icon','truck');}}update_post_meta($listing,'_lk_features_completed',1);}
