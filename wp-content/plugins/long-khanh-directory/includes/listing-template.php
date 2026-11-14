<?php
if (!defined('ABSPATH')) exit;

function lk_context_id() {
 if (!empty($GLOBALS['lk_listing_context'])) return (int)$GLOBALS['lk_listing_context'];
 if (is_singular('listing')) return get_queried_object_id();
 $candidate=(int)get_option('lk_listing_preview');
 return get_post_type($candidate)==='listing'?$candidate:0;
}
function lk_is_directory_listing($id) {
 if(get_post_type($id)!=='listing')return false;
 if(get_post_meta($id,'_lk_managed',true))return true;
 $terms=lk_directory_terms('listing-category');
 return !is_wp_error($terms)&&$terms&&has_term(wp_list_pluck($terms,'term_id'),'listing-category',$id);
}
function lk_listing_media($id,$view='photo') {
 if(isset($GLOBALS['lk_media_cache'][$id][$view]))return $GLOBALS['lk_media_cache'][$id][$view];
 $raw=get_post_meta($id,$view==='menu'?'_lk_menu_ids':'gallery_image_ids',true);
 if($view==='menu'){
  $native=get_post_meta($id,'menu_listing',true);$urls=is_array($native)?($native['menu-img']??''):'';
  if($urls){$items=array();foreach(is_array($urls)?$urls:explode(',',$urls) as $url){$url=esc_url_raw(trim($url));if($url)$items[]=array('id'=>attachment_url_to_postid($url),'url'=>$url);}if($items)return $GLOBALS['lk_media_cache'][$id][$view]=$items;}
 }
 $ids=is_array($raw)?$raw:explode(',',(string)$raw);
 $ids=array_values(array_filter(array_map('absint',$ids)));
 if(!$ids&&$view!=='menu'&&get_post_thumbnail_id($id))$ids[]=get_post_thumbnail_id($id);
 $attachments=$ids?get_posts(array('post_type'=>'attachment','post_status'=>'inherit','post_mime_type'=>'image','post__in'=>$ids,'orderby'=>'post__in','posts_per_page'=>-1)):array();
 if(!$attachments&&$view!=='menu'&&wp_attachment_is_image(get_post_thumbnail_id($id)))$attachments=array(get_post(get_post_thumbnail_id($id)));
 return $GLOBALS['lk_media_cache'][$id][$view]=array_map(function($media){return array('id'=>$media->ID,'url'=>wp_get_attachment_url($media->ID));},$attachments);
}
function lk_bindings_for($type) {
 $options=array(''=>'Nội dung nhập trực tiếp');
 $sources=array(
  'heading'=>array('title'=>'Tên địa điểm','gallery-title'=>'Tiêu đề album / menu'),
  'text-editor'=>array('description'=>'Giới thiệu địa điểm','score'=>'Điểm đánh giá','rating-message'=>'Trạng thái đánh giá','address'=>'Địa chỉ','gallery-status'=>'Thông báo album trống','region-count'=>'Số địa điểm trong khu vực'),
  'image'=>array('featured'=>'Ảnh đại diện'),
  'video'=>array('listing-video'=>'Video giới thiệu địa điểm'),
  'image-gallery'=>array('gallery'=>'Ảnh địa điểm','view-gallery'=>'Album / menu đang xem'),
  'icon-list'=>array('links'=>'Địa chỉ và liên kết nhanh','info'=>'Giá, giờ, điện thoại','benefits'=>'Tiện ích','directions'=>'Liên kết chỉ đường'),
  'google_maps'=>array('map'=>'Vị trí địa điểm'),
  'button'=>array('photos-button'=>'Xem tất cả ảnh','back-button'=>'Về địa điểm','menu-button'=>'Xem menu','directions-button'=>'Xem đường đi','save-button'=>'Lưu địa điểm'),
  'progress'=>array('criteria-position'=>'Điểm vị trí','criteria-space'=>'Điểm không gian','criteria-quality'=>'Điểm chất lượng','criteria-service'=>'Điểm phục vụ','criteria-price'=>'Điểm giá cả'),
 );
 return $options+($sources[$type]??array());
}
add_action('elementor/element/after_section_end',function($element,$section){
 $sections=array('heading'=>'section_title','text-editor'=>'section_editor','image'=>'section_image','video'=>'section_video','image-gallery'=>'section_gallery','icon-list'=>'section_icon','google_maps'=>'section_map','button'=>'section_button','progress'=>'section_progress');
 $type=$element->get_name();
 if(!isset($sections[$type])||$section!==$sections[$type])return;
 $element->start_controls_section('lk_binding_section',array('label'=>'Dữ liệu địa điểm Long Khánh','tab'=>\Elementor\Controls_Manager::TAB_CONTENT));
 $element->add_control('lk_binding',array('label'=>'Nguồn nội dung','type'=>\Elementor\Controls_Manager::SELECT,'options'=>lk_bindings_for($type),'default'=>'','description'=>'Trong template chung, nội dung tự lấy từ bài địa điểm đang xem. Kiểu chữ, màu và bố cục vẫn sửa bằng Elementor.'));
 if($type==='heading')$element->add_control('lk_typewriter_lines',array('label'=>'Câu chuyển động (mỗi dòng một câu)','type'=>\Elementor\Controls_Manager::TEXTAREA,'description'=>'Để trống để dùng tiêu đề tĩnh. Câu đầu lấy từ ô Tiêu đề; các dòng dưới là câu tiếp theo.'));
 if($type==='text-editor'){$regions=array();$terms=lk_directory_terms('location');if(!is_wp_error($terms))foreach($terms as $term)$regions[$term->slug]=$term->name;$element->add_control('lk_region',array('label'=>'Khu vực cần đếm','type'=>\Elementor\Controls_Manager::SELECT,'options'=>$regions,'condition'=>array('lk_binding'=>'region-count')));}
 $element->end_controls_section();
},10,2);
// Bound native widgets must be rendered for each listing, never cached as one
// listing's content and reused for a different listing.
add_filter('elementor/element/is_dynamic_content',function($dynamic,$raw){return !empty($raw['settings']['lk_binding'])||$dynamic;},10,2);
// Elementor's document HTML cache is keyed only by template ID. A directory
// template has different output for every listing, including native galleries.
function lk_listing_cache_ttl($value){
 $preview=absint($_GET['elementor-preview']??0);
 $templates=array_filter(array((int)get_option('lk_template_listing'),(int)get_option('lk_template_listing_gallery')));
 $document=class_exists('Elementor\Plugin')?\Elementor\Plugin::$instance->documents->get_current():null;
 $document_id=$document?(int)$document->get_main_id():0;
 return in_array($document_id,$templates,true)||(!$document_id&&in_array($preview,$templates,true))?'disable':$value;
}
add_filter('option_elementor_element_cache_ttl','lk_listing_cache_ttl');
add_filter('default_option_elementor_element_cache_ttl','lk_listing_cache_ttl');
function lk_resolve_listing_settings($type,$settings,$id) {
 $binding=$settings['lk_binding']??'';
 if($type==='text-editor'&&$binding==='region-count'){$region=$settings['lk_region']??'';$count=count(array_filter(lk_directory_index(),function($place)use($region){return $place['regionSlug']===$region;}));$settings['editor']='<p>'.$count.' địa điểm</p>';return $settings;}
 if(!$binding||!$id)return $settings;
 $d=lk_place_data($id);
 $item=function($text,$url,$icon,$i){return array('_id'=>'lk-field-'.$i,'text'=>$text,'link'=>array('url'=>$url),'selected_icon'=>array('value'=>'fas fa-'.$icon,'library'=>'fa-solid'));};
 $destination=$d['lat']!==null&&$d['lng']!==null?$d['lat'].','.$d['lng']:$d['address'];
 $directions='https://www.google.com/maps/dir/?api=1&destination='.rawurlencode($destination);
 if($type==='heading'){$settings['title']=esc_html(($binding==='gallery-title'?(get_query_var('lk_view')==='menu'?'Menu của ':'Hình ảnh '):'').$d['title']);if($binding==='gallery-title')$settings['link']=array('url'=>$d['url']);}
 if($type==='text-editor'){
  $content='';
  if($binding==='description')$content=wpautop(wp_kses_post(get_post_field('post_content',$id)?:get_post_field('post_excerpt',$id)));
  if($binding==='address')$content='<p>'.esc_html($d['address']).'</p>';
  if($binding==='gallery-status'&&!lk_listing_media($id,get_query_var('lk_view','photo')))$content='<p class="lk-gallery-empty">'.(get_query_var('lk_view')==='menu'?'Menu đang được cập nhật. Bạn có thể liên hệ địa điểm để hỏi thêm.':'Hình ảnh đang được cập nhật.').'</p>';
  if($binding==='score')$content='<p class="lk-score">'.($d['rating']?esc_html($d['rating']):'0').'</p>';
  if($binding==='rating-message')$content='<div class="lk-rating-summary"><span class="lk-score">'.esc_html($d['rating']).'</span><div><strong class="lk-rating-message">'.($d['rating']?'Đánh giá từ cộng đồng':'Chưa có đánh giá').'</strong><small>/5 ('.(int)$d['reviews'].' đánh giá)</small></div></div>';
  $settings['editor']=$content;
 }
 if($type==='image')$settings['image']=array('id'=>get_post_thumbnail_id($id),'url'=>$d['image']);
 if($type==='video'&&$binding==='listing-video'){$options=get_post_meta($id,'lp_listingpro_options',true);$video=esc_url_raw(is_array($options)?($options['video']??''):'');$host=strtolower((string)wp_parse_url($video,PHP_URL_HOST));$settings['video_type']=in_array($host,array('vimeo.com','www.vimeo.com'),true)?'vimeo':'youtube';$settings['youtube_url']=$video;$settings['vimeo_url']=$video;if(preg_match('/\.(mp4|webm)$/i',(string)wp_parse_url($video,PHP_URL_PATH))){$settings['video_type']='hosted';$settings['hosted_url']=array('url'=>$video,'id'=>0);}}
 if($type==='image-gallery')$settings['wp_gallery']=lk_listing_media($id,$binding==='view-gallery'?get_query_var('lk_view','photo'):'photo');
 if($type==='google_maps')$settings['address']=$d['address'];
 if($type==='progress'){$criteria=lk_rating_criteria($id);$settings['percent']=array('size'=>($criteria[substr($binding,9)]??0)*20,'unit'=>'%');}
 if($type==='icon-list'){
  $items=array();
  if($binding==='links')$items=array($item($d['address'],'','map-marker-alt',0),$item('Hiển thị bản đồ','#location','map-marked-alt',1),$item('Xem đường đi',$directions,'directions',2),$item('Xem menu',$d['url'].'menu/','utensils',3));
  if($binding==='info')$items=array($item(lk_money($d['min']).' - '.lk_money($d['max']).($d['priceStatus']&&$d['priceStatus']!=='notsay'?' · '.$d['priceLabel']:''),'','dollar-sign',0),$item(($d['opened']?'Đang mở cửa':'Đang đóng cửa').' · '.$d['hoursLabel'],'','clock',1),$item($d['phone']?:'Đang cập nhật',$d['phone']?'tel:'.$d['phone']:'','phone-alt',2));
  if($binding==='benefits')foreach(wp_get_post_terms($id,'features') as $i=>$term){$icon=get_term_meta($term->term_id,'_lk_icon',true)?:'check';$icon=strtr($icon,array('hand-holding-box'=>'shopping-bag'));$items[]=$item($term->name,'',$icon,$i);}
  if($binding==='directions')$items=array($item('Xem đường đi',$directions,'directions',0));
  $settings['icon_list']=$items;
 }
 if($type==='button'){
  if($binding==='save-button')$settings['link']=array('url'=>'#');
  if($binding==='photos-button'){$settings['text']='Xem tất cả ảnh ('.count(lk_listing_media($id)).')';$settings['link']=array('url'=>$d['url'].'photo/');}
  if($binding==='back-button')$settings['link']=array('url'=>$d['url']);
  if($binding==='menu-button')$settings['link']=array('url'=>$d['url'].'menu/');
  if($binding==='directions-button')$settings['link']=array('url'=>$directions);
 }
 return $settings;
}
// Resolve before Elementor parses native settings. before_render runs after
// get_settings_for_display() has cached them, so it cannot bind these fields.
function lk_bind_listing_nodes($nodes,$id) {
 foreach($nodes as &$node){
  if(!empty($node['settings']['lk_binding']))$node['settings']=lk_resolve_listing_settings($node['widgetType']??'', $node['settings'],$id);
  if(!empty($GLOBALS['lk_menu_dialog'])){
   if(($node['widgetType']??'')==='image-gallery'){$node['settings']['open_lightbox']='no';$node['settings']['gallery_link']='none';}
   if(($node['widgetType']??'')==='heading')$node['settings']['header_size']='h2';
  }
  if(!empty($node['elements']))$node['elements']=lk_bind_listing_nodes($node['elements'],$id);
 }
 return $nodes;
}
add_filter('elementor/frontend/builder_content_data',function($nodes,$document_id){
 if(!in_array((int)$document_id,array((int)get_option('lk_template_listing'),(int)get_option('lk_template_listing_gallery')),true))return $nodes;
 return lk_bind_listing_nodes($nodes,lk_context_id());
},20,2);
// The editor renders individual native widgets over AJAX, outside document
// loading. Reset Elementor's parsed settings through its public API there.
add_action('elementor/widget/before_render_content',function($widget){
 if(!$widget->get_settings('lk_binding'))return;
 $widget->set_settings(lk_resolve_listing_settings($widget->get_name(),$widget->get_settings(),lk_context_id()));
 if(method_exists($widget,'reset_render_state'))$widget->reset_render_state();
});
add_filter('elementor/widget/render_content',function($html,$widget){
 if($widget->get_name()==='icon-list'&&$widget->get_settings('lk_binding')==='info'&&lk_context_id()){$data=lk_place_data(lk_context_id());$html=preg_replace_callback('/<li\b[^>]*>.*?<\/li>/s',function($match)use($data){return (str_contains($match[0],'fa-clock')||str_contains($match[0],'fas-clock'))?'<li class="elementor-icon-list-item lk-hours-list-item">'.lk_schedule_details($data).'</li>':$match[0];},$html);}
 if($widget->get_name()==='progress'&&str_starts_with((string)$widget->get_settings('lk_binding'),'criteria-')){$criteria=lk_rating_criteria(lk_context_id());$score=$criteria[substr($widget->get_settings('lk_binding'),9)]??0;$html=preg_replace_callback('/(<span class="elementor-progress-percentage">).*?(<\/span>)/s',function($match)use($score){return $match[1].esc_html($score).$match[2];},$html);}
 if($widget->get_name()==='heading'&&$widget->get_settings('lk_typewriter_lines')){
  $lines=trim($widget->get_settings('lk_typewriter_lines'));
  $html=preg_replace('/(<h[1-6]\\b)/','$1 data-lk-typewriter="'.esc_attr($lines).'"',$html,1);
 }
 if($widget->get_name()==='image-gallery'&&$widget->get_settings('lk_binding')==='view-gallery'&&!lk_listing_media(lk_context_id(),get_query_var('lk_view','photo'))){
  $html='<p class="lk-gallery-empty">'.(get_query_var('lk_view')==='menu'?'Menu đang được cập nhật. Bạn có thể liên hệ địa điểm để hỏi thêm.':'Hình ảnh đang được cập nhật.').'</p>';
 }
 if($widget->get_name()==='image-gallery'&&$widget->get_settings('lk_binding')==='gallery'){
  $remaining=count(lk_listing_media(lk_context_id()))-5;$index=0;
  if($remaining>0)$html=preg_replace_callback('/<dl class=[\x22\x27]gallery-item[\x22\x27]>.*?<\/dl>/s',function($match)use(&$index,$remaining){$index++;return $index===5?str_replace('</dl>','<span class="lk-gallery-remainder">+ '.(int)$remaining.' ảnh</span></dl>',$match[0]):$match[0];},$html);
 }
 return $html;
},20,2);
// Server-render bound controls while editing, so the preview shows real data.
add_filter('elementor/widget/print_template',function($template,$widget){return count(lk_bindings_for($widget->get_name()))>1?'':$template;},10,2);
add_filter('query_vars',function($vars){$vars[]='lk_view';return $vars;});

