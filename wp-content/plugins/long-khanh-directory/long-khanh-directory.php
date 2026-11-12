<?php
/**
 * Plugin Name: Long Khánh - Elementor Directory
 * Description: Giao diện Địa Điểm Long Khánh cho ListingPro. Trang, header và footer chỉnh bằng Elementor Free; bổ sung tìm kiếm, đánh giá và biểu mẫu động.
 * Version: 1.0.0
 */
if (!defined('ABSPATH')) { exit; }
define('LK_DIR', __DIR__ . '/');
define('LK_URL', plugin_dir_url(__FILE__));
require_once LK_DIR . 'includes/directory.php';
require_once LK_DIR . 'includes/review-composer.php';
require_once LK_DIR . 'includes/forms.php';
require_once LK_DIR . 'includes/community.php';
require_once LK_DIR . 'includes/listing-template.php';
require_once LK_DIR . 'includes/admin-listing.php';

add_action('elementor/widgets/register', function($manager) {
 // ListingPro registers its own widget with the same name. Keep the native
 // Elementor gallery for the editable documents created by this integration.
 if(class_exists('Elementor\\Widget_Image_Gallery'))$manager->register(new \Elementor\Widget_Image_Gallery());
 require_once LK_DIR . 'includes/widgets.php';
 foreach (array('Search','Listings','Results','Reviews','Form','Account','Feed') as $name) {
  $class = 'LK_Widget_' . $name; $manager->register(new $class());
 }
},100);
add_action('elementor/elements/categories_registered', function($manager) {
 $manager->add_category('long-khanh', array('title'=>'Địa Điểm Long Khánh','icon'=>'fa fa-map-marker'));
});

