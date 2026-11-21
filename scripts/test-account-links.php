<?php
/** Local integration tests: real WP account/reset APIs, no delivered emails. */
if(PHP_SAPI!=='cli')exit(1);
define('LK_LAYOUT_EXPORTING',true);require dirname(__DIR__).'/wp-load.php';
if(wp_parse_url(home_url(),PHP_URL_HOST)!=='localhost')throw new RuntimeException('Local only.');
global $wpdb;$checks=array();$failed=false;$ids=array();$manifest=hash_file('sha256',WP_CONTENT_DIR.'/lk-layouts/manifest.json');
function account_check($ok,$label){global $checks;if(!$ok)throw new RuntimeException($label);$checks[]=$label;}
class LK_Account_Test_Redirect extends RuntimeException{public $url;function __construct($url){$this->url=$url;parent::__construct('Captured redirect');}}
$redirect=function($url){throw new LK_Account_Test_Redirect($url);};
$capture=function($handler,$post){$_POST=$post;try{$handler();}catch(LK_Account_Test_Redirect $r){return $r->url;}throw new RuntimeException('Missing redirect');};
$mail=array();$intercept=function($pre,$args)use(&$mail){$mail[]=$args;return true;};add_filter('pre_wp_mail',$intercept,100,2);
$map=get_option('lk_page_map',array());
$rename=function($url,$id)use($map){if($id===($map['new-review']??0))return home_url('/viet-danh-gia-moi/');if($id===($map['explore']??0))return home_url('/kham-pha-moi/');return $url;};
$layout_before=$wpdb->get_results("SELECT post_id,meta_key,meta_value FROM {$wpdb->postmeta} WHERE meta_key IN ('_elementor_data','_elementor_page_settings') ORDER BY meta_id",ARRAY_A);
foreach(array($wpdb->users,$wpdb->usermeta,$wpdb->options) as $table){$engine=$wpdb->get_var($wpdb->prepare('SELECT ENGINE FROM information_schema.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=%s',$table));if($engine!=='InnoDB')throw new RuntimeException('Need transactional account fixtures.');}
$wpdb->query('START TRANSACTION');ob_start();
try{
 wp_set_current_user(0);$_GET=array();$_SERVER['REMOTE_ADDR']='127.0.0.1';
 account_check(lk_form_page_url('profile')===lk_auth_url()&&strpos(lk_auth_url('register'),'profile')===false,'Deleted profile page is not required by account entry points');
 $nested=home_url('/explore/?lk_feed=following&query=a%20b#feed');parse_str(wp_parse_url(lk_auth_url('register',array('redirect_to'=>$nested)),PHP_URL_QUERY),$nested_args);
 account_check(($nested_args['redirect_to']??'')===$nested&&($nested_args['lk_account']??'')==='register','Nested return URLs retain every parameter and anchor');
 add_filter('page_link',$rename,999,2);
 account_check(lk_resolve_action_url(lk_link('new-review'))===home_url('/viet-danh-gia-moi/'),'Legacy review links follow a renamed destination page');
 account_check(lk_resolve_action_url(lk_link('explore').'?lk_feed=following#feed')===home_url('/kham-pha-moi/?lk_feed=following#feed'),'Following tab retains its filter and anchor on the renamed page');
 account_check(lk_resolve_action_url('https://custom.invalid/new-review/')==='https://custom.invalid/new-review/'&&lk_resolve_action_url(home_url('/custom-review/'))===home_url('/custom-review/'),'Custom and external Elementor destinations are preserved');
 $html='<a class="owner-class" target="_blank" rel="nofollow" href="'.esc_url(lk_link('new-review')).'">Owner label</a>';$fixed=lk_action_links_html($html);
 account_check(str_contains($fixed,'owner-class')&&str_contains($fixed,'Owner label')&&str_contains($fixed,'target="_blank"')&&str_contains($fixed,'rel="nofollow"')&&str_contains($fixed,'viet-danh-gia-moi'),'Runtime link repair preserves Elementor text, classes, target and rel');
 ob_start();lk_community_authors();$authors=ob_get_clean();
 account_check(!str_contains($authors,lk_link('new-review')),'Dynamic community calls to action use mapped page destinations');
 remove_filter('page_link',$rename,999);
 ob_start();lk_auth_form('login',array('show_social'=>'no'));$login=ob_get_clean();
 account_check(str_contains($login,esc_url(lk_auth_url('register',array('redirect_to'=>home_url('/')))))&&str_contains($login,'lk_account=lost')&&!str_contains($login,'wp-login.php?action=lostpassword'),'Login form links open frontend registration and password help');
 $old_query=$GLOBALS['wp_query'];$old_post=$GLOBALS['post']??null;
 try{
  $GLOBALS['wp_query']=new WP_Query(array('page_id'=>$map['explore']));$GLOBALS['post']=get_post(get_option('lk_template_login'));$_GET=array('lk_feed'=>'following');
  account_check(lk_auth_return_url()===add_query_arg('lk_feed','following',get_permalink($map['explore'])),'Popup login returns to the queried following feed rather than the Elementor template');
 }finally{$GLOBALS['wp_query']=$old_query;$GLOBALS['post']=$old_post;$_GET=array();}
 $suffix=strtolower(wp_generate_password(12,false));$name='lk_auth_test_'.$suffix.'. user';$email='lk_auth_test_'.$suffix.'@example.invalid';$password='Test-Password-'.wp_generate_password(20,false);
 account_check(is_wp_error(lk_register_account($name,$email,'short')),'Short registration passwords are rejected before creating accounts');
 $id=lk_register_account($name,$email,$password);if(is_wp_error($id))throw new RuntimeException('Cannot create owned account fixture');$ids[]=$id;
 account_check(get_userdata($id)->roles===array('subscriber')&&wp_check_password($password,get_userdata($id)->user_pass,$id),'Registration creates an ordinary subscriber with a valid password hash');
 account_check(is_wp_error(lk_register_account($name,$email,$password)),'Duplicate registration is rejected');
 add_filter('wp_redirect',$redirect,999);
 $return=$capture('lk_handle_register',array('lk_nonce'=>wp_create_nonce('lk_register'),'username'=>$name,'email'=>$email,'password'=>$password));
 account_check(str_contains($return,'lk_account=register')&&str_contains($return,'existing_user_login'),'Registration errors return to a usable registration form');
 $return=$capture('lk_handle_lost_password',array('lk_nonce'=>wp_create_nonce('lk_lost'),'identity'=>$email));
 account_check(str_contains($return,'sent=1')&&str_contains($return,'lk_account=lost')&&count($mail)===1,'Password request sends through the native mail API and confirms on the frontend');
 account_check(preg_match('~https?://[^\s]+~',$mail[0]['message'],$match)===1,'Reset email contains a usable URL');parse_str(wp_parse_url($match[0],PHP_URL_QUERY),$args);
 account_check(($args['lk_account']??'')==='reset'&&!is_wp_error(check_password_reset_key($args['key']??'',$name)),'Email reset URL carries a valid native WordPress reset key');
 account_check(!has_filter('retrieve_password_message','lk_auth_reset_email'),'Custom email formatting does not affect unrelated native reset requests');
 $mail_count=count($mail);$unknown=$capture('lk_handle_lost_password',array('lk_nonce'=>wp_create_nonce('lk_lost'),'identity'=>'missing_'.$suffix.'@example.invalid'));
 account_check($unknown===$return&&count($mail)===$mail_count,'Unknown accounts receive the same public confirmation without an email');
 $_COOKIE[lk_reset_cookie_name()]=rawurlencode($name).':'.$args['key'];
 account_check(is_wp_error(lk_reset_account_password($password.'new','different'))&&wp_check_password($password,get_userdata($id)->user_pass,$id),'Mismatched confirmation does not change the password');
 $changed=lk_reset_account_password($password.'new',$password.'new');clean_user_cache($id);
 account_check($changed===true&&wp_check_password($password.'new',get_userdata($id)->user_pass,$id)&&is_wp_error(check_password_reset_key($args['key'],$name)),'Successful reset changes the password and consumes the one-use key');
 account_check(is_wp_error(lk_reset_account_password($password,$password)),'A used reset key cannot reset the account again');
 $throttle='owned_'.$suffix;for($n=0;$n<5;$n++)account_check(lk_auth_rate_limit('test',$throttle),'Rate limit permits request '.($n+1));
 account_check(!lk_auth_rate_limit('test',$throttle),'Repeated requests are limited');
 $_POST=array('redirect_to'=>'https://external.invalid/');account_check(lk_auth_return_url()===home_url('/'),'Untrusted login return URLs are rejected');
 $_POST=array();wp_set_current_user($id);$options=get_option('listingpro_options',array());
 account_check(lk_resolve_action_url(lk_link('profile'))===(esc_url_raw($options['listing-author']??'')?:lk_auth_url()),'Logged-in account navigation uses the environment dashboard');
}catch(Throwable $error){$failed=true;fwrite(STDERR,$error->getMessage()."\n");}
finally{
 ob_end_clean();$wpdb->query('ROLLBACK');wp_cache_flush();wp_set_current_user(0);$_POST=array();$_GET=array();unset($_COOKIE[lk_reset_cookie_name()]);remove_filter('page_link',$rename,999);remove_filter('wp_redirect',$redirect,999);remove_filter('pre_wp_mail',$intercept,100);
}
try{
 foreach($ids as $id)account_check(!$wpdb->get_var($wpdb->prepare("SELECT ID FROM {$wpdb->users} WHERE ID=%d",$id)),'Owned account fixture is rolled back');
 $layout_after=$wpdb->get_results("SELECT post_id,meta_key,meta_value FROM {$wpdb->postmeta} WHERE meta_key IN ('_elementor_data','_elementor_page_settings') ORDER BY meta_id",ARRAY_A);
 account_check($layout_before===$layout_after&&$manifest===hash_file('sha256',WP_CONTENT_DIR.'/lk-layouts/manifest.json'),'Elementor documents and versioned package remain unchanged');
}catch(Throwable $error){$failed=true;fwrite(STDERR,$error->getMessage()."\n");}
foreach($checks as $label)echo 'PASS '.$label."\n";
exit($failed?1:0);
