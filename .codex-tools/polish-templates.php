<?php
if(PHP_SAPI!=='cli')exit;
require dirname(__DIR__).'/wp-load.php';
$users=get_users(array('role'=>'administrator','number'=>1));wp_set_current_user($users[0]->ID);
function lk_polish_nodes($nodes,$ancestors=''){
 foreach($nodes as &$n){
  $s=&$n['settings'];$classes=$ancestors.' '.($s['css_classes']??$s['_css_classes']??'');
  if(!empty($s['icon_list']))foreach($s['icon_list'] as &$item)if(($item['selected_icon']['value']??'')==='fas fa-hand-holding-box')$item['selected_icon']['value']='fas fa-shopping-bag';unset($item);
  if(($n['widgetType']??'')==='icon-list'&&str_contains($classes,'lk-map-column'))$s['lk_binding']='directions';
  if(($n['widgetType']??'')==='heading'&&str_contains($classes,'lk-hero'))$s['lk_typewriter_lines']='Viết Review - Chia sẻ cùng cộng đồng';
  if(($n['widgetType']??'')==='lk-listings'&&!empty($s['nearby']))$s['_element_id']='related';
  if(str_contains($s['css_classes']??'','lk-detail-info'))$s['_element_id']='detail';
  if(!empty($n['elements']))$n['elements']=lk_polish_nodes($n['elements'],$classes);
 }
 return $nodes;
}
$docs=get_posts(array('post_type'=>array('page','post','elementor_library'),'post_status'=>'publish','meta_key'=>'_lk_managed','meta_value'=>1,'posts_per_page'=>-1));
foreach($docs as $doc){
 $data=json_decode(get_post_meta($doc->ID,'_elementor_data',true),true);if(!$data)continue;
 $data=lk_polish_nodes($data);
 if($doc->ID==(int)get_option('lk_template_listing')){
  $exists=false;foreach($data as $node)if(str_contains($node['settings']['css_classes']??'','lk-detail-tabs'))$exists=true;
  if(!$exists){
   $items=array();foreach(array('detail'=>'Thông tin','reviews'=>'Đánh giá','related'=>'Lân cận') as $anchor=>$text)$items[]=array('_id'=>'lk-tab-'.$anchor,'text'=>$text,'link'=>array('url'=>'#'.$anchor),'selected_icon'=>array('value'=>'','library'=>''));
   $data[]=array('id'=>'lktabs01','elType'=>'section','settings'=>array('css_classes'=>'lk-section lk-detail-tabs','gap'=>'no','layout'=>'boxed','content_width'=>array('size'=>1160,'unit'=>'px')),'elements'=>array(array('id'=>'lktabs02','elType'=>'column','settings'=>array('_column_size'=>100),'elements'=>array(array('id'=>'lktabs03','elType'=>'widget','widgetType'=>'icon-list','settings'=>array('icon_list'=>$items),'elements'=>array())))));
  }
 }
 if($doc->ID==(int)get_option('lk_template_listing_gallery')){
  $encoded=wp_json_encode($data);
  if(!str_contains($encoded,'gallery-status'))$data[0]['elements'][0]['elements'][]=array('id'=>'lkempty1','elType'=>'widget','widgetType'=>'text-editor','settings'=>array('editor'=>'','lk_binding'=>'gallery-status'),'elements'=>array());
 }
 update_post_meta($doc->ID,'_elementor_data',wp_slash(wp_json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)));delete_post_meta($doc->ID,'_elementor_css');delete_post_meta($doc->ID,'_elementor_element_cache');
}
Elementor\Plugin::$instance->files_manager->clear_cache();
echo 'Polished '.count($docs).' documents.',PHP_EOL;