add_action('init',function(){
 register_post_type('lk_message',array('label'=>'Liên hệ Long Khánh','public'=>false,'show_ui'=>true,'supports'=>array('title','editor'),'capability_type'=>'post','capabilities'=>array('create_posts'=>'do_not_allow'),'map_meta_cap'=>true));
});
function lk_register_routes(){
 add_rewrite_rule('^dia-diem/([^/]+)/(photo|menu)/?$', 'index.php?post_type=listing&name=$matches[1]&lk_view=$matches[2]', 'top');
 add_rewrite_rule('^dia-diem/([^/]+)/?$', 'index.php?post_type=listing&name=$matches[1]', 'top');
 add_rewrite_rule('^blog/([^/]+)/?$', 'index.php?post_type=post&name=$matches[1]', 'top');
 foreach(get_option('lk_routes',array()) as $route=>$id)add_rewrite_rule('^'.preg_quote($route,'#').'/?$','index.php?page_id='.(int)$id,'top');
}
add_action('init','lk_register_routes',30);
add_filter('post_type_link',function($url,$post){return $post->post_type==='listing'&&get_post_meta($post->ID,'_lk_managed',true)?lk_link('dia-diem/'.$post->post_name):$url;},10,2);
add_filter('post_link',function($url,$post){return get_post_meta($post->ID,'_lk_managed',true)?lk_link('blog/'.$post->post_name):$url;},10,2);
add_filter('page_link',function($url,$id){static $routes; if($routes===null)$routes=array_flip(get_option('lk_routes',array()));return isset($routes[$id])?lk_link($routes[$id]):$url;},10,2);
add_action('admin_menu',function(){ add_menu_page('Địa Điểm Long Khánh','Long Khánh','edit_pages','lk-directory','lk_admin_page','dashicons-location-alt',28); });
function lk_admin_page(){
 if(!current_user_can('edit_pages'))return;
 echo '<div class="wrap"><h1>Địa Điểm Long Khánh</h1><p>Theme ListingPro vẫn đang hoạt động. Các trang và bản mẫu bên dưới được dựng bằng Elementor Free.</p><h2>Bản mẫu dùng chung</h2><ul>';
 foreach(array('header'=>'Header và thanh điều hướng mobile','footer'=>'Footer','menu'=>'Menu mobile','listing'=>'Template chung chi tiết địa điểm','listing_gallery'=>'Template chung album và menu') as $key=>$name){$id=get_option('lk_template_'.$key);if($id)echo '<li><a href="'.esc_url(admin_url('post.php?post='.$id.'&action=elementor')).'">Sửa '.$name.' bằng Elementor</a></li>';}
 echo '</ul><form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="lk_template_preview">';wp_nonce_field('lk_template_preview');
 echo '<label for="lk-preview-listing"><strong>Địa điểm dùng để xem trước khi sửa template</strong></label> <select id="lk-preview-listing" name="listing_id">';
 foreach(lk_posts() as $place)echo '<option value="'.$place->ID.'" '.selected(get_option('lk_listing_preview'),$place->ID,false).'>'.esc_html($place->post_title).'</option>';
 echo '</select> <button class="button">Lưu địa điểm xem trước</button></form><p><a class="button button-primary" href="'.esc_url(admin_url('post-new.php?post_type=listing')).'">Thêm địa điểm từ dữ liệu</a></p><ul>';
 echo '</ul><h2>Các trang</h2><table class="widefat"><thead><tr><th>Trang</th><th>Chỉnh sửa</th><th>Xem</th></tr></thead><tbody>';
 foreach(get_posts(array('post_type'=>array('page','listing','post'),'post_status'=>'publish','meta_key'=>'_lk_managed','meta_value'=>'1','posts_per_page'=>100,'orderby'=>'post_type','order'=>'ASC')) as $p){
  echo '<tr><td>'.esc_html($p->post_title).'</td><td>';
  if($p->post_type==='listing')echo '<a href="'.esc_url(get_edit_post_link($p->ID)).'">Nhập dữ liệu địa điểm</a> | <a href="'.esc_url(admin_url('post.php?post='.get_option('lk_template_listing').'&action=elementor')).'">Sửa template chung</a>';
  else echo '<a href="'.esc_url(admin_url('post.php?post='.$p->ID.'&action=elementor')).'">Sửa với Elementor</a> | <a href="'.esc_url(get_edit_post_link($p->ID)).'">Thông tin WordPress</a>';
  echo '</td><td><a href="'.esc_url(get_permalink($p)).'">Mở trang</a></td></tr>';
 }
 echo '</tbody></table><h2>Cách chỉnh sửa</h2><p>Chữ, ảnh, nút, danh mục, khu vực, bố cục và nền dùng widget Elementor gốc. Bấm vào từng thành phần trong trình sửa để thay nội dung. Các widget trong nhóm “Địa Điểm Long Khánh” bổ sung dữ liệu động và biểu mẫu mà Elementor Free chưa có.</p><p>Địa điểm được lưu bằng loại bài ListingPro. Giá, địa chỉ và giờ hoạt động trong bảng “Thông tin địa điểm Long Khánh” được dùng cho tìm kiếm và thẻ địa điểm. Ảnh đại diện dùng cho kết quả tìm kiếm. Các nội dung được thêm từ bản tham chiếu là dữ liệu demo.</p></div>';
}
add_action('add_meta_boxes',function(){ add_meta_box('lk-data','Thông tin địa điểm Long Khánh','lk_meta_box','listing','normal'); });
function lk_meta_box($post){
 if(!get_post_meta($post->ID,'_lk_managed',true)&&$post->post_status!=='auto-draft')return;
 wp_nonce_field('lk_save_data','lk_data_nonce'); $d=lk_place_data($post);
 foreach(array('address'=>'Địa chỉ','phone'=>'Điện thoại','min'=>'Giá thấp nhất (đ)','max'=>'Giá cao nhất (đ)','open'=>'Giờ mở cửa','close'=>'Giờ đóng cửa','lat'=>'Vĩ độ','lng'=>'Kinh độ') as $key=>$label){$type=in_array($key,array('open','close'),true)?'time':(in_array($key,array('min','max','lat','lng'),true)?'number':'text');echo '<p><label>'.esc_html($label).'<br><input style="width:100%" type="'.$type.'" '.(in_array($key,array('lat','lng'),true)?'step="any"':(in_array($key,array('min','max'),true)?'min="0"':'')).' name="lk_data['.esc_attr($key).']" value="'.esc_attr($d[$key]).'"></label></p>';}
}
add_action('save_post_listing',function($id){
 if(!isset($_POST['lk_data_nonce'])||!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['lk_data_nonce'])),'lk_save_data')||!current_user_can('edit_post',$id))return;
 $d=isset($_POST['lk_data'])?map_deep(wp_unslash($_POST['lk_data']),'sanitize_text_field'):array();
 $options=get_post_meta($id,'lp_listingpro_options',true); if(!is_array($options))$options=array();
 foreach(array('address'=>'gAddress','phone'=>'phone','min'=>'list_price','max'=>'list_price_to','lat'=>'latitude','lng'=>'longitude') as $key=>$field)if(isset($d[$key]))$options[$field]=$d[$key];
 foreach(array('open','close') as $key)if(isset($d[$key])&&preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/',$d[$key]))update_post_meta($id,'_lk_'.$key,$d[$key]);
 foreach(array('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') as $day)$options['business_hours'][$day]=array('open'=>get_post_meta($id,'_lk_open',true)?:'07:00','close'=>get_post_meta($id,'_lk_close',true)?:'22:00');
 update_post_meta($id,'lp_listingpro_options',$options);
 update_post_meta($id,'_lk_managed',1);
});
