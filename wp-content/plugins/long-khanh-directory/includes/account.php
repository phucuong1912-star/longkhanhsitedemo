<?php
if(!defined('ABSPATH'))exit;

// A query endpoint survives a renamed or deleted legacy profile page. No page
// creation or Elementor metadata migration is needed on the destination site.
function lk_auth_mode(){
 $mode=isset($_GET['lk_account'])&&is_string($_GET['lk_account'])?sanitize_key(wp_unslash($_GET['lk_account'])):'';
 return in_array($mode,array('login','register','lost','reset'),true)?$mode:'';
}
function lk_auth_url($mode='login',$args=array()){return add_query_arg(urlencode_deep(array_merge($args,array('lk_account'=>$mode))),home_url('/'));}
function lk_auth_redirect($mode,$args=array()){
 if(!isset($args['redirect_to'])&&$mode!=='reset'){$return=lk_auth_return_url();if($return!==home_url('/'))$args['redirect_to']=$return;}
 wp_safe_redirect(lk_auth_url($mode,$args));exit;
}
function lk_auth_return_url(){
 $url=$_POST['redirect_to']??$_GET['redirect_to']??'';
 // Elementor swaps the global post while rendering the login template. Return
 // to the queried page, never the template document's private permalink.
 $fallback=!lk_auth_mode()&&is_singular()?get_permalink(get_queried_object_id()):home_url('/');
 if(!lk_auth_mode()&&$fallback){
  if(($_GET['lk_feed']??'')==='following')$fallback=add_query_arg('lk_feed','following',$fallback);
  if(isset($_GET['place_id'])&&is_scalar($_GET['place_id']))$fallback=add_query_arg('place_id',absint($_GET['place_id']),$fallback);
 }
 if(!is_string($url)||trim($url)==='')return $fallback?:home_url('/');
 $safe=wp_validate_redirect(wp_unslash($url),home_url('/'));$parts=wp_parse_url($safe);$home=wp_parse_url(home_url('/'));
 if(isset($parts['host'])&&(strtolower($parts['host'])!==strtolower($home['host'])||($parts['port']??null)!==($home['port']??null)))return home_url('/');
 return $safe;
}
function lk_auth_rate_limit($scope,$identity){
 $key='lk_auth_'.hash_hmac('sha256',$scope.'|'.strtolower(trim($identity)),wp_salt('nonce'));
 $count=(int)get_transient($key);if($count>=5)return false;
 set_transient($key,$count+1,10*MINUTE_IN_SECONDS);return true;
}
function lk_register_account($username,$email,$password){
 if(!is_string($password)||strlen($password)<8||strlen($password)>4096)return new WP_Error('password');
 $username=sanitize_user($username,true);$email=sanitize_email($email);
 if(!$username||!validate_username($username)||strlen($username)>60)return new WP_Error('username');
 if(!is_email($email))return new WP_Error('email');
 if(username_exists($username))return new WP_Error('existing_user_login');
 if(email_exists($email))return new WP_Error('existing_user_email');
 return wp_insert_user(array('user_login'=>$username,'user_email'=>$email,'user_pass'=>$password,'role'=>'subscriber'));
}
function lk_auth_reset_email($message,$key,$login,$user){
 $url=lk_auth_url('reset',array('key'=>$key,'login'=>$login));
 return "Bạn vừa yêu cầu đặt lại mật khẩu tại ".wp_specialchars_decode(get_option('blogname'),ENT_QUOTES).".\r\n\r\nMở liên kết sau để tạo mật khẩu mới:\r\n".$url."\r\n\r\nNếu không phải bạn yêu cầu, hãy bỏ qua email này. Mật khẩu hiện tại vẫn được giữ nguyên.\r\n";
}
function lk_request_password_reset($identity){
 add_filter('retrieve_password_message','lk_auth_reset_email',20,4);
 try{return retrieve_password($identity);}
 finally{remove_filter('retrieve_password_message','lk_auth_reset_email',20);}
}
function lk_handle_lost_password(){
 lk_require_nonce('lk_lost');$identity=lk_post_value('identity');
 if(!$identity)lk_auth_redirect('lost',array('auth_error'=>'identity'));
 if(!lk_auth_rate_limit('lost-client',$_SERVER['REMOTE_ADDR']??'local')||!lk_auth_rate_limit('lost-identity',$identity))lk_auth_redirect('lost',array('auth_error'=>'wait'));
 $result=lk_request_password_reset($identity);
 if(is_wp_error($result)&&!in_array($result->get_error_code(),array('invalid_email','invalidcombo'),true))lk_auth_redirect('lost',array('auth_error'=>'mail'));
 // Do not reveal whether an email address has an account.
 lk_auth_redirect('lost',array('sent'=>1));
}
add_action('admin_post_nopriv_lk_lost','lk_handle_lost_password');
add_action('admin_post_lk_lost','lk_handle_lost_password');
function lk_reset_cookie_name(){return 'lk_reset_'.COOKIEHASH;}
function lk_reset_cookie($value){
 setcookie(lk_reset_cookie_name(),$value,array('expires'=>$value?time()+HOUR_IN_SECONDS:time()-HOUR_IN_SECONDS,'path'=>wp_parse_url(home_url('/'),PHP_URL_PATH)?:'/','secure'=>is_ssl(),'httponly'=>true,'samesite'=>'Lax'));
 if($value)$_COOKIE[lk_reset_cookie_name()]=$value;else unset($_COOKIE[lk_reset_cookie_name()]);
}
function lk_reset_user(){
 $value=$_COOKIE[lk_reset_cookie_name()]??'';
 if(!is_string($value)||strlen($value)>512||strpos($value,':')===false)return new WP_Error('expired');
 list($login,$key)=explode(':',wp_unslash($value),2);
 return check_password_reset_key($key,rawurldecode($login));
}
function lk_reset_account_password($password,$confirm){
 $user=lk_reset_user();if(is_wp_error($user))return new WP_Error('expired');
 if(!is_string($password)||strlen($password)<8||strlen($password)>4096)return new WP_Error('password');
 if(!is_string($confirm)||$password!==$confirm)return new WP_Error('confirm');
 $errors=new WP_Error();do_action('validate_password_reset',$errors,$user);
 if($errors->has_errors())return new WP_Error('password');
 reset_password($user,$password);return true;
}
function lk_handle_reset_password(){
 lk_require_nonce('lk_reset');
 $result=lk_reset_account_password(isset($_POST['password'])?wp_unslash($_POST['password']):'',isset($_POST['confirm_password'])?wp_unslash($_POST['confirm_password']):'');
 if(is_wp_error($result))lk_auth_redirect('reset',array('auth_error'=>$result->get_error_code()));
 lk_reset_cookie('');lk_auth_redirect('login',array('password_changed'=>1));
}
add_action('admin_post_nopriv_lk_reset','lk_handle_reset_password');
add_action('admin_post_lk_reset','lk_handle_reset_password');
add_action('template_redirect',function(){
 if(!lk_auth_mode())return;
 if(!defined('DONOTCACHEPAGE'))define('DONOTCACHEPAGE',true);
 nocache_headers();header('Referrer-Policy: no-referrer');header('X-Robots-Tag: noindex, nofollow');
 if(lk_auth_mode()==='reset'&&isset($_GET['key'])){
  $key=is_string($_GET['key'])?sanitize_text_field(wp_unslash($_GET['key'])):'';
  $login=isset($_GET['login'])&&is_string($_GET['login'])?wp_unslash($_GET['login']):'';
  if(is_wp_error(check_password_reset_key($key,$login))){lk_reset_cookie('');lk_auth_redirect('lost',array('auth_error'=>'expired'));}
  lk_reset_cookie(rawurlencode($login).':'.$key);lk_auth_redirect('reset');
 }
},0);
add_filter('template_include',function($template){return lk_auth_mode()?LK_DIR.'templates/account.php':$template;},120);
add_filter('redirect_canonical',function($url){return lk_auth_mode()?false:$url;});
add_filter('document_title_parts',function($parts){if(lk_auth_mode())$parts['title']=array('login'=>'Đăng nhập','register'=>'Tạo tài khoản','lost'=>'Quên mật khẩu','reset'=>'Đặt mật khẩu mới')[lk_auth_mode()];return $parts;});
add_action('wp_enqueue_scripts',function(){if(lk_auth_mode())wp_enqueue_style('lk-account',LK_URL.'assets/account.css',array(),filemtime(LK_DIR.'assets/account.css'));},130);

