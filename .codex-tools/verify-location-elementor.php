<?php
if(PHP_SAPI!=='cli')exit;
define('WP_HTTP_BLOCK_EXTERNAL',true);
define('WP_ADMIN',true);
require dirname(__DIR__).'/wp-load.php';
$checks=array();function location_check($key,$value){global $checks;$checks[$key]=(bool)$value;}
location_check('empty_coordinates_allowed',lk_parse_coordinates('','')===array('latitude'=>'','longitude'=>''));
location_check('zero_coordinates_allowed',lk_parse_coordinates('0','0')===array('latitude'=>'0','longitude'=>'0'));
location_check('negative_coordinates',lk_parse_coordinates('-10.1234567','-107.1234567')===array('latitude'=>'-10.1234567','longitude'=>'-107.1234567'));
foreach(array(array('91','0'),array('0','181'),array('10',''),array('','107'),array(array('1'),'2'),array('1e999','2')) as $i=>$pair)location_check('reject_invalid_coordinates_'.$i,is_wp_error(lk_parse_coordinates($pair[0],$pair[1])));
$manager=Elementor\Plugin::$instance->widgets_manager;
$prototype=$manager->get_widget_types('lk-form');
location_check('prototype_assets_without_settings',in_array('lk-location-picker',$prototype->get_script_depends(),true)&&in_array('lk-location-picker',$prototype->get_style_depends(),true));
foreach(array('lk-form'=>array('address_title','map_height','field_background','field_typography_font_family','add_place_button'),'lk-results'=>array('price_tier_title','filter_width','result_gap'),'lk-reviews'=>array('review_width','star_size','modal_title')) as $type=>$keys){$widget=$manager->get_widget_types($type);$controls=$widget->get_controls();foreach($keys as $key)location_check($type.'_'.$key,isset($controls[$key]));}
ob_start();lk_form(array('kind'=>'add-place','basic_title'=>'Tùy chỉnh tiêu đề','address_title'=>'Vị trí tùy chỉnh','hours_title'=>'Lịch tùy chỉnh','price_title'=>'Phân loại tùy chỉnh','add_place_button'=>'Gửi địa điểm tùy chỉnh'));$html=ob_get_clean();
location_check('elementor_labels_render',str_contains($html,'Tùy chỉnh tiêu đề')&&str_contains($html,'Vị trí tùy chỉnh')&&str_contains($html,'Lịch tùy chỉnh')&&str_contains($html,'Phân loại tùy chỉnh')&&str_contains($html,'Gửi địa điểm tùy chỉnh'));
location_check('address_editor_fields',str_contains($html,'name="latitude"')&&str_contains($html,'name="longitude"')&&str_contains($html,'data-location-mode="pin"'));
global $wpdb;
$id=(int)$wpdb->get_var($wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE post_type='listing' AND post_title=%s ORDER BY ID DESC LIMIT 1",'Kiểm tra bản đồ 20261113'));
if($id){
 $original=get_post_meta($id,'lp_listingpro_options',true);
 if(($original['gAddress']??'')!=='Địa chỉ kiểm thử bản đồ')throw new RuntimeException('Fixture guard failed');
 if(($argv[1]??'')==='cleanup'){wp_trash_post($id);echo wp_json_encode(array('fixture_status'=>get_post_status($id),'listings'=>count(lk_posts())));exit;}
 location_check('map_coordinates_submitted',abs((float)($original['latitude']??0)-10.9398856)<0.000001&&abs((float)($original['longitude']??0)-107.2445869)<0.000001);
 $admins=get_users(array('role'=>'administrator','number'=>1));wp_set_current_user($admins[0]->ID);
 $_POST=array('lk_data_nonce'=>wp_create_nonce('lk_save_data'),'lk_data'=>array('lat'=>'-10.1234567','lng'=>'0'));wp_update_post(array('ID'=>$id));
 $saved=get_post_meta($id,'lp_listingpro_options',true);location_check('admin_coordinate_save',$saved['latitude']==='-10.1234567'&&$saved['longitude']==='0');
 location_check('other_listing_data_preserved',$saved['business_hours']===$original['business_hours']&&$saved['price_status']===$original['price_status']);
 $_POST['lk_data']=array('lat'=>'200','lng'=>'0');wp_update_post(array('ID'=>$id));location_check('invalid_admin_preserves_coordinates',get_post_meta($id,'lp_listingpro_options',true)===$saved);
 $_POST=array();update_post_meta($id,'lp_listingpro_options',$original);
}
echo wp_json_encode(array('checks'=>$checks,'passed'=>!in_array(false,$checks,true),'fixture'=>$id),JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE),PHP_EOL;
exit(in_array(false,$checks,true)?1:0);

