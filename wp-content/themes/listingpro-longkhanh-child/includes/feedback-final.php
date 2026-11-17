<?php
if (!defined('ABSPATH')) exit;

add_action('wp_enqueue_scripts',function(){
 if(!lk_is_site())return;
 wp_enqueue_style('lk-feedback-final',get_stylesheet_directory_uri().'/assets/feedback-final.css',array('lk-mobile-navigation'),filemtime(__DIR__.'/../assets/feedback-final.css'));
 wp_enqueue_script('lk-feedback-final',get_stylesheet_directory_uri().'/assets/feedback-final.js',array('lk-feedback2'),filemtime(__DIR__.'/../assets/feedback-final.js'),true);
},120);
add_filter('elementor/frontend/section/should_render',function($render,$section){
 if(str_contains((string)$section->get_settings('css_classes'),'lk-benefits')&&lk_context_id()){
  $terms=wp_get_post_terms(lk_context_id(),'features',array('fields'=>'ids'));return $render && !is_wp_error($terms) && (bool)$terms;
 }
 return $render;
},30,2);
add_filter('elementor/widget/render_content',function($html,$widget){
 if($widget->get_name()!=='google_maps'||$widget->get_settings('lk_binding')!=='map'||!lk_context_id())return $html;
 $data=lk_place_data(lk_context_id());
 if($data['lat']===null||$data['lng']===null)return $html;
 return '<div class="lk-detail-leaflet" data-lk-detail-map data-lat="'.esc_attr($data['lat']).'" data-lng="'.esc_attr($data['lng']).'" role="region" aria-label="Bản đồ '.esc_attr($data['title']).'"></div>';
},40,2);

add_filter('elementor/frontend/widget/should_render',function($render,$widget){
 if(str_contains((string)$widget->get_settings('_css_classes'),'lk-review-cta')&&lk_context_id()){
  $data=lk_place_data(lk_context_id());return $render && !$data['reviews'];
 }
 return $render;
},30,2);

// This widget stays editable in the menu document; account data is never exported.
add_action('elementor/element/button/section_button/after_section_end',function($element){
 $element->start_controls_section('lk_member_links',array('label'=>'Liên kết tài khoản trong menu'));
 foreach(array('lk_member_profile_link'=>'Chỉnh sửa hồ sơ','lk_member_saved_link'=>'Danh sách đã lưu') as $key=>$label)$element->add_control($key,array('label'=>$label,'type'=>\Elementor\Controls_Manager::URL,'description'=>'Dùng cho nút có class lk-menu-account. Để trống để lấy từ liên kết chính của nút.'));
 $element->end_controls_section();
});
function lk_menu_member_links($settings){
 $options=get_option('listingpro_options',array());
 $base=esc_url_raw($settings['link']['url']??'',array('http','https'))?:esc_url_raw($options['listing-author']??'',array('http','https'))?:lk_form_page_url('profile');
 return array('profile'=>esc_url_raw($settings['lk_member_profile_link']['url']??'',array('http','https'))?:add_query_arg('dashboard','update-profile',$base),'saved'=>esc_url_raw($settings['lk_member_saved_link']['url']??'',array('http','https'))?:preg_replace('/#.*$/','',$base).'#saved-places');
}
add_filter('elementor/element/is_dynamic_content',function($dynamic,$raw){
 return $dynamic || str_contains($raw['settings']['_css_classes']??'','lk-menu-account');
},30,2);
add_filter('elementor/widget/render_content',function($html,$widget){
 if($widget->get_name()!=='button'||!str_contains((string)$widget->get_settings('_css_classes'),'lk-menu-account')||!is_user_logged_in())return $html;
 $user=wp_get_current_user();
 $links=lk_menu_member_links($widget->get_settings_for_display());
 return '<div class="lk-menu-member"><div class="lk-menu-member-header"><span class="lk-member-avatar" aria-hidden="true">'.esc_html(lk_author_initials($user->display_name)).'</span><strong>'.esc_html($user->display_name).'</strong></div><a href="'.esc_url($links['profile']).'">'.lk_icon('user-edit').' Chỉnh sửa hồ sơ</a><a class="lk-member-saved" href="'.esc_url($links['saved']).'">'.lk_icon('bookmark').' Danh sách đã lưu</a></div>';
},40,2);
// A template's HTML cache cannot be shared between signed-in and guest menus.
function lk_final_menu_cache($value){
 if(!class_exists('Elementor\Plugin'))return $value;
 $doc=\Elementor\Plugin::$instance->documents->get_current();
 return $doc && (int)$doc->get_main_id()===(int)get_option('lk_template_menu')?'disable':$value;
}
add_filter('option_elementor_element_cache_ttl','lk_final_menu_cache',40);
add_filter('default_option_elementor_element_cache_ttl','lk_final_menu_cache',40);
add_filter('elementor/widget/render_content',function($html,$widget){
 if(!str_contains((string)$widget->get_settings('_css_classes'),'lk-menu-logout'))return $html;
 return is_user_logged_in()?'<a class="lk-button lk-outline lk-menu-signout" href="'.esc_url(wp_logout_url(home_url('/'))).'">Đăng xuất</a>':'';
},40,2);
add_filter('elementor/element/is_dynamic_content',function($dynamic,$raw){return $dynamic||str_contains($raw['settings']['_css_classes']??'','lk-menu-logout');},40,2);
