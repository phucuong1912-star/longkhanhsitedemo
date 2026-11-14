<?php
if (!defined('ABSPATH')) exit;

function lk_f2_blog_group(){
 $group=is_scalar($_GET['blog_group']??null)?sanitize_key($_GET['blog_group']):'';
 return in_array($group,array('an-uong','du-lich','giai-tri'),true)?$group:'';
}
add_filter('body_class',function($classes){if(is_page('blog')&&lk_f2_blog_group())$classes[]='lk-blog-category';return $classes;});
function lk_f2_blog_cache_ttl($value){return did_action('wp')&&is_page('blog')?'disable':$value;}
add_filter('option_elementor_element_cache_ttl','lk_f2_blog_cache_ttl',30);
add_filter('default_option_elementor_element_cache_ttl','lk_f2_blog_cache_ttl',30);
// Category views reuse the native Elementor sections from the Blog document.
// Editing a card once updates both the carousel and its full category view.
add_filter('elementor/frontend/builder_content_data',function($nodes,$document_id){
 $group=lk_f2_blog_group();if(!$group)return $nodes;$page=get_page_by_path('blog');
 if(!$page||(int)$document_id!==$page->ID||!$group)return $nodes;
 $filtered=array();foreach($nodes as $node){$classes=$node['settings']['css_classes']??'';if(str_contains($classes,'lk-blog-hero')||str_contains($classes,'lk-blog-group-'.$group))$filtered[]=$node;}
 $walk=function($items)use(&$walk){foreach($items as &$item){if(str_contains($item['settings']['_css_classes']??'','lk-blog-more')){$item['settings']['text']='Tất cả bài viết';$item['settings']['link']=array('url'=>home_url('/blog/'));}if(!empty($item['elements']))$item['elements']=$walk($item['elements']);}return $items;};
 return $filtered?$walk($filtered):$nodes;
},30,2);
add_action('wp_loaded',function(){
 if(get_option('lk_feedback2_blog_groups')||!(PHP_SAPI==='cli'||current_user_can('manage_options')))return;
 $page=get_page_by_path('blog');if(!$page)return;$raw=get_post_meta($page->ID,'_elementor_data',true);$nodes=json_decode($raw,true);if(!$nodes)return;
 if(!get_post_meta($page->ID,'_lk_feedback2_backup',true))update_post_meta($page->ID,'_lk_feedback2_backup',wp_slash($raw));
 $group='';foreach($nodes as &$node){$classes=$node['settings']['css_classes']??'';
  if(str_contains($classes,'lk-blog-group-heading')){$text=wp_json_encode($node,JSON_UNESCAPED_UNICODE);$group=str_contains($text,'Cẩm nang ăn uống')?'an-uong':(str_contains($text,'Cẩm nang du lịch')?'du-lich':(str_contains($text,'Vui chơi và giải trí')?'giai-tri':''));
   if($group){$node['settings']['css_classes']=$classes.' lk-blog-group-'.$group;$walk=function($items)use(&$walk,$group){foreach($items as &$item){if(str_contains($item['settings']['_css_classes']??'','lk-blog-more'))$item['settings']['link']=array('url'=>add_query_arg('blog_group',$group,home_url('/blog/')));if(!empty($item['elements']))$item['elements']=$walk($item['elements']);}return $items;};$node['elements']=$walk($node['elements']);}
  }elseif($group&&str_contains($classes,'lk-blog-row')){$node['settings']['css_classes']=$classes.' lk-blog-group-'.$group;$group='';}
 }unset($node);
 update_post_meta($page->ID,'_elementor_data',wp_slash(wp_json_encode($nodes,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)));delete_post_meta($page->ID,'_elementor_element_cache');delete_post_meta($page->ID,'_elementor_css');update_option('lk_feedback2_blog_groups',1);
},40);

