<?php
if(PHP_SAPI!=='cli')exit;
require dirname(__DIR__).'/wp-load.php';
$admins=get_users(array('role'=>'administrator','number'=>1));wp_set_current_user($admins[0]->ID);
$ids=get_option('lk_place_ids');$sample=$ids['ca-phe-me-bap'];
$template=(int)get_option('lk_template_listing');
if(!$template){
 $data=json_decode(get_post_meta($sample,'_elementor_data',true),true);
 if(!$data)throw new RuntimeException('Missing source document');
 $template=wp_insert_post(array('post_type'=>'elementor_library','post_title'=>'Long Khánh - Template chung địa điểm','post_status'=>'publish'));
 update_option('lk_template_listing',$template);update_option('lk_listing_preview',$sample);
}else $data=json_decode(get_post_meta($template,'_elementor_data',true),true);
function lk_shared_nodes($nodes,$ancestor=''){
 foreach($nodes as &$node){
  $s=&$node['settings'];$s['_element_cache']='no';$classes=$ancestor.' '.($s['css_classes']??$s['_css_classes']??'');
  if($node['elType']==='widget'){
   $type=$node['widgetType'];$binding='';
   if($type==='heading'&&($s['header_size']??'')==='h1')$binding='title';
   if($type==='icon-list'&&str_contains($classes,'lk-detail-links'))$binding='links';
   if($type==='icon-list'&&str_contains($classes,'lk-info-column'))$binding='info';
   if($type==='icon-list'&&str_contains($classes,'lk-benefits'))$binding='benefits';
   if($type==='google_maps')$binding='map';
   if($type==='image-gallery')$binding='gallery';
   if($type==='text-editor'&&str_contains($s['editor']??'','lk-score'))$binding='score';
   if($type==='text-editor'&&str_contains($s['editor']??'','lk-rating-message'))$binding='rating-message';
   if($type==='text-editor'&&str_contains($classes,'lk-space'))$binding='description';
   if($type==='button'&&str_contains($s['text']??'','Xem tất cả ảnh'))$binding='photos-button';
   if($type==='button'&&str_contains($s['text']??'','Đánh giá ngay'))$s['_css_classes']='lk-review-cta';
   if($type==='lk-listings')$s['nearby']='yes';
   if($binding){$s['lk_binding']=$binding;$s['_element_cache']='no';}
  }
  if(str_contains($s['css_classes']??'','lk-map-column'))$s['_element_id']='location';
  if(!empty($node['elements']))$node['elements']=lk_shared_nodes($node['elements'],$classes);
 }return $nodes;
}
function lk_store_template($id,$data){
 update_post_meta($id,'_lk_managed',1);update_post_meta($id,'_lk_shared_template',1);
 update_post_meta($id,'_elementor_edit_mode','builder');update_post_meta($id,'_elementor_template_type','section');update_post_meta($id,'_elementor_version',ELEMENTOR_VERSION);update_post_meta($id,'_wp_page_template','elementor_canvas');update_post_meta($id,'_elementor_data',wp_slash(wp_json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)));delete_post_meta($id,'_elementor_css');delete_post_meta($id,'_elementor_element_cache');
}
lk_store_template($template,lk_shared_nodes($data));
$gallery=(int)get_option('lk_template_listing_gallery');
$routes=get_option('lk_routes',array());
if(!$gallery){
 $old=$routes['dia-diem/ca-phe-me-bap/photo'];$data=json_decode(get_post_meta($old,'_elementor_data',true),true);
 $gallery=wp_insert_post(array('post_type'=>'elementor_library','post_title'=>'Long Khánh - Template chung album và menu','post_status'=>'publish'));update_option('lk_template_listing_gallery',$gallery);
}else $data=json_decode(get_post_meta($gallery,'_elementor_data',true),true);
function lk_gallery_nodes($nodes){foreach($nodes as &$n){if($n['elType']==='widget'){$binding=array('heading'=>'gallery-title','text-editor'=>'address','button'=>'back-button','image-gallery'=>'view-gallery')[$n['widgetType']]??'';if($binding){$n['settings']['lk_binding']=$binding;$n['settings']['_element_cache']='no';}}if(!empty($n['elements']))$n['elements']=lk_gallery_nodes($n['elements']);}return $nodes;}
lk_store_template($gallery,lk_gallery_nodes($data));
$media=get_option('lk_media_map');$reference=json_decode(file_get_contents(LK_DIR.'assets/images/reference-content.json'),true);
foreach($ids as $slug=>$id){
 $menus=array();foreach($media as $key=>$value)if(str_starts_with($key,'menu-'.$slug.'-')&&!empty($value['id']))$menus[]=$value['id'];update_post_meta($id,'_lk_menu_ids',$menus);
 $features=array();foreach($reference['places'][$slug]['benefits']??array() as $b){if($b['name']==='Có giao hàng')continue;$term=term_exists($b['name'],'features');if(!$term)$term=wp_insert_term($b['name'],'features');if(is_wp_error($term))continue;$tid=(int)(is_array($term)?$term['term_id']:$term);$features[]=$tid;$icon=str_replace(array('far fa-','fas fa-'),'',$b['icon']);$icon=strtr($icon,array('cars'=>'car','air-conditioner'=>'snowflake','sun-cloud'=>'sun','shipping-fast'=>'truck'));update_term_meta($tid,'_lk_icon',$icon);}
 wp_set_object_terms($id,$features,'features');
 $legacy=get_post_meta($id,'_elementor_data',true);if($legacy&&!get_post_meta($id,'_lk_legacy_elementor_data',true))update_post_meta($id,'_lk_legacy_elementor_data',wp_slash($legacy));
 if(!get_post_field('post_content',$id))wp_update_post(array('ID'=>$id,'post_content'=>'<p>'.esc_html(get_the_title($id)).' là địa điểm tại Long Khánh. Tham khảo hình ảnh, khoảng giá và giờ hoạt động trước khi ghé. Vui lòng liên hệ trực tiếp để xác nhận thông tin hiện tại.</p>'));
 delete_post_meta($id,'_elementor_data');delete_post_meta($id,'_elementor_edit_mode');delete_post_meta($id,'_wp_page_template');delete_post_meta($id,'_elementor_css');delete_post_meta($id,'_elementor_element_cache');
}
// Old per-place photo/menu pages are recoverable in Trash. The same public URLs
// are now routed through the shared album template, including future listings.
foreach($routes as $route=>$id){if(preg_match('#^dia-diem/[^/]+/(photo|menu)$#',$route)){if(get_post_status($id)!=='trash')wp_trash_post($id);unset($routes[$route]);}}
update_option('lk_routes',$routes);$support=array_values(array_diff(get_option('elementor_cpt_support',array('page','post')),array('listing')));update_option('elementor_cpt_support',$support);
lk_register_routes();flush_rewrite_rules(false);Elementor\Plugin::$instance->files_manager->clear_cache();
echo wp_json_encode(array('listing_template'=>$template,'gallery_template'=>$gallery,'preview_listing'=>$sample,'migrated'=>count($ids),'remaining_individual_routes'=>count($routes))),PHP_EOL;