function lk_auth_password_field($name,$label,$new=false){
 echo '<label>'.esc_html($label).'<div class="lk-password"><input type="password" name="'.esc_attr($name).'" required '.($new?'minlength="8" maxlength="4096" autocomplete="new-password"':'autocomplete="current-password"').'><button type="button" class="lk-toggle-password" aria-label="Hiện mật khẩu">'.lk_icon('eye').'</button></div></label>';
}
function lk_auth_form($view='login',$s=array(),$heading='lk-account-title'){
 $titles=array('login'=>$s['login_title']??'Đăng nhập tài khoản','register'=>'Tạo tài khoản','lost'=>'Quên mật khẩu','reset'=>'Đặt mật khẩu mới');
 $error=isset($_GET['auth_error'])&&is_string($_GET['auth_error'])?sanitize_key($_GET['auth_error']):(isset($_GET['login_error'])?'login':(isset($_GET['register_error'])?'existing_user_email':''));
 $messages=array('login'=>'Email, tên người dùng hoặc mật khẩu chưa đúng. Vui lòng thử lại.','username'=>'Tên người dùng chưa hợp lệ. Dùng chữ không dấu, số hoặc dấu gạch dưới.','email'=>'Vui lòng nhập email hợp lệ.','password'=>'Mật khẩu cần có ít nhất 8 ký tự.','confirm'=>'Hai mật khẩu chưa trùng nhau. Vui lòng nhập lại.','existing_user_login'=>'Tên người dùng đã được sử dụng. Hãy chọn tên khác.','existing_user_email'=>'Email đã có tài khoản. Bạn có thể đăng nhập hoặc chọn Quên mật khẩu.','identity'=>'Vui lòng nhập email hoặc tên người dùng.','mail'=>'Chưa gửi được email. Vui lòng thử lại sau hoặc liên hệ quản trị viên.','wait'=>'Bạn đã thử nhiều lần. Vui lòng chờ 10 phút rồi thử lại.','expired'=>'Liên kết đặt lại mật khẩu không hợp lệ hoặc đã hết hạn. Hãy yêu cầu một email mới.');
 echo '<h2 class="lk-login-heading" id="'.esc_attr($heading).'">'.esc_html($titles[$view]).'</h2>';
 if($error)echo '<p class="lk-error" role="alert">'.esc_html($messages[$error]??'Chưa thực hiện được. Vui lòng kiểm tra thông tin và thử lại.').'</p>';
 if(isset($_GET['registered']))echo '<p class="lk-notice" role="status">Đã tạo tài khoản. Bạn có thể đăng nhập ngay.</p>';
 if(isset($_GET['password_changed']))echo '<p class="lk-notice" role="status">Đã đổi mật khẩu. Hãy đăng nhập bằng mật khẩu mới.</p>';
 if($view==='lost'&&isset($_GET['sent']))echo '<div class="lk-notice" role="status"><strong>Kiểm tra email của bạn</strong><p>Nếu thông tin khớp với một tài khoản, bạn sẽ nhận được liên kết đặt lại mật khẩu. Kiểm tra cả thư mục Spam/Thư rác.</p></div>';
 $can_reset=$view!=='reset'||!is_wp_error(lk_reset_user());
 if(!$can_reset)echo '<p class="lk-notice">Liên kết đã hết hạn hoặc không còn dùng được. <a href="'.esc_url(lk_auth_url('lost')).'">Gửi lại email đặt mật khẩu</a>.</p>';
 else{
  if($view==='lost')echo '<p>Nhập email hoặc tên người dùng. Chúng tôi sẽ gửi liên kết để bạn đặt mật khẩu mới.</p>';
  if($view==='register')echo '<p>Tạo tài khoản để lưu địa điểm và chia sẻ đánh giá.</p>';
  $action=array('login'=>'lk_login','register'=>'lk_register','lost'=>'lk_lost','reset'=>'lk_reset')[$view];
  echo '<form class="lk-form" action="'.esc_url(admin_url('admin-post.php')).'" method="post"><input type="hidden" name="action" value="'.$action.'">';wp_nonce_field($action,'lk_nonce');
  $prefix=wp_unique_id('lk-auth-');
  if($view==='lost')lk_input('identity','Email hoặc tên người dùng','text',true,'',$prefix);
  elseif($view!=='reset'){
   lk_input('username',$view==='register'?'Tên người dùng':($s['username_label']??'Email hoặc tên người dùng'),'text',true,'',$prefix);
   if($view==='register')lk_input('email','Email','email',true,'',$prefix);
  }
  if($view!=='lost')lk_auth_password_field('password',$view==='reset'?'Mật khẩu mới':'Mật khẩu',$view!=='login');
  if($view==='reset')lk_auth_password_field('confirm_password','Nhập lại mật khẩu mới',true);
  if(in_array($view,array('register','reset'),true))echo '<p class="lk-auth-help">Dùng ít nhất 8 ký tự; nên kết hợp chữ, số và ký hiệu.</p>';
  echo '<input type="hidden" name="redirect_to" value="'.esc_url(lk_auth_return_url()).'"><button type="submit" class="lk-button">'.esc_html(array('login'=>'Đăng nhập','register'=>'Tạo tài khoản','lost'=>isset($_GET['sent'])?'Gửi lại email':'Gửi liên kết đặt lại mật khẩu','reset'=>'Lưu mật khẩu mới')[$view]).'</button></form>';
 }
 if($view==='login'&&($s['show_social']??'yes')==='yes')lk_social_signin($s);
 $return=array('redirect_to'=>lk_auth_return_url());
 echo '<nav class="lk-auth-links lk-center" aria-label="Hỗ trợ tài khoản">';
 if($view==='login')echo '<a href="'.esc_url(lk_auth_url('lost',$return)).'">Quên mật khẩu?</a><p>Bạn chưa có tài khoản? <a href="'.esc_url(lk_auth_url('register',$return)).'">Tạo tài khoản</a></p>';
 else echo '<a href="'.esc_url(lk_auth_url('login',$return)).'">Quay lại đăng nhập</a>';
 echo '</nav>';
}

