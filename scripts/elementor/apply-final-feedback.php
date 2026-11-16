<?php
/** Explicit local-only edit of allowlisted Elementor documents. */
if(PHP_SAPI!=='cli')exit(1);
require dirname(__DIR__,2).'/wp-load.php';
if(!in_array(wp_parse_url(home_url(),PHP_URL_HOST),array('localhost','127.0.0.1','::1'),true))throw new RuntimeException('Local only.');
foreach(array('listing','menu') as $key){
 $id=(int)get_option('lk_template_'.$key);$raw=get_post_meta($id,'_elementor_data',true);$nodes=json_decode($raw,true);
 if(!$nodes)throw new RuntimeException('Missing document: '.$key);
 if(!get_post_meta($id,'_lk_final_feedback_backup',true))update_post_meta($id,'_lk_final_feedback_backup',wp_slash($raw));
 $walk=function($items)use(&$walk,$key){foreach($items as &$node){
  $s=&$node['settings'];$classes=$s['css_classes']??$s['_css_classes']??'';
  if($key==='listing'&&str_contains($classes,'lk-map-column')&&!str_contains(wp_json_encode($node),'lk-intro-card')){
   $intro=array();$map=array();$in_map=false;
   foreach($node['elements'] as $child){
    if(($child['widgetType']??'')==='heading'&&str_contains($child['settings']['title']??'','Địa điểm cụ thể'))$in_map=true;
    if(($child['settings']['lk_binding']??'')==='directions'){$child['settings']['lk_binding']='directions-address';$child['settings']['_css_classes']='lk-map-address';}
    if($in_map)$map[]=$child;else $intro[]=$child;
   }
   $wrap=function($children,$name){return array('id'=>substr(md5($name),0,8),'elType'=>'section','isInner'=>true,'settings'=>array('css_classes'=>$name,'layout'=>'full_width','gap'=>'no'),'elements'=>array(array('id'=>substr(md5($name.'col'),0,8),'elType'=>'column','isInner'=>true,'settings'=>array('_column_size'=>100),'elements'=>$children)));};
   $node['elements']=array($wrap($intro,'lk-intro-card'),$wrap($map,'lk-location-card'));
  }
  if($key==='menu'&&($node['widgetType']??'')==='button'&&($s['text']??'')==='Đăng nhập / Đăng ký')$s['_css_classes']='lk-menu-account';
  if(!empty($node['elements']))$node['elements']=$walk($node['elements']);
 }return $items;};$nodes=$walk($nodes);
 if($key==='menu'&&!str_contains(wp_json_encode($nodes),'lk-menu-logout'))$nodes[0]['elements'][0]['elements'][]=array('id'=>'lkflogout','elType'=>'widget','widgetType'=>'button','settings'=>array('text'=>'Đăng xuất','_css_classes'=>'lk-menu-logout'),'elements'=>array());
 update_post_meta($id,'_elementor_data',wp_slash(wp_json_encode($nodes,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)));
 delete_post_meta($id,'_elementor_css');delete_post_meta($id,'_elementor_element_cache');
 echo $key." updated locally\n";
}
if(class_exists('Elementor\Plugin'))\Elementor\Plugin::$instance->files_manager->clear_cache();