// ListingPro blocks subscriber requests to all wp-admin endpoints, including
// admin-post.php. Exempt only our authenticated, nonce-checked frontend forms
// for this request. This does not grant capabilities or expose admin screens.
add_action('init',function(){
 if(!is_user_logged_in()||($GLOBALS['pagenow']??'')!=='admin-post.php'||($_SERVER['REQUEST_METHOD']??'')!=='POST')return;
 $actions=array('lk_add_place'=>'lk_add-place','lk_review'=>'lk_review','lk_contact'=>'lk_contact','lk_login'=>'lk_login');
 $action=is_scalar($_POST['action']??null)?sanitize_key($_POST['action']):'';
 $nonce=is_scalar($_POST['lk_nonce']??null)?sanitize_text_field(wp_unslash($_POST['lk_nonce'])):'';
 if(isset($actions[$action])&&wp_verify_nonce($nonce,$actions[$action]))remove_action('init','listingpro_block_admin_access');
},1);

add_action('admin_post_lk_add_place',function(){
 lk_require_nonce('lk_add-place');
 // The parent registers 29 thumbnail sizes. Only generate the sizes used by
 // this directory and the native dashboard during a frontend submission.
 add_filter('intermediate_image_sizes_advanced',function($sizes,$metadata,$attachment_id){
  $parent=(int)get_post_field('post_parent',$attachment_id);
  if(get_post_type($parent)!=='listing'||!get_post_meta($parent,'_lk_managed',true))return $sizes;
  return array_intersect_key($sizes,array_flip(array('thumbnail','medium','medium_large','large','listingpro-listing-grid','listingpro-list-thumb')));
 },10,3);
},2);

add_action('init',function(){
 register_taxonomy('lk_collection','listing',array('labels'=>array('name'=>'Bộ sưu tập','singular_name'=>'Bộ sưu tập','add_new_item'=>'Thêm bộ sưu tập'),'public'=>false,'show_ui'=>true,'show_admin_column'=>true,'show_in_rest'=>true,'hierarchical'=>true,'rewrite'=>false));
},30);

function lk_f2_features($category){
 $ids=array();
 if(function_exists('listingpro_get_term_meta'))$ids=(array)listingpro_get_term_meta($category,'lp_category_tags');
 $ids=array_filter(array_map('absint',$ids));
 foreach(get_posts(array('post_type'=>'listing','post_status'=>'publish','posts_per_page'=>-1,'tax_query'=>array(array('taxonomy'=>'listing-category','field'=>'term_id','terms'=>$category)))) as $p)$ids=array_merge($ids,wp_get_object_terms($p->ID,'features',array('fields'=>'ids')));
 $ids=array_unique(array_filter($ids));
 return $ids?get_terms(array('taxonomy'=>'features','include'=>$ids,'hide_empty'=>false)):array();
}
add_action('lk_add_place_taxonomies',function($settings=array()){
 echo '<fieldset class="lk-submission-features"><legend>'.esc_html($settings['features_title']??'Tiện ích').'</legend>';
 foreach(lk_directory_terms('listing-category') as $category){echo '<div data-amenity-category="'.(int)$category->term_id.'" hidden>';foreach(lk_f2_features($category->term_id) as $term)echo '<label class="lk-inline"><input type="checkbox" name="features[]" value="'.(int)$term->term_id.'" disabled> '.esc_html($term->name).'</label>';echo '</div>';}
 echo '<p class="lk-amenity-hint">Chọn danh mục để xem tiện ích phù hợp.</p></fieldset><fieldset><legend>'.esc_html($settings['collections_title']??'Bộ sưu tập').'</legend>';
 foreach(get_terms(array('taxonomy'=>'lk_collection','hide_empty'=>false)) as $t)echo '<label class="lk-inline"><input type="checkbox" name="collections[]" value="'.(int)$t->term_id.'"> '.esc_html($t->name).'</label>';
 echo '</fieldset>';
});
add_action('lk_add_place_media_before',function($settings=array()){
 lk_input('video',$settings['video_title']??'Video giới thiệu (không bắt buộc)','url',false,'https://youtu.be/...');
});
add_action('lk_add_place_fields',function($settings=array()){
 echo '<label>'.esc_html($settings['featured_title']??'Ảnh nổi bật').'<input type="file" name="featured_photo" accept="image/jpeg,image/png,image/webp"><small>JPG, PNG hoặc WebP, tối đa 5 MB.</small></label>';
});
add_action('lk_place_created',function($id){
 $allowed=wp_list_pluck(lk_f2_features(absint($_POST['category']??0)),'term_id');
 wp_set_object_terms($id,array_values(array_intersect($allowed,array_map('absint',(array)($_POST['features']??array())))),'features');
 $terms=array_filter(array_map('absint',(array)($_POST['collections']??array())),function($t){return term_exists($t,'lk_collection');});wp_set_object_terms($id,array_values($terms),'lk_collection');
 $options=get_post_meta($id,'lp_listingpro_options',true);$options['video']=esc_url_raw(wp_unslash($_POST['video']??''));update_post_meta($id,'lp_listingpro_options',$options);
 if(!empty($_FILES['featured_photo']['name'])){require_once ABSPATH.'wp-admin/includes/file.php';require_once ABSPATH.'wp-admin/includes/media.php';require_once ABSPATH.'wp-admin/includes/image.php';$photo=media_handle_upload('featured_photo',$id);if(!is_wp_error($photo))set_post_thumbnail($id,$photo);}
});
add_action('admin_post_lk_add_place',function(){
 if(empty($_FILES['featured_photo']['name']))return;
 lk_require_nonce('lk_add-place');$f=$_FILES['featured_photo'];$type=wp_check_filetype_and_ext($f['tmp_name'],$f['name'],array('jpg|jpeg'=>'image/jpeg','png'=>'image/png','webp'=>'image/webp'));
 if($f['error']!==UPLOAD_ERR_OK||$f['size']>5*MB_IN_BYTES||empty($type['type']))wp_die('Ảnh nổi bật chưa hợp lệ. Chọn JPG, PNG hoặc WebP dưới 5 MB.','Không thể gửi',array('response'=>400));
},1);