add_action('add_meta_boxes_listing',function($post){
 add_meta_box('lk-shared-template','Template chung Long Khánh',function($p){
  $id=(int)get_option('lk_template_listing');
  echo '<p>Địa điểm dùng template Elementor chung. Chỉ nhập nội dung, ảnh, giá và danh mục cho bài này.</p><p><a class="button button-primary" href="'.esc_url(admin_url('post.php?post='.$id.'&action=elementor')).'">Sửa template chung bằng Elementor</a></p><p>Sửa template sẽ áp dụng cho tất cả địa điểm Long Khánh.</p>';
 },'listing','side','high');
});
add_filter('post_row_actions',function($actions,$post){
 if($post->post_type==='listing'&&lk_is_directory_listing($post->ID))$actions['lk-template']='<a href="'.esc_url(admin_url('post.php?post='.get_option('lk_template_listing').'&action=elementor')).'">Sửa template chung</a>';
 return $actions;
},10,2);
add_action('save_post_listing',function($id,$post,$update){
 if(wp_is_post_revision($id)||wp_is_post_autosave($id))return;
 if(!$update||lk_is_directory_listing($id)){
  update_post_meta($id,'_lk_managed',1);
  foreach(array('listing-category','location') as $taxonomy)foreach(wp_get_post_terms($id,$taxonomy,array('fields'=>'ids')) as $term)update_term_meta($term,'_lk_directory',1);
 }
},20,3);
