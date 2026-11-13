<?php
if(!defined('ABSPATH'))exit;
function lk_form($s){
 $kind=$s['kind']??'contact';$error=isset($_GET['lk_error'])?sanitize_key($_GET['lk_error']):'';
 if($kind==='review'){lk_review_composer($s);return;}
 if(isset($_GET['lk_success']))echo '<div class="lk-notice" role="status">'.($kind==='contact'?'Đã nhận lời nhắn của bạn. Cảm ơn bạn đã góp ý.':'Đã nhận địa điểm. Nội dung đang chờ duyệt.').'</div>';
 if($error)echo '<div class="lk-error" role="alert">'.($error==='location'?'Tọa độ chưa hợp lệ. Nhập đủ vĩ độ, kinh độ hoặc chọn ghim trên bản đồ.':($error==='hours'?'Lịch mở cửa chưa hợp lệ. Kiểm tra giờ của từng thứ, tránh khung giờ trùng nhau; chọn Mở 24 giờ nếu mở cả ngày.':'Vui lòng kiểm tra các trường bắt buộc và thử lại. Thông tin chưa được gửi.')).'</div>';
 if($kind==='add-place'&&!is_user_logged_in()){echo '<div class="lk-notice">Bạn có thể xem trước biểu mẫu bên dưới. <button class="lk-login-open lk-text-button">Đăng nhập</button> để gửi địa điểm.</div>';}
 echo '<form class="lk-form" method="post" enctype="multipart/form-data" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="lk_'.($kind==='contact'?'contact':'add_place').'"><input type="hidden" name="return_to" value="'.esc_url(get_permalink()).'">';wp_nonce_field('lk_'.$kind,'lk_nonce');echo '<div class="lk-honeypot" aria-hidden="true"><label>Website xác nhận<input name="company_url" tabindex="-1" autocomplete="off"></label></div>';
 if($kind==='contact'){
  lk_input('name','Tên của bạn','text',true);lk_input('email','Email của bạn','email',true);lk_input('phone','SĐT của bạn','tel');lk_input('message','Lời nhắn của bạn','textarea',true);
 }else{
  echo '<h2>'.esc_html($s['basic_title']??'Thông tin cơ bản').'</h2>';lk_input('name','Tên địa điểm','text',true,'Nhập tên địa điểm');
  foreach(array('category'=>array('Danh mục','listing-category'),'region'=>array('Khu vực','location')) as $name=>$config){echo '<label>'.esc_html($config[0]).' <span aria-hidden="true">*</span><select name="'.$name.'" required><option value="">Chọn '.esc_html(mb_strtolower($config[0])).'</option>';foreach(lk_directory_terms($config[1]) as $t)echo '<option value="'.$t->term_id.'">'.esc_html($t->name).'</option>';echo '</select></label>';}
  lk_location_editor(array(),false,$s);lk_input('description','Giới thiệu','textarea');echo '<label class="lk-inline"><input type="checkbox" name="owner" value="1"> Bạn là chủ quán</label><h2>'.esc_html($s['other_title']??'Thông tin khác').'</h2>';lk_schedule_editor(null,'hours',$s);echo '<div class="lk-form-prices">';lk_price_status_select('price_status','notsay',$s['price_title']??'Thông tin giá');lk_input('min','Giá từ (VNĐ)','number');lk_input('max','Giá đến (VNĐ)','number');echo '</div><h2>'.esc_html($s['contact_title']??'Thông tin liên hệ').'</h2>';lk_input('phone','Điện thoại','tel');lk_input('email','Email','email');lk_input('facebook','Facebook','url');lk_input('website','Website','url');echo '<h2>'.esc_html($s['images_title']??'Hình ảnh').'</h2><label>Chọn tối đa 5 ảnh (JPG, PNG hoặc WebP, tối đa 5 MB/ảnh)<input type="file" name="photos[]" accept="image/jpeg,image/png,image/webp" multiple></label>';
 }
 echo '<button class="lk-button" type="submit">'.esc_html($kind==='add-place'?($s['add_place_button']??'Thêm địa điểm'):($s['button']??'Gửi')).'</button></form>';
}
function lk_input($name,$label,$type='text',$required=false,$placeholder=''){
 $id='lk-field-'.$name;echo '<label for="'.$id.'">'.esc_html($label).($required?' <span aria-hidden="true">*</span>':'');
 if($type==='textarea')echo '<textarea id="'.$id.'" name="'.$name.'" rows="5" '.($required?'required':'').'></textarea>';
 else echo '<input id="'.$id.'" name="'.$name.'" type="'.$type.'" '.($type==='number'?'min="0" ':'').'placeholder="'.esc_attr($placeholder).'" '.($required?'required':'').' '.($type==='email'?'autocomplete="email"':($type==='tel'?'autocomplete="tel"':'')).'>';
 echo '</label>';
}
function lk_account($mode='page',$s=array()){
 if(is_user_logged_in()){
  $u=wp_get_current_user();echo '<h2>'.esc_html($s['title']??'Tài khoản của bạn').'</h2><p>Xin chào, <strong>'.esc_html($u->display_name).'</strong>.</p><p><a href="'.esc_url(lk_link('add-place')).'" class="lk-button">Đóng góp địa điểm</a> <a href="'.esc_url(wp_logout_url(lk_link('profile'))).'">Đăng xuất</a></p><h3>Địa điểm đã lưu</h3><div class="lk-saved-places"></div>';return;
 }
 $register=$mode==='page'&&isset($_GET['register']);
 echo '<h2 id="lk-login-title">'.($register?'Tạo tài khoản':'Đăng nhập tài khoản').'</h2>';
 if(isset($_GET['login_error']))echo '<p class="lk-error" role="alert">Tên đăng nhập hoặc mật khẩu chưa đúng. Vui lòng thử lại.</p>';
 if(isset($_GET['registered']))echo '<p class="lk-notice" role="status">Đã tạo tài khoản. Bạn có thể đăng nhập ngay.</p>';
 if(isset($_GET['register_error']))echo '<p class="lk-error" role="alert">Tên đăng nhập hoặc email đã được sử dụng, hoặc mật khẩu chưa đủ 8 ký tự.</p>';
 echo '<form class="lk-form" action="'.esc_url(admin_url('admin-post.php')).'" method="post"><input type="hidden" name="action" value="'.($register?'lk_register':'lk_login').'">';wp_nonce_field($register?'lk_register':'lk_login','lk_nonce');
 lk_input('username',$register?'Tên người dùng':'Email hoặc tên người dùng','text',true);
 if($register)lk_input('email','Email','email',true);
 echo '<label>Mật khẩu<div class="lk-password"><input type="password" name="password" required '.($register?'minlength="8" autocomplete="new-password"':'autocomplete="current-password"').'><button type="button" class="lk-toggle-password" aria-label="Hiện mật khẩu">'.lk_icon('eye').'</button></div></label><input type="hidden" name="redirect_to" value="'.esc_url($mode==='login'?get_permalink():lk_link('profile')).'"><button type="submit" class="lk-button">'.($register?'Tạo tài khoản':'Đăng nhập').'</button></form>';
 echo '<p class="lk-center"><a href="'.esc_url(wp_lostpassword_url(lk_link('profile'))).'">Quên mật khẩu?</a></p><p class="lk-center">'.($register?'Bạn đã có tài khoản? <a href="'.esc_url(lk_link('profile')).'">Đăng nhập</a>':'Bạn chưa có tài khoản? <a href="'.esc_url(add_query_arg('register',1,lk_link('profile'))).'">Tạo tài khoản</a>').'</p>';
 if($mode==='page')echo '<h3>Địa điểm đã lưu trên thiết bị</h3><div class="lk-saved-places"></div>';
}
function lk_post_value($name){return isset($_POST[$name])?sanitize_text_field(wp_unslash($_POST[$name])):'';}
function lk_require_nonce($action){if(empty($_POST['lk_nonce'])||!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['lk_nonce'])),$action))wp_die('Phiên làm việc đã hết hạn. Vui lòng tải lại trang và thử lại.','Không thể gửi',array('response'=>403));}
function lk_redirect_form($slug,$args){wp_safe_redirect(add_query_arg($args,lk_link($slug)));exit;}
function lk_handle_contact(){
 lk_require_nonce('lk_contact');$name=lk_post_value('name');$email=sanitize_email(lk_post_value('email'));$message=isset($_POST['message'])?sanitize_textarea_field(wp_unslash($_POST['message'])):'';
 if(!$name||!is_email($email)||!$message||lk_post_value('company_url'))lk_redirect_form('contact',array('lk_error'=>'invalid'));
 $key='lk_contact_'.md5($email);if(get_transient($key))lk_redirect_form('contact',array('lk_error'=>'wait'));
 $id=wp_insert_post(array('post_type'=>'lk_message','post_status'=>'private','post_title'=>'Liên hệ từ '.$name,'post_content'=>$message),true);
 if(is_wp_error($id))lk_redirect_form('contact',array('lk_error'=>'save'));
 update_post_meta($id,'email',$email);update_post_meta($id,'phone',lk_post_value('phone'));set_transient($key,1,MINUTE_IN_SECONDS);lk_redirect_form('contact',array('lk_success'=>1));
}
add_action('admin_post_nopriv_lk_contact','lk_handle_contact');add_action('admin_post_lk_contact','lk_handle_contact');
function lk_handle_login(){
 lk_require_nonce('lk_login');$result=wp_signon(array('user_login'=>lk_post_value('username'),'user_password'=>isset($_POST['password'])?wp_unslash($_POST['password']):'','remember'=>true),is_ssl());
 if(is_wp_error($result))lk_redirect_form('profile',array('login_error'=>1));
 wp_safe_redirect(wp_validate_redirect(lk_post_value('redirect_to'),lk_link('profile')));exit;
}
add_action('admin_post_nopriv_lk_login','lk_handle_login');add_action('admin_post_lk_login','lk_handle_login');
function lk_handle_register(){
 lk_require_nonce('lk_register');$username=sanitize_user(lk_post_value('username'),true);$email=sanitize_email(lk_post_value('email'));$password=isset($_POST['password'])?wp_unslash($_POST['password']):'';
 if(!$username||!is_email($email)||strlen($password)<8||username_exists($username)||email_exists($email))lk_redirect_form('profile',array('register'=>1,'register_error'=>1));
 $id=wp_insert_user(array('user_login'=>$username,'user_email'=>$email,'user_pass'=>$password,'role'=>'subscriber'));
 if(is_wp_error($id))lk_redirect_form('profile',array('register'=>1,'register_error'=>1));lk_redirect_form('profile',array('registered'=>1));
}
add_action('admin_post_nopriv_lk_register','lk_handle_register');
function lk_handle_review(){
 lk_require_nonce('lk_review');$id=absint($_POST['place_id']??0);$rating=absint($_POST['rating']??0);$content=isset($_POST['review'])?sanitize_textarea_field(wp_unslash($_POST['review'])):'';
 $criteria=array();foreach(lk_criteria_labels() as $key=>$label){$score=absint($_POST['criteria'][$key]??0);if($score>=1&&$score<=5)$criteria[$key]=$score;}
 if(count($criteria)===5)$rating=round(array_sum($criteria)/5,1);
 elseif(isset($_POST['criteria']))wp_die('Vui lòng chọn điểm cho cả năm tiêu chí.','Không thể gửi',array('response'=>400));
 if(!is_user_logged_in()||get_post_type($id)!=='listing'||!lk_is_directory_listing($id)||get_post_status($id)!=='publish'||get_post_field('post_password',$id)||$rating<1||$rating>5||mb_strlen($content)<10||mb_strlen($content)>10000)wp_die('Vui lòng đăng nhập và nhập đầy đủ nội dung đánh giá.','Không thể gửi',array('response'=>400));
 lk_validate_review_uploads();$photos=lk_save_review_uploads($id);
 $u=wp_get_current_user();$comment=wp_insert_comment(array('comment_post_ID'=>$id,'comment_author'=>$u->display_name,'comment_author_email'=>$u->user_email,'user_id'=>$u->ID,'comment_content'=>$content,'comment_type'=>'lk_review','comment_approved'=>0));
 if($comment){if($criteria)update_comment_meta($comment,'lk_criteria',$criteria);update_comment_meta($comment,'lk_rating',$rating);update_comment_meta($comment,'lk_review_title',mb_substr(lk_post_value('review_title'),0,120));update_comment_meta($comment,'lk_anonymous',empty($_POST['anonymous'])?0:1);if($photos)update_comment_meta($comment,'lk_photo_ids',$photos);wp_set_comment_status($comment,'approve');}wp_safe_redirect(get_permalink($id).'#reviews');exit;
}
add_action('admin_post_lk_review','lk_handle_review');
function lk_handle_add_place(){
 lk_require_nonce('lk_add-place');if(!is_user_logged_in())lk_redirect_form('profile',array());
 $name=lk_post_value('name');$address=lk_post_value('address');$cat=absint($_POST['category']??0);$region=absint($_POST['region']??0);
 if(!$name||!$address||!term_exists($cat,'listing-category')||!term_exists($region,'location')||lk_post_value('company_url'))lk_redirect_form('add-place',array('lk_error'=>'invalid'));
 $coordinates=lk_parse_coordinates(wp_unslash($_POST['latitude']??''),wp_unslash($_POST['longitude']??''));if(is_wp_error($coordinates))lk_redirect_form('add-place',array('lk_error'=>'location'));
 $schedule=lk_parse_schedule(wp_unslash($_POST['hours']??array()));if(is_wp_error($schedule))lk_redirect_form('add-place',array('lk_error'=>'hours'));
 $price_status=lk_post_value('price_status');if(!array_key_exists($price_status,lk_price_status_labels()))lk_redirect_form('add-place',array('lk_error'=>'invalid'));
 $id=wp_insert_post(array('post_type'=>'listing','post_status'=>'pending','post_title'=>$name,'post_content'=>sanitize_textarea_field(wp_unslash($_POST['description']??'')),'post_author'=>get_current_user_id()),true);
 if(is_wp_error($id))lk_redirect_form('add-place',array('lk_error'=>'save'));
 wp_set_object_terms($id,array($cat),'listing-category');wp_set_object_terms($id,array($region),'location');update_post_meta($id,'_lk_managed',1);
 update_post_meta($id,'lp_listingpro_options',array('gAddress'=>$address,'phone'=>lk_post_value('phone'),'email'=>sanitize_email(lk_post_value('email')),'facebook'=>esc_url_raw(lk_post_value('facebook')),'website'=>esc_url_raw(lk_post_value('website')),'list_price'=>absint($_POST['min']??0),'list_price_to'=>absint($_POST['max']??0),'price_status'=>$price_status)+lk_schedule_options($schedule)+$coordinates);
 lk_save_legacy_hours($id,$schedule);
 if(!empty($_FILES['photos']['name'][0])){
  require_once ABSPATH.'wp-admin/includes/file.php';require_once ABSPATH.'wp-admin/includes/media.php';require_once ABSPATH.'wp-admin/includes/image.php';
  $ids=array();foreach(array_slice($_FILES['photos']['name'],0,5,true) as $i=>$filename){
   if($_FILES['photos']['error'][$i]!==UPLOAD_ERR_OK||$_FILES['photos']['size'][$i]>5*MB_IN_BYTES)continue;
   $tmp=$_FILES['photos']['tmp_name'][$i];$type=wp_check_filetype_and_ext($tmp,$filename,array('jpg|jpeg'=>'image/jpeg','png'=>'image/png','webp'=>'image/webp'));if(empty($type['type']))continue;
   $_FILES['lk_photo']=array('name'=>sanitize_file_name($filename),'type'=>$type['type'],'tmp_name'=>$tmp,'error'=>0,'size'=>$_FILES['photos']['size'][$i]);$attachment=media_handle_upload('lk_photo',$id);if(!is_wp_error($attachment))$ids[]=$attachment;
  }if($ids){set_post_thumbnail($id,$ids[0]);update_post_meta($id,'gallery_image_ids',implode(',',$ids));}
 }
 lk_redirect_form('add-place',array('lk_success'=>1));
}
add_action('admin_post_lk_add_place','lk_handle_add_place');add_action('admin_post_nopriv_lk_add_place',function(){lk_redirect_form('profile',array());});