add_action('wp_enqueue_scripts',function(){
 if(is_page_template('template-dashboard.php')&&($_GET['dashboard']??'')==='menus')wp_enqueue_script('lk-dashboard-menu',get_stylesheet_directory_uri().'/assets/dashboard-menu.js',array(),filemtime(__DIR__.'/../assets/dashboard-menu.js'),true);
 if(!lk_is_site())return;
 wp_enqueue_style('lk-feedback2',get_stylesheet_directory_uri().'/assets/feedback2.css',array('lk-elementor-controls'),filemtime(__DIR__.'/../assets/feedback2.css'));
 wp_enqueue_style('lk-location-map');
 wp_enqueue_script('lk-location-leaflet');
 wp_enqueue_script('lk-feedback2',get_stylesheet_directory_uri().'/assets/feedback2.js',array('lk-results','lk-location-leaflet'),filemtime(__DIR__.'/../assets/feedback2.js'),true);
},30);

function lk_f2_place_actions(){
 $id=lk_context_id();if(!$id||get_query_var('lk_view'))return;
 $d=lk_place_data($id);$destination=$d['lat']!==null&&$d['lng']!==null?$d['lat'].','.$d['lng']:$d['address'];
 echo '<nav class="lk-detail-actions" aria-label="Thao tác địa điểm">'.($d['phone']?'<a href="tel:'.esc_attr(preg_replace('/[^+0-9]/','',$d['phone'])).'">'.lk_icon('phone-alt').'<span>Gọi điện</span></a>':'<span aria-disabled="true">'.lk_icon('phone-alt').'<span>Chưa có số điện thoại</span></span>').'<a class="lk-central-action" target="_blank" rel="noopener" href="'.esc_url('https://www.google.com/maps/dir/?api=1&destination='.rawurlencode($destination)).'">'.lk_icon('directions').'<span>Chỉ đường</span></a><button type="button" data-place-share>'.lk_icon('share-alt').'<span>Chia sẻ</span></button></nav>';
}
add_filter('body_class',function($classes){if(is_singular('listing')&&!get_query_var('lk_view'))$classes[]='lk-f2-detail';return $classes;});
add_filter('body_class',function($classes){if(is_page('profile')&&!is_user_logged_in())$classes[]='lk-f2-account-page';return $classes;});
add_action('admin_bar_menu',function($bar){if(is_user_logged_in())$bar->add_node(array('id'=>'lk-dashboard','title'=>'Bảng điều khiển địa điểm','href'=>home_url('/listing-author/')));},100);
add_filter('elementor/widget/render_content',function($html,$widget){if($widget->get_name()==='button'&&$widget->get_settings('lk_binding')==='save-button'&&lk_context_id())return preg_replace('/<a\b/','<a role="button" data-save="'.(int)lk_context_id().'" aria-label="Lưu địa điểm" aria-pressed="false"',$html,1);return $html;},30,2);
add_filter('elementor/frontend/widget/should_render',function($render,$widget){if($widget->get_name()==='video'&&$widget->get_settings('lk_binding')==='listing-video'){$options=get_post_meta(lk_context_id(),'lp_listingpro_options',true);return $render&&!empty($options['video']);}return $render;},20,2);
add_action('add_meta_boxes_listing',function(){add_meta_box('lk-video','Video giới thiệu',function($post){$o=get_post_meta($post->ID,'lp_listingpro_options',true);wp_nonce_field('lk_video','lk_video_nonce');echo '<label for="lk-video-url">URL YouTube / Vimeo</label><input class="widefat" id="lk-video-url" type="url" name="lk_video_url" value="'.esc_attr(is_array($o)?($o['video']??''):'').'">';},'listing','side');});
add_action('save_post_listing',function($id){if(wp_is_post_revision($id)||!current_user_can('edit_post',$id)||empty($_POST['lk_video_nonce'])||!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['lk_video_nonce'])),'lk_video'))return;$o=get_post_meta($id,'lp_listingpro_options',true);$o=is_array($o)?$o:array();$o['video']=esc_url_raw(wp_unslash($_POST['lk_video_url']??''));update_post_meta($id,'lp_listingpro_options',$o);},100);

