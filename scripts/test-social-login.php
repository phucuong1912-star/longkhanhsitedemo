<?php
/** Local integration/adapter tests; never enable providers or call OAuth services. */
if(PHP_SAPI!=='cli')exit(1);
define('LK_LAYOUT_EXPORTING',true);require dirname(__DIR__).'/wp-load.php';
if(!in_array(wp_parse_url(home_url(),PHP_URL_HOST),array('localhost','127.0.0.1','::1'),true))throw new RuntimeException('Local only.');
function social_check($ok,$label){if(!$ok)throw new RuntimeException($label);echo 'PASS '.$label."\n";}
$snapshot=function(){return hash('sha256',serialize(array(get_option('nsl_google'),get_option('nsl_facebook'),get_option('nextend_social_login'),get_option('active_plugins'),get_option('users_can_register'))));};
$before=$snapshot();$manifest=hash_file('sha256',WP_CONTENT_DIR.'/lk-layouts/manifest.json');
$preview='';
if(class_exists('NextendSocialLogin')){
 foreach(array('facebook','google') as $id){
  $provider=NextendSocialLogin::$providers[$id]??null;if(!$provider)continue;
  // Render a visual fixture only. Do not enable the provider or request OAuth.
  $button=$provider->getConnectButton('default',home_url('/'),false,'login',ucfirst($id));
  social_check(str_contains($button,'data-provider="'.$id.'"')&&str_contains($button,'loginSocial='.$id),'Installed Nextend API renders the correct provider endpoint: '.$id);
  $preview.='<div class="nsl-container nsl-container-block"><div class="nsl-container-buttons">'.$button.'</div></div>';
 }
 ob_start();NextendSocialLogin::stylesWithoutTag();$css=ob_get_clean();
 file_put_contents(dirname(__DIR__).'/.codex-tools/social-ready-preview.html','<html><head><meta name="viewport" content="width=device-width,initial-scale=1"><style>'.$css.'</style><link rel="stylesheet" href="'.esc_url(get_stylesheet_directory_uri().'/assets/site.css').'"><link rel="stylesheet" href="'.esc_url(get_stylesheet_directory_uri().'/assets/feedback2.css').'"><link rel="stylesheet" href="'.esc_url(get_stylesheet_directory_uri().'/assets/feedback-final.css').'"></head><body class="lk-site"><div style="padding:16px;max-width:460px"><div class="lk-socials lk-social-oauth">'.$preview.'</div></div></body></html>');
}
final class Social_Test_Provider {
 public $state='not-configured';public $enabled=false;public $calls=array();
 function getState(){return $this->state;}function isEnabled(){return $this->enabled;}
 function getConnectButton(...$args){$this->calls[]=$args;return '<a href="'.esc_url(add_query_arg('loginSocial','google',wp_login_url())).'" data-plugin="nsl" data-provider="google" data-action="connect">'.$args[4].'</a>';}
}
$fake=new Social_Test_Provider();
foreach(array('not-configured','not-tested','disabled') as $state){$fake->state=$state;$fake->enabled=true;social_check(lk_social_provider_markup($fake,'Google')===''&&!$fake->calls,'No OAuth link for '.$state);}
$fake->state='enabled';$fake->enabled=false;social_check(lk_social_provider_markup($fake,'Google')==='','Disabled instance is not usable even if state reports enabled');
social_check(!lk_social_provider_ready(null),'Missing plugin/provider is handled');
$fake->enabled=true;$_GET['redirect_to']='https://outside.invalid/';
$html=lk_social_provider_markup($fake,'Google <script>');unset($_GET['redirect_to']);
social_check(str_contains($html,'data-plugin="nsl"')&&$fake->calls[0][1]===home_url('/'),'Ready adapter uses native Nextend button contract and same-site landing');
social_check(!str_contains($html,'<script>'),'Elementor custom labels cannot inject HTML');
ob_start();lk_social_signin(array('google_label'=>'Google QA','facebook_label'=>'Facebook QA'));$html=ob_get_clean();
social_check(!str_contains($html,'data-social=')&&!str_contains($html,'sẽ sớm'),'Placeholder click behavior is removed');
foreach(array('google','facebook') as $id){
 $p=class_exists('NextendSocialLogin')?(NextendSocialLogin::$enabledProviders[$id]??null):null;
 if(lk_social_provider_ready($p))social_check(str_contains($html,'data-provider="'.$id.'"'),'Configured provider has its own native link: '.$id);
 else social_check(str_contains($html,'disabled aria-disabled="true"'),'Unconfigured provider remains unavailable: '.$id);
}
$users=get_users(array('role'=>'administrator','number'=>1));if(!$users)throw new RuntimeException('Need an existing local administrator.');
wp_set_current_user($users[0]->ID);
try{
 ob_start();lk_social_signin();$signed=ob_get_clean();social_check($signed==='','Signed-in users do not get duplicate login actions');
 ob_start();lk_social_admin_status();$status=ob_get_clean();social_check(str_contains($status,'Callback:')&&!str_contains($status,'client_secret')&&!str_contains($status,'appid'),'Admin status shows callbacks and no credential fields');
}finally{wp_set_current_user(0);}
social_check($before===$snapshot()&&$manifest===hash_file('sha256',WP_CONTENT_DIR.'/lk-layouts/manifest.json'),'Provider settings, activation, registration policy and Elementor package untouched');
