<?php
if (!defined('ABSPATH')) exit;
add_action('pre_get_posts',function($query){
 $id=isset($_GET['elementor-preview'])?absint($_GET['elementor-preview']):0;
 if(!$id||!$query->is_main_query()||get_post_type($id)!=='elementor_library'||!get_post_meta($id,'_lk_managed',true)||!current_user_can('edit_post',$id))return;
 $query->set('post_type','elementor_library');$query->set('p',$id);$query->set('name','');$query->set('elementor_library','');
 $query->is_home=false;$query->is_archive=false;$query->is_singular=true;$query->is_single=true;$query->is_page=false;
},9);
add_filter('elementor/widget/render_content',function($content,$widget){
 $classes=(string)$widget->get_settings('_css_classes');
 foreach(array('lk-open-menu'=>'Mở menu','lk-open-search'=>'Mở tìm kiếm','lk-nearby'=>'Tìm địa điểm quanh đây') as $class=>$label){
  if(strpos($classes,$class)!==false)$content=preg_replace('/<a\b/','<a aria-label="'.esc_attr($label).'"',$content,1);
 }
 return $content;
},10,2);
function lk_is_site() {
 return is_singular() && ((bool)get_post_meta(get_queried_object_id(), '_lk_managed', true)||lk_is_directory_listing(get_queried_object_id()));
}
// The legacy shortcode injects ID-based widths and borders. Elementor's native
// gallery controls and this child theme provide the layout on managed pages.
add_filter('use_default_gallery_style',function($enabled){return lk_is_site()?false:$enabled;});
add_filter('template_include',function($template){
 $preview=isset($_GET['elementor-preview'])?absint($_GET['elementor-preview']):0;
 if($preview&&get_post_meta($preview,'_lk_managed',true)&&current_user_can('edit_post',$preview))return ELEMENTOR_PATH.'modules/page-templates/templates/canvas.php';
 if(is_singular('listing')&&lk_is_directory_listing(get_queried_object_id())&&get_option('lk_template_listing'))return get_stylesheet_directory().'/single-lk-listing.php';
 return $template;
},100);
add_filter('body_class', function($classes) {
 if (lk_is_site()) { $classes[]='lk-site'; if(is_front_page()) $classes[]='lk-home'; }
 return $classes;
});
add_action('wp_enqueue_scripts', function() {
 if (!lk_is_site()) return;
 // ListingPro remains active. Canvas owns the page shell; avoid its global
 // Bootstrap/layout rules overriding the editable Elementor documents.
 global $wp_styles, $wp_scripts;
 foreach ($wp_styles->queue as $handle) {
  $src = $wp_styles->registered[$handle]->src ?? '';
  if (strpos($src, '/themes/listingpro/')!==false || strpos($src, '/plugins/listingpro-')!==false) wp_dequeue_style($handle);
 }
 foreach ($wp_scripts->queue as $handle) {
  $src = $wp_scripts->registered[$handle]->src ?? '';
  if (strpos($src, '/themes/listingpro/')!==false || strpos($src, '/plugins/listingpro-')!==false) wp_dequeue_script($handle);
 }
 wp_enqueue_style('lk-icons', plugins_url('elementor/assets/lib/font-awesome/css/all.min.css'), array(), '5.15.3');
 wp_enqueue_style('lk-site', get_stylesheet_directory_uri().'/assets/site.css', array(), (string)filemtime(get_stylesheet_directory().'/assets/site.css'));
 wp_enqueue_script('lk-site', get_stylesheet_directory_uri().'/assets/site.js', array(), (string)filemtime(get_stylesheet_directory().'/assets/site.js'), true);
 wp_enqueue_style('lk-motion',get_stylesheet_directory_uri().'/assets/motion.css',array('lk-site'),(string)filemtime(get_stylesheet_directory().'/assets/motion.css'));
 wp_enqueue_script('lk-motion',get_stylesheet_directory_uri().'/assets/motion.js',array('lk-site'),(string)filemtime(get_stylesheet_directory().'/assets/motion.js'),true);
 wp_enqueue_style('lk-pages',get_stylesheet_directory_uri().'/assets/pages.css',array('lk-motion'),(string)filemtime(get_stylesheet_directory().'/assets/pages.css'));
 wp_enqueue_script('lk-composer',get_stylesheet_directory_uri().'/assets/review-composer.js',array('lk-site'),(string)filemtime(get_stylesheet_directory().'/assets/review-composer.js'),true);
 $places=lk_directory_index();
 wp_add_inline_script('lk-site', 'window.LK_SITE=' . wp_json_encode(array('base'=>home_url('/'),'search'=>lk_link('search'),'profile'=>lk_link('profile'),'places'=>$places,'page'=>get_queried_object_id(),'loggedIn'=>is_user_logged_in()),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) . ';','before');
});