/** Repair only legacy functional destinations, keeping custom Elementor URLs. */
function lk_resolve_action_url($url){
 if(!is_string($url)||$url==='')return $url;
 $home=wp_parse_url(home_url('/'));$decoded=html_entity_decode($url,ENT_QUOTES,'UTF-8');
 if(strpos($decoded,'/')===0&&strpos($decoded,'//')!==0)$decoded=$home['scheme'].'://'.$home['host'].(isset($home['port'])?':'.$home['port']:'').$decoded;
 elseif(strpos($decoded,'://')===false&&strpos($decoded,'//')!==0)$decoded=home_url('/').$decoded;
 $parts=wp_parse_url($decoded);
 if(!$parts||!in_array($parts['scheme']??'',array('http','https'),true)||isset($parts['user'])||isset($parts['pass'])||strtolower($parts['host']??'')!==strtolower($home['host'])||($parts['port']??null)!==($home['port']??null))return $url;
 $base=trailingslashit($home['path']??'/');$path=untrailingslashit($parts['path']??'');$key='';
 foreach(array('profile','new-review','explore') as $candidate)if($path===$base.$candidate)$key=$candidate;
 if(!$key)return $url;
 parse_str($parts['query']??'',$args);
 if($key==='profile'){
  // Custom dashboard destinations and arbitrary page parameters remain editable.
  if(array_diff(array_keys($args),array('register','login_error','registered','register_error','redirect_to')))return $url;
  if(is_user_logged_in()&&!$args){$options=get_option('listingpro_options',array());$target=esc_url_raw($options['listing-author']??'')?:lk_auth_url();}
  else{$target=lk_auth_url(isset($args['register'])?'register':'login');unset($args['register']);}
 }else $target=lk_form_page_url($key);
 $target=$args?add_query_arg(urlencode_deep($args),$target):$target;
 return $target.(isset($parts['fragment'])?'#'.$parts['fragment']:'');
}
function lk_action_links_html($html){
 $processor=new WP_HTML_Tag_Processor($html);
 while($processor->next_tag('a')){$href=$processor->get_attribute('href');if(is_string($href)){$resolved=lk_resolve_action_url($href);if($resolved!==$href)$processor->set_attribute('href',$resolved);}}
 return $processor->get_updated_html();
}
add_filter('elementor/widget/render_content',function($html){return lk_action_links_html($html);},90);
function lk_action_link_nodes($nodes){
 foreach($nodes as $node){
  $settings=$node['settings']??array();$json=wp_json_encode($settings);
  if(preg_match('~/(?:profile|new-review|explore)/~',str_replace('\\/','/',$json)))return true;
  if(!empty($node['elements'])&&lk_action_link_nodes($node['elements']))return true;
 }
 return false;
}
function lk_action_link_cache_ttl($ttl){
 $document=class_exists('Elementor\Plugin')?\Elementor\Plugin::$instance->documents->get_current():null;
 return $document&&lk_action_link_nodes($document->get_elements_data())?'disable':$ttl;
}
add_filter('option_elementor_element_cache_ttl','lk_action_link_cache_ttl',45);
add_filter('default_option_elementor_element_cache_ttl','lk_action_link_cache_ttl',45);