// Versioned changes to native Elementor documents; take a recoverable snapshot.
// Run once on an administrator request (or CLI), never on anonymous traffic.
add_action('wp_loaded',function(){
 if(get_option('lk_feedback2_revision')==='3'||!(PHP_SAPI==='cli'||current_user_can('manage_options')))return;
 foreach(array('Ăn sáng','Ăn trưa','Ăn chiều','Ăn vặt') as $name)if(!term_exists($name,'lk_collection'))wp_insert_term($name,'lk_collection');
 $ids=array_filter(array(get_option('lk_template_header'),get_option('lk_template_menu'),get_option('lk_template_listing')));
 foreach($ids as $id){$raw=get_post_meta($id,'_elementor_data',true);$nodes=json_decode($raw,true);if(!$nodes)continue;
 if(!get_post_meta($id,'_lk_feedback2_backup',true))update_post_meta($id,'_lk_feedback2_backup',wp_slash($raw));
 $walk=function($ns,$anc='')use(&$walk){foreach($ns as &$n){$s=&$n['settings'];$cls=$anc.' '.($s['css_classes']??$s['_css_classes']??'');
 if(str_contains($cls,'lk-bottom-nav')&&($n['widgetType']??'')==='icon-list'){$s['icon_list']=array();foreach(array(array('Trang chủ','','home'),array('Blog','blog/','rss'),array('Gần tôi','search/?nearby=me','map-marker-alt'),array('Khuyến mãi','promo/','percent'),array('Tài khoản','profile/','user')) as $i=>$item)$s['icon_list'][]=array('_id'=>'f2nav'.$i,'text'=>$item[0],'link'=>array('url'=>home_url('/'.$item[1])),'selected_icon'=>array('value'=>'fas fa-'.$item[2],'library'=>'fa-solid'));}
 if(($n['widgetType']??'')==='lk-listings'&&!empty($s['nearby'])){$s['more']='Xem thêm';$s['tabs']='';$s['_css_classes']='lk-nearby-simple';}
 if(($n['widgetType']??'')==='heading'&&str_contains($cls,'lk-detail-top'))$s['typography_font_weight']='600';
 if(!empty($n['elements']))$n['elements']=$walk($n['elements'],$cls);
 }return $ns;};$nodes=$walk($nodes);
 // Put introduction immediately before the map while preserving editable widgets.
 if((int)$id===(int)get_option('lk_template_listing')){
 if(!str_contains(wp_json_encode($nodes),'lk-place-actions'))$nodes[]=array('id'=>'f2actions','elType'=>'section','settings'=>array('css_classes'=>'lk-detail-action-shell'),'elements'=>array(array('id'=>'f2actcol','elType'=>'column','settings'=>array('_column_size'=>100),'elements'=>array(array('id'=>'f2actwidget','elType'=>'widget','widgetType'=>'lk-place-actions','settings'=>array(),'elements'=>array())))));
 $intro=null;foreach($nodes as $k=>$n)if(str_contains($n['settings']['css_classes']??'','lk-space')){$intro=$n;unset($nodes[$k]);}
 if($intro)foreach($nodes as &$n)if(str_contains($n['settings']['css_classes']??'','lk-detail-info')){foreach($n['elements'] as &$col)if(str_contains($col['settings']['css_classes']??'','lk-map-column')){$col['elements']=array_merge($intro['elements'][0]['elements'],$col['elements']);}unset($col);}unset($n);
 }
 update_post_meta($id,'_elementor_data',wp_slash(wp_json_encode(array_values($nodes),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)));delete_post_meta($id,'_elementor_css');delete_post_meta($id,'_elementor_element_cache');
 }
 if(class_exists('Elementor\Plugin'))\Elementor\Plugin::$instance->files_manager->clear_cache();
 update_option('lk_feedback2_revision','3');
});

