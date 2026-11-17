<?php
if(!defined('ABSPATH'))exit;

function lk_saved_ids($raw){
 if(!is_array($raw))return array();
 $ids=array();foreach($raw as $id)if(is_scalar($id)&&ctype_digit((string)$id)&&(int)$id>0)$ids[]=(int)$id;
 return array_values(array_unique($ids));
}
function lk_saved_public_posts($ids){
 return $ids?get_posts(array('post_type'=>'listing','post_status'=>'publish','has_password'=>false,'post__in'=>$ids,'orderby'=>'post__in','posts_per_page'=>-1)):array();
}
function lk_account_saved_payload(){
 $posts=lk_saved_public_posts(lk_saved_ids(get_user_meta(get_current_user_id(),'lp_saved_user_posts',true)));
 return array('ids'=>array_map('intval',wp_list_pluck($posts,'ID')),'places'=>array_map('lk_place_data',$posts));
}
function lk_account_saved_change($operation,$ids){
 if(!is_user_logged_in())return new WP_Error('authentication','Vui lòng đăng nhập lại.',array('status'=>401));
 if(!in_array($operation,array('save','remove','import'),true))return new WP_Error('operation','Thao tác chưa hợp lệ.');
 if(!is_array($ids)||!$ids||count($ids)>500||count($ids)!==count(lk_saved_ids($ids)))return new WP_Error('ids','Danh sách địa điểm chưa hợp lệ.');
 $ids=lk_saved_ids($ids);
 if($operation!=='import'&&count($ids)!==1)return new WP_Error('ids','Chọn một địa điểm.');
 if($operation!=='remove'&&count(lk_saved_public_posts($ids))!==count($ids))return new WP_Error('listing','Một số địa điểm không còn công khai. Vui lòng tải lại trang.');
 global $wpdb;$uid=get_current_user_id();
 $lock='lk-saved-'.substr(hash('sha256',DB_NAME.$wpdb->prefix.$uid),0,48);
 if((string)$wpdb->get_var($wpdb->prepare('SELECT GET_LOCK(%s,2)',$lock))!=='1')return new WP_Error('busy','Danh sách đang được cập nhật. Vui lòng thử lại.',array('status'=>409));
 try{
  wp_cache_delete($uid,'user_meta');$before=get_user_meta($uid,'lp_saved_user_posts',true);
  if($before===''||$before===false)$before=array();
  if(!is_array($before))return new WP_Error('format','Dữ liệu đã lưu chưa đúng định dạng; chưa thực hiện thay đổi.',array('status'=>409));
  // Preserve all existing entries, including listings outside this integration.
  $after=$before;
  if($operation==='remove'){
   foreach($after as $key=>$value)if(is_scalar($value)&&ctype_digit((string)$value)&&in_array((int)$value,$ids,true))unset($after[$key]);
  }else foreach($ids as $id)if(!in_array($id,lk_saved_ids($after),true))$after[]=$id;
  if($after!==$before){
   // Also detect native ListingPro changes made outside our advisory lock.
   update_user_meta($uid,'lp_saved_user_posts',$after,$before);
   wp_cache_delete($uid,'user_meta');
   if(get_user_meta($uid,'lp_saved_user_posts',true)!==$after)return new WP_Error('write','Chưa lưu được thay đổi. Vui lòng thử lại.',array('status'=>500));
  }
  return lk_account_saved_payload();
 }finally{$wpdb->get_var($wpdb->prepare('SELECT RELEASE_LOCK(%s)',$lock));}
}
function lk_saved_places_ajax(){
 if(($_SERVER['REQUEST_METHOD']??'')!=='POST')wp_send_json_error(array('message'=>'Yêu cầu chưa hợp lệ.'),405);
 if(!is_user_logged_in())wp_send_json_error(array('message'=>'Vui lòng đăng nhập lại.'),401);
 $nonce=is_scalar($_POST['nonce']??null)?sanitize_text_field(wp_unslash($_POST['nonce'])):'';
 if(!wp_verify_nonce($nonce,'lk_saved_places'))wp_send_json_error(array('message'=>'Phiên đăng nhập đã hết hạn. Vui lòng tải lại trang.'),403);
 $operation=is_scalar($_POST['operation']??null)?sanitize_key($_POST['operation']):'';
 if($operation==='list')$result=lk_account_saved_payload();
 else{$raw=is_string($_POST['ids']??null)?json_decode(wp_unslash($_POST['ids']),true):null;$result=lk_account_saved_change($operation,$raw);}
 if(is_wp_error($result)){$data=$result->get_error_data();wp_send_json_error(array('message'=>$result->get_error_message()),is_array($data)?($data['status']??400):400);}
 wp_send_json_success($result);
}
add_action('wp_ajax_lk_saved_places','lk_saved_places_ajax');
add_action('wp_ajax_nopriv_lk_saved_places',function(){wp_send_json_error(array('message'=>'Vui lòng đăng nhập lại.'),401);});
function lk_saved_client_config(){
 $options=get_option('listingpro_options',array());$dashboard=esc_url_raw($options['listing-author']??'',array('http','https'));
 return array('url'=>admin_url('admin-ajax.php'),'nonce'=>is_user_logged_in()?wp_create_nonce('lk_saved_places'):'','dashboard'=>$dashboard?add_query_arg('dashboard','saved',$dashboard):'');
}
