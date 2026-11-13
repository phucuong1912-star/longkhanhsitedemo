<?php
if(!defined('ABSPATH'))exit;

function lk_location_config() {
 $options=get_option('listingpro_options',array());$key=trim((string)($options['google_map_api']??''));
 $google=($options['map_option']??'openstreet')==='google'&&$key!=='';
 return array('provider'=>$google?'google':'openstreet','googleReady'=>$google,'googleScript'=>$google?add_query_arg(array('key'=>$key,'libraries'=>'places,marker','v'=>'weekly','loading'=>'async','language'=>'vi'),'https://maps.googleapis.com/maps/api/js'):'','center'=>array('lat'=>10.9398,'lng'=>107.2446));
}
function lk_register_location_assets() {
 wp_register_style('lk-location-map',get_template_directory_uri().'/assets/css/mapbox.css',array(),null);
 wp_register_style('lk-location-picker',LK_URL.'assets/location-picker.css',array('lk-location-map'),filemtime(LK_DIR.'assets/location-picker.css'));
 wp_register_script('lk-location-leaflet',get_template_directory_uri().'/assets/js/mapbox.js',array(),null,true);
 wp_register_script('lk-location-picker',LK_URL.'assets/location-picker.js',array('lk-location-leaflet'),filemtime(LK_DIR.'assets/location-picker.js'),true);
 wp_add_inline_script('lk-location-picker','window.LK_LOCATION='.wp_json_encode(lk_location_config(),JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT).';','before');
}
add_action('init','lk_register_location_assets',30);
function lk_enqueue_location_assets(){wp_enqueue_style('lk-location-picker');wp_enqueue_script('lk-location-picker');}

function lk_parse_coordinates($lat,$lng) {
 if(!is_scalar($lat)||!is_scalar($lng))return new WP_Error('coordinates','Tọa độ chưa hợp lệ.');
 $lat=trim((string)$lat);$lng=trim((string)$lng);
 if($lat===''&&$lng==='')return array('latitude'=>'','longitude'=>'');
 if($lat===''||$lng===''||!is_numeric($lat)||!is_numeric($lng)||!is_finite((float)$lat)||!is_finite((float)$lng)||(float)$lat < -90||(float)$lat > 90||(float)$lng < -180||(float)$lng > 180)return new WP_Error('coordinates','Vui lòng nhập đủ vĩ độ (-90 đến 90) và kinh độ (-180 đến 180).');
 $format=function($value){return (float)$value==0?'0':rtrim(rtrim(sprintf('%.7F',(float)$value),'0'),'.');};
 return array('latitude'=>$format($lat),'longitude'=>$format($lng));
}
function lk_location_editor($values=array(),$admin=false,$settings=array()) {
 lk_enqueue_location_assets();$id=wp_unique_id('lk-address-');$config=lk_location_config();
 $address=$values['address']??'';$lat=$values['lat']??'';$lng=$values['lng']??'';
 $names=$admin?array('address'=>'lk_data[address]','lat'=>'lk_data[lat]','lng'=>'lk_data[lng]'):array('address'=>'address','lat'=>'latitude','lng'=>'longitude');
 echo '<fieldset class="lk-address-editor"><legend>'.esc_html($settings['address_title']??'Thêm Địa Chỉ Tùy Chỉnh').'</legend><div class="lk-address-tabs" role="tablist" aria-label="Cách chọn vị trí">';
 foreach(array('address'=>$config['googleReady']?'Tìm bằng Google':'Nhập địa chỉ','manual'=>'Nhập tọa độ thủ công','pin'=>'Thả ghim') as $mode=>$label)echo '<button type="button" role="tab" aria-selected="'.($mode==='address'?'true':'false').'" aria-controls="'.$id.'panel" data-location-mode="'.$mode.'">'.($mode==='pin'?'<i class="fas fa-map-pin" aria-hidden="true"></i> ':'').esc_html($label).'</button>';
 echo '</div><div id="'.$id.'panel" class="lk-address-panel"><div class="lk-google-search" hidden></div><label for="'.$id.'text">Địa chỉ'.(!$admin?' <span aria-hidden="true">*</span>':'').'<input id="'.$id.'text" type="text" name="'.esc_attr($names['address']).'" data-location-address placeholder="Nhập địa chỉ tại đây" value="'.esc_attr($address).'" '.(!$admin?'required':'').'></label><div class="lk-address-coordinates">';
 foreach(array('lat'=>array('Vĩ độ',-90,90,$lat),'lng'=>array('Kinh độ',-180,180,$lng)) as $field=>$data)echo '<label>'.esc_html($data[0]).'<input type="number" step="any" min="'.$data[1].'" max="'.$data[2].'" data-location-coordinate="'.$field.'" name="'.esc_attr($names[$field]).'" placeholder="'.($field==='lat'?'10.9398000':'107.2446000').'" value="'.esc_attr($data[3]).'"></label>';
 echo '</div><p class="lk-location-help">'.($config['googleReady']?'Chọn địa chỉ từ Google, nhập tọa độ hoặc bấm bản đồ để đặt ghim.':'Nhập địa chỉ, nhập tọa độ hoặc chọn Thả ghim để xác định vị trí chính xác.').'</p><div class="lk-location-map-panel" hidden><p>Bấm lên bản đồ để đặt ghim, hoặc kéo ghim đến vị trí chính xác.</p><div class="lk-location-canvas" role="region" aria-label="Bản đồ chọn vị trí địa điểm"></div><button type="button" class="lk-location-my-position">Dùng vị trí của tôi</button></div><p class="lk-location-feedback" role="status" hidden></p></div></fieldset>';
}
// Managed listings use this picker; legacy ListingPro editors keep their scripts.
add_action('admin_print_scripts',function(){global $post;if($post&&$post->post_type==='listing'&&get_post_meta($post->ID,'_lk_managed',true)){wp_dequeue_script('maps');wp_dequeue_script('jquery-droppin');}},100);
add_action('admin_notices',function(){if(!empty($_GET['lk_location_error']))echo '<div class="notice notice-error"><p>Tọa độ chưa được lưu. Vui lòng nhập đủ vĩ độ và kinh độ hợp lệ hoặc chọn lại ghim trên bản đồ.</p></div>';});