add_action('wp_loaded',function(){
 if(get_option('lk_feedback2_save_widget')||!(PHP_SAPI==='cli'||current_user_can('manage_options')))return;
 $id=(int)get_option('lk_template_listing');$raw=get_post_meta($id,'_elementor_data',true);$nodes=json_decode($raw,true);if(!$nodes)return;
 if(!str_contains($raw,'save-button'))foreach($nodes as &$node)if(str_contains($node['settings']['css_classes']??'','lk-detail-top'))$node['elements'][0]['elements'][]=array('id'=>'f2save','elType'=>'widget','widgetType'=>'button','settings'=>array('text'=>'Lưu địa điểm','lk_binding'=>'save-button','selected_icon'=>array('value'=>'far fa-bookmark','library'=>'fa-regular'),'_css_classes'=>'lk-heading-save','button_text_color'=>'#52ab5c','background_color'=>'#FFFFFF00'),'elements'=>array());unset($node);
 update_post_meta($id,'_elementor_data',wp_slash(wp_json_encode($nodes,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)));delete_post_meta($id,'_elementor_css');delete_post_meta($id,'_elementor_element_cache');update_option('lk_feedback2_save_widget',1);
});

add_action('wp_loaded',function(){
 if(get_option('lk_template_login')||!(PHP_SAPI==='cli'||current_user_can('manage_options')))return;
 $id=wp_insert_post(array('post_type'=>'elementor_library','post_status'=>'publish','post_title'=>'Long Khánh - Popup đăng nhập','post_name'=>'long-khanh-popup-dang-nhap'));
 if(is_wp_error($id)||!$id)return;
 $nodes=array(array('id'=>'f2106101','elType'=>'section','settings'=>array('css_classes'=>'lk-login-content','layout'=>'full_width','gap'=>'no'),'elements'=>array(array('id'=>'f2106102','elType'=>'column','settings'=>array('_column_size'=>100),'elements'=>array(array('id'=>'f2106103','elType'=>'widget','widgetType'=>'lk-account','settings'=>array('display_mode'=>'login','login_title'=>'Đăng nhập tài khoản','show_social'=>'yes','_css_classes'=>'lk-login-elementor-form'),'elements'=>array()))))));
 foreach(array('_lk_managed'=>1,'_elementor_edit_mode'=>'builder','_elementor_template_type'=>'section','_wp_page_template'=>'elementor_canvas','_elementor_version'=>ELEMENTOR_VERSION) as $key=>$value)update_post_meta($id,$key,$value);
 update_post_meta($id,'_elementor_data',wp_slash(wp_json_encode($nodes,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)));update_option('lk_template_login',$id);
});