function lk_template($key) {
 $id=(int)get_option('lk_template_'.$key);
 if ($id && $id!==get_the_ID() && class_exists('Elementor\\Plugin')) {
  echo Elementor\Plugin::$instance->frontend->get_builder_content_for_display($id, true); // Elementor sanitizes widget content.
 }
}
add_action('elementor/page_templates/canvas/before_content', function() {
 if (!lk_is_site() || get_post_type()==='elementor_library') return;
 echo '<a class="lk-skip" href="#lk-main">Chuyển đến nội dung</a><header class="lk-site-header">';
 lk_template('header'); echo '</header><main id="lk-main">';
});
add_action('elementor/page_templates/canvas/after_content', function() {
 if (!lk_is_site() || get_post_type()==='elementor_library') return;
 echo '</main><footer class="lk-site-footer">'; lk_template('footer'); echo '</footer>';
 echo '<dialog id="lk-menu" class="lk-drawer" aria-label="Menu điều hướng"><button class="lk-close" data-close aria-label="Đóng menu">&times;</button>';
 lk_template('menu'); echo '</dialog>';
 echo '<dialog id="lk-login" class="lk-login" aria-labelledby="lk-login-title"><button class="lk-close" data-close aria-label="Đóng đăng nhập">&times;</button><div class="lk-login-body">';
 lk_account('login');
 echo '</div><div class="lk-login-art"><img src="'.esc_url(lk_asset('login-illustration')).'" alt="Khám phá địa điểm Long Khánh"><h2>Địa Điểm Long Khánh</h2></div></dialog>';
 echo '<dialog id="lk-search-modal" class="lk-search-modal" aria-label="Tìm kiếm địa điểm">';
 lk_search(array('placeholder'=>'Nhập tên quán, khu vực, kiểu quán,...','button'=>'Tìm quán','mode'=>'modal'));
 echo '<button data-close class="lk-cancel">Huỷ</button><div class="lk-search-suggestions"></div></dialog>';
 echo '<dialog id="lk-map" class="lk-map-dialog" aria-labelledby="lk-map-title"><h2 id="lk-map-title">Tìm kiếm địa điểm</h2><button class="lk-close" data-close aria-label="Đóng bản đồ">&times;</button><div class="lk-map-layout"><div class="lk-map-list"></div><iframe title="Bản đồ địa điểm Long Khánh" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe></div></dialog>';
 if(is_singular('listing')&&!get_query_var('lk_view')&&!post_password_required()){
  echo '<dialog id="lk-place-menu" class="lk-place-menu" aria-label="Menu địa điểm"><button class="lk-close" data-close aria-label="Đóng menu địa điểm">&times;</button>';
  $GLOBALS['lk_menu_dialog']=true;set_query_var('lk_view','menu');lk_template('listing_gallery');set_query_var('lk_view','');unset($GLOBALS['lk_menu_dialog']);
  echo '</dialog>';
 }
 echo '<button class="lk-top" aria-label="Về đầu trang"><i class="fas fa-arrow-up" aria-hidden="true"></i></button><div class="lk-toast" role="status" aria-live="polite"></div>';
});

