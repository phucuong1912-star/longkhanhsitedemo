<?php
if(!defined('ABSPATH'))exit;

/** Use the card's destination, never its old independently saved region. */
function lk_region_card_query($url){
 if(!is_string($url)||$url==='')return null;
 $url=html_entity_decode($url,ENT_QUOTES,'UTF-8');
 $home=wp_parse_url(home_url('/'));
 if(strpos($url,'/')===0&&strpos($url,'//')!==0)$url=$home['scheme'].'://'.$home['host'].(isset($home['port'])?':'.$home['port']:'').$url;
 $parts=wp_parse_url($url);
 if(!$parts||!in_array($parts['scheme']??'',array('http','https'),true)||isset($parts['user'])||isset($parts['pass'])||strtolower($parts['host']??'')!==strtolower($home['host']??'')||($parts['port']??null)!==($home['port']??null))return null;
 $paths=array(wp_parse_url(lk_link('search'),PHP_URL_PATH));
 $map=get_option('lk_page_map',array());$search=is_array($map)?absint($map['search']??0):0;
 if($search&&get_post_type($search)==='page'&&get_post_status($search)==='publish')$paths[]=wp_parse_url(get_permalink($search),PHP_URL_PATH);
 if(!in_array(untrailingslashit($parts['path']??''),array_map('untrailingslashit',$paths),true))return null;
 parse_str($parts['query']??'',$query);
 $regions=array_values(array_filter(array_map('sanitize_title',array_filter((array)($query['regions']??array()),'is_scalar'))));
 if(!$regions)return null;
 // Match the search endpoint's filters without inheriting the current request.
 $query=array_intersect_key($query,array_flip(array('q','categories','regions','features','price_status','open','min_price','max_price')));
 $query['regions']=array_values(array_unique($regions));
 return $query;
}
function lk_region_card_count($query){
 $previous=$_GET;
 try{$_GET=$query;return count(lk_filtered_posts(lk_search_state()));}
 finally{$_GET=$previous;}
}
function lk_region_card_heading_query($nodes){
 foreach($nodes as $node){
  if(($node['widgetType']??'')==='heading'){
   $query=lk_region_card_query($node['settings']['link']['url']??'');if($query!==null)return $query;
  }
  // A nested card owns its own destination.
  $classes=preg_split('/\s+/',trim(($node['settings']['css_classes']??'').' '.($node['settings']['_css_classes']??'')));
  if(!in_array('lk-region-card',$classes,true)&&!empty($node['elements'])){
   $query=lk_region_card_heading_query($node['elements']);if($query!==null)return $query;
  }
 }
 return null;
}
function lk_bind_region_card_nodes($nodes,$query=null){
 foreach($nodes as &$node){
  $settings=$node['settings']??array();
  $classes=preg_split('/\s+/',trim(($settings['css_classes']??'').' '.($settings['_css_classes']??'')));
  $current=in_array('lk-region-card',$classes,true)?lk_region_card_heading_query($node['elements']??array()):$query;
  if($current!==null&&($node['widgetType']??'')==='text-editor'&&(($settings['lk_binding']??'')==='region-count'||preg_match('/^\d+\s+địa điểm(?:\s+demo)?$/u',trim(wp_strip_all_tags($settings['editor']??''))))){
   $settings['lk_binding']='region-count';$settings['_lk_region_link_query']=$current;
   $node['settings']=lk_resolve_listing_settings('text-editor',$settings,0);
  }
  if(!empty($node['elements']))$node['elements']=lk_bind_region_card_nodes($node['elements'],$current);
 }
 return $nodes;
}
// Render-time copies only: no save, migration or homepage metadata update.
add_filter('elementor/frontend/builder_content_data','lk_bind_region_card_nodes',15);
function lk_region_card_has_nodes($nodes){
 foreach($nodes as $node){
  $classes=preg_split('/\s+/',trim(($node['settings']['css_classes']??'').' '.($node['settings']['_css_classes']??'')));
  if(in_array('lk-region-card',$classes,true)||lk_region_card_has_nodes($node['elements']??array()))return true;
 }
 return false;
}
function lk_region_card_cache_ttl($value){
 $document=class_exists('Elementor\Plugin')?\Elementor\Plugin::$instance->documents->get_current():null;
 // The document cache stores old widget settings, including the old region.
 // Only documents containing region cards need fresh destination-based counts.
 return $document&&lk_region_card_has_nodes($document->get_elements_data())?'disable':$value;
}
add_filter('option_elementor_element_cache_ttl','lk_region_card_cache_ttl',20);
add_filter('default_option_elementor_element_cache_ttl','lk_region_card_cache_ttl',20);
add_action('wp_enqueue_scripts',function(){
 if(!function_exists('lk_is_site')||!lk_is_site())return;
 wp_enqueue_style('lk-region-cards',LK_URL.'assets/region-cards.css',array('lk-site'),filemtime(LK_DIR.'assets/region-cards.css'));
 wp_enqueue_script('lk-region-cards',LK_URL.'assets/region-cards.js',array('lk-motion'),filemtime(LK_DIR.'assets/region-cards.js'),true);
},40);