add_action('wp_loaded',function(){
 if(get_option('lk_feedback2_login_defaults')||!(PHP_SAPI==='cli'||current_user_can('manage_options')))return;
 $id=(int)get_option('lk_template_login');$nodes=json_decode(get_post_meta($id,'_elementor_data',true),true);if(!$nodes)return;
 $nodes[0]['settings']['css_classes']='lk-login-content lk-login-body';
 $nodes[0]['elements'][0]['elements'][0]['settings']+=array('radius'=>array('size'=>4,'unit'=>'px'),'field_height'=>array('size'=>40,'unit'=>'px'),'field_gap'=>array('size'=>20,'unit'=>'px'),'field_border'=>'#717171','login_heading_typography'=>'custom','login_heading_font_size'=>array('size'=>21,'unit'=>'px'),'login_heading_font_weight'=>'500');
 update_post_meta($id,'_elementor_data',wp_slash(wp_json_encode($nodes,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)));delete_post_meta($id,'_elementor_css');delete_post_meta($id,'_elementor_element_cache');update_option('lk_feedback2_login_defaults',1);
});

add_action('wp_loaded',function(){
 if(get_option('lk_feedback2_video_widget')||!(PHP_SAPI==='cli'||current_user_can('manage_options')))return;
 $id=(int)get_option('lk_template_listing');$raw=get_post_meta($id,'_elementor_data',true);$nodes=json_decode($raw,true);if(!$nodes)return;
 $walk=function($ns)use(&$walk){$result=array();foreach($ns as $node){if(!empty($node['elements']))$node['elements']=$walk($node['elements']);$result[]=$node;if(($node['widgetType']??'')==='text-editor'&&($node['settings']['lk_binding']??'')==='description')$result[]=array('id'=>'f2107201','elType'=>'widget','widgetType'=>'video','settings'=>array('lk_binding'=>'listing-video','video_type'=>'youtube','youtube_url'=>'','yt_privacy'=>'yes','lazy_load'=>'yes','_css_classes'=>'lk-place-video'),'elements'=>array());}return $result;};
 if(!str_contains($raw,'listing-video'))$nodes=$walk($nodes);
 update_post_meta($id,'_elementor_data',wp_slash(wp_json_encode($nodes,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)));delete_post_meta($id,'_elementor_css');delete_post_meta($id,'_elementor_element_cache');update_option('lk_feedback2_video_widget',1);
});

add_action('elementor/widgets/register',function($manager){
 class LK_F2_Actions extends LK_Widget {
  public function get_name(){return 'lk-place-actions';}
  public function get_title(){return 'Thanh thao tác địa điểm mobile';}
  protected function register_controls(){$this->start_controls_section('content',array('label'=>'Thao tác'));foreach(array('call_label'=>'Gọi điện','directions_label'=>'Chỉ đường','share_label'=>'Chia sẻ') as $key=>$value)$this->add_control($key,array('type'=>'text','label'=>$value,'default'=>$value));$this->end_controls_section();$this->appearance();$this->button_style();$this->start_controls_section('action_style',array('label'=>'Thanh thao tác','tab'=>\Elementor\Controls_Manager::TAB_STYLE));$this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'action_typography','label'=>'Chữ trên thanh','selector'=>'{{WRAPPER}} .lk-detail-actions>a, {{WRAPPER}} .lk-detail-actions>button, {{WRAPPER}} .lk-detail-actions>span'));$this->add_responsive_control('action_icon_size',array('label'=>'Cỡ biểu tượng','type'=>'slider','range'=>array('px'=>array('min'=>16,'max'=>36)),'selectors'=>array('{{WRAPPER}}'=>'--lk-action-icon: {{SIZE}}{{UNIT}};')));$this->end_controls_section();}
  protected function render(){$s=$this->get_settings_for_display();ob_start();lk_f2_place_actions();$html=ob_get_clean();foreach(array('call_label'=>'Gọi điện','directions_label'=>'Chỉ đường','share_label'=>'Chia sẻ') as $key=>$value)$html=str_replace('<span>'.$value.'</span>','<span>'.esc_html($s[$key]??$value).'</span>',$html);echo $html;}
 }
 $manager->register(new LK_F2_Actions());
 class LK_F2_Related extends LK_Widget {
  public function get_name(){return 'lk-related-posts';}
  public function get_title(){return 'Bài viết liên quan';}
  protected function register_controls(){$this->start_controls_section('content',array('label'=>'Bài viết liên quan'));$this->add_control('title',array('label'=>'Tiêu đề','type'=>'text','default'=>'Bài viết liên quan'));$this->add_control('limit',array('label'=>'Số bài','type'=>'number','default'=>4,'min'=>1,'max'=>12));$this->end_controls_section();$this->appearance();}
  protected function render(){$s=$this->get_settings_for_display();$id=get_queried_object_id();$args=array('post_type'=>array('post','page'),'post_status'=>'publish','posts_per_page'=>max(1,min(12,(int)$s['limit'])),'post__not_in'=>array($id));$blog_ids=array_values((array)get_option('lk_blog_ids',array()));if(in_array($id,$blog_ids)){$related_ids=array_values(array_diff($blog_ids,array($id)));if(!$related_ids)return;$args['post__in']=$related_ids;}$cats=wp_get_post_categories($id);if($cats)$args['category__in']=$cats;$posts=get_posts($args);if(!$posts)return;echo '<div class="lk-article-related"><h2>'.esc_html($s['title']).'</h2>';foreach($posts as $post)echo '<a href="'.esc_url(get_permalink($post)).'">'.get_the_post_thumbnail($post,'medium',array('loading'=>'lazy')).'<span>'.esc_html(get_the_title($post)).'</span></a>';echo '</div>';}
 }
 $manager->register(new LK_F2_Related());
},110);

