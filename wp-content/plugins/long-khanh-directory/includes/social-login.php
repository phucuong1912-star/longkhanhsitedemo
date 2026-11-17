<?php
if(!defined('ABSPATH'))exit;

/** OAuth/state, token verification and account creation belong to Nextend. */
function lk_social_provider_ready($provider){
 return is_object($provider)&&method_exists($provider,'getState')&&method_exists($provider,'isEnabled')&&$provider->getState()==='enabled'&&$provider->isEnabled();
}
function lk_social_provider_markup($provider,$label){
 if(!lk_social_provider_ready($provider))return '';
 // Fixed same-site landing page, never an untrusted redirect_to or HTTP Host.
 return $provider->getConnectButton('default',home_url('/'),false,'login',esc_html($label));
}
function lk_social_signin($settings=array()){
 if(is_user_logged_in())return;
 echo '<div class="lk-social-signin"><div class="lk-social-divider">'.esc_html($settings['social_divider']??'hoặc tiếp tục với').'</div><div class="lk-socials lk-social-oauth">';
 $missing=false;
 foreach(array('facebook'=>'Facebook','google'=>'Google') as $id=>$name){
  $label=$settings[$id.'_label']??$name;
  $provider=class_exists('NextendSocialLogin')?(NextendSocialLogin::$enabledProviders[$id]??null):null;
  $button=lk_social_provider_markup($provider,$label);
  if($button!=='')echo '<div class="nsl-container nsl-container-block"><div class="nsl-container-buttons">'.$button.'</div></div>';
  else{$missing=true;echo '<button type="button" disabled aria-disabled="true" title="'.esc_attr($name.' hiện chưa khả dụng').'">'.lk_icon('lock').' '.esc_html($label).'</button>';}
 }
 echo '</div>';
 if($missing)echo '<p class="lk-social-status" role="status">Một số phương thức chưa khả dụng. Bạn có thể đăng nhập bằng email hoặc tên người dùng.</p>';
 echo '</div>';
}
function lk_social_admin_status(){
 if(!current_user_can('manage_options'))return;
 echo '<h2>Đăng nhập Google / Facebook</h2><p><a href="'.esc_url(admin_url('options-general.php?page=nextend-social-login')).'">Cấu hình trong Nextend Social Login</a>. Dùng callback do plugin hiển thị trên đúng môi trường.</p><ul>';
 foreach(array('google'=>'Google','facebook'=>'Facebook') as $id=>$name){
  $provider=class_exists('NextendSocialLogin')?(NextendSocialLogin::$providers[$id]??null):null;
  $state=$provider?$provider->getState():'plugin-unavailable';
  $labels=array('not-configured'=>'Chưa cấu hình ứng dụng','not-tested'=>'Chưa xác minh kết nối','disabled'=>'Chưa bật','enabled'=>'Đã bật','plugin-unavailable'=>'Plugin chưa sẵn sàng');
  echo '<li><strong>'.esc_html($name).'</strong>: '.esc_html($labels[$state]??'Chưa sẵn sàng');
  if($provider)echo ' — Callback: <code>'.esc_html($provider->getBaseRedirectUriForAppCreation()).'</code>';
  echo '</li>';
 }
 echo '</ul>';
}