add_action('wp_loaded',function(){
 if(get_option('lk_feedback2_blog_revision')==='2'||!(PHP_SAPI==='cli'||current_user_can('manage_options')))return;
 if(!get_option('lk_blog_ids'))return;
 foreach(get_posts(array('post_type'=>array('post','page'),'post_status'=>'publish','post__in'=>array_values((array)get_option('lk_blog_ids',array())),'posts_per_page'=>-1)) as $post){$raw=get_post_meta($post->ID,'_elementor_data',true);$nodes=json_decode($raw,true);if(!$nodes||str_contains($raw,'lk-related-posts'))continue;if(!get_post_meta($post->ID,'_lk_feedback2_backup',true))update_post_meta($post->ID,'_lk_feedback2_backup',wp_slash($raw));$nodes[]=array('id'=>'f2related','elType'=>'section','settings'=>array('css_classes'=>'lk-section lk-article','layout'=>'boxed'),'elements'=>array(array('id'=>'f2relcol','elType'=>'column','settings'=>array('_column_size'=>100),'elements'=>array(array('id'=>'f2relwidget','elType'=>'widget','widgetType'=>'lk-related-posts','settings'=>array('title'=>'Bài viết liên quan','limit'=>4),'elements'=>array())))));update_post_meta($post->ID,'_elementor_data',wp_slash(wp_json_encode($nodes,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)));delete_post_meta($post->ID,'_elementor_css');delete_post_meta($post->ID,'_elementor_element_cache');}
 update_option('lk_feedback2_blog_revision','2');
});

add_action('wp_loaded',function(){
 if(get_option('lk_feedback2_article_captions')||!(PHP_SAPI==='cli'||current_user_can('manage_options')))return;
 foreach((array)get_option('lk_blog_ids',array()) as $id){$raw=get_post_meta($id,'_elementor_data',true);$nodes=json_decode($raw,true);if(!$nodes)continue;
  if(!get_post_meta($id,'_lk_feedback2_backup',true))update_post_meta($id,'_lk_feedback2_backup',wp_slash($raw));
 $walk=function($ns)use(&$walk){$heading='';foreach($ns as &$node){$s=&$node['settings'];if(($node['widgetType']??'')==='heading'&&($s['header_size']??'')==='h2')$heading=wp_strip_all_tags($s['title']??'');if(($node['widgetType']??'')==='image'&&$heading&&!($s['caption']??'')){$s['caption_source']='custom';$s['caption']=$heading;}if(!empty($node['elements']))$node['elements']=$walk($node['elements']);}return $ns;};$nodes=$walk($nodes);update_post_meta($id,'_elementor_data',wp_slash(wp_json_encode($nodes,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)));delete_post_meta($id,'_elementor_css');delete_post_meta($id,'_elementor_element_cache');
 }update_option('lk_feedback2_article_captions',1);
});

add_action('wp_loaded',function(){
 if(get_option('lk_feedback2_caption_labels')||!(PHP_SAPI==='cli'||current_user_can('manage_options')))return;
 foreach((array)get_option('lk_blog_ids',array()) as $id){$raw=get_post_meta($id,'_elementor_data',true);$nodes=json_decode($raw,true);if(!$nodes)continue;
  if(!get_post_meta($id,'_lk_feedback2_backup',true))update_post_meta($id,'_lk_feedback2_backup',wp_slash($raw));
  $walk=function($items)use(&$walk){$heading='';$result=array();foreach($items as $item){$s=&$item['settings'];if(($item['widgetType']??'')==='text-editor'&&str_contains($s['editor']??'','class="lk-article-source"'))continue;
   if(($item['widgetType']??'')==='heading'&&($s['header_size']??'')==='h2')$heading=wp_strip_all_tags($s['title']??'');
   if(($item['widgetType']??'')==='image'&&$heading&&($s['caption']??'')===$heading)$s['caption']='Hình: '.preg_replace('/^\d+[.)]\s*/u','',$heading);
   if(!empty($item['elements']))$item['elements']=$walk($item['elements']);$result[]=$item;unset($s);
  }return $result;};$nodes=$walk($nodes);update_post_meta($id,'_elementor_data',wp_slash(wp_json_encode($nodes,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)));delete_post_meta($id,'_elementor_css');delete_post_meta($id,'_elementor_element_cache');
 }update_option('lk_feedback2_caption_labels',1);
},30);

add_action('wp_loaded',function(){
 if(get_option('lk_feedback2_blog_polish')||!(PHP_SAPI==='cli'||current_user_can('manage_options')))return;
 $page=get_page_by_path('blog');if(!$page)return;$raw=get_post_meta($page->ID,'_elementor_data',true);$nodes=json_decode($raw,true);if(!$nodes)return;
 if(!get_post_meta($page->ID,'_lk_feedback2_backup',true))update_post_meta($page->ID,'_lk_feedback2_backup',wp_slash($raw));
 $walk=function($ns,$anc='')use(&$walk){foreach($ns as &$n){$s=&$n['settings'];$cls=$anc.' '.($s['css_classes']??$s['_css_classes']??'');if(str_contains($cls,'lk-blog-card')&&isset($s['editor']))$s['editor']=preg_replace('/(\d{1,2})\/(\d{1,2})\/(\d{4})/','$1 Thg $2 $3',$s['editor']);if(!empty($n['elements']))$n['elements']=$walk($n['elements'],$cls);}return $ns;};$nodes=$walk($nodes);
 foreach($nodes as &$node){$rawNode=wp_json_encode($node,JSON_UNESCAPED_UNICODE);if(!str_contains($rawNode,'Cẩm nang ăn uống')&&!str_contains($rawNode,'Cẩm nang du lịch')&&!str_contains($rawNode,'Vui chơi và giải trí'))continue;$node['settings']['css_classes']=($node['settings']['css_classes']??'').' lk-blog-group-heading';$termName=str_contains($rawNode,'Cẩm nang ăn uống')?'Ẩm thực':(str_contains($rawNode,'Cẩm nang du lịch')?'Du lịch':'Giải trí');$term=get_term_by('name',$termName,'category');$link=$term?get_term_link($term):home_url('/blog/');$node['elements'][0]['elements'][]=array('id'=>substr(md5($rawNode),0,8),'elType'=>'widget','widgetType'=>'button','settings'=>array('text'=>'Xem thêm »','link'=>array('url'=>is_wp_error($link)?home_url('/blog/'):$link),'_css_classes'=>'lk-blog-more'),'elements'=>array());}unset($node);
 update_post_meta($page->ID,'_elementor_data',wp_slash(wp_json_encode($nodes,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)));delete_post_meta($page->ID,'_elementor_css');delete_post_meta($page->ID,'_elementor_element_cache');update_option('lk_feedback2_blog_polish',1);
});
