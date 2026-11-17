<?php
/** Local real-DB tests; rollback every account/content fixture. */
if(PHP_SAPI!=='cli')exit(1);
define('LK_LAYOUT_EXPORTING',true);require dirname(__DIR__).'/wp-load.php';
if(wp_parse_url(home_url(),PHP_URL_HOST)!=='localhost')throw new RuntimeException('Local only.');
add_filter('pre_wp_mail','__return_false');
function saved_check($ok,$label){if(!$ok)throw new RuntimeException($label);echo 'PASS '.$label."\n";}
global $wpdb;
$tables=array($wpdb->users,$wpdb->usermeta,$wpdb->posts,$wpdb->postmeta,$wpdb->comments,$wpdb->commentmeta,$wpdb->terms,$wpdb->term_taxonomy,$wpdb->term_relationships);
$fingerprint=function()use($wpdb,$tables){$result=array();foreach($tables as $table)$result[$table]=hash('sha256',serialize($wpdb->get_results("SELECT * FROM `$table` ORDER BY 1,2",ARRAY_A)));return $result;};
foreach($tables as $table){$row=$wpdb->get_row($wpdb->prepare('SHOW TABLE STATUS WHERE Name = %s',$table));if(strcasecmp($row->Engine,'InnoDB')!==0)throw new RuntimeException('Transactional fixture tables required.');}
$before=$fingerprint();$manifest=hash_file('sha256',WP_CONTENT_DIR.'/lk-layouts/manifest.json');$posts=lk_posts();if(count($posts)<3)throw new RuntimeException('Need three existing public local listings.');
$a=$posts[0]->ID;$b=$posts[1]->ID;$c=$posts[2]->ID;$failed=false;
$wpdb->query('START TRANSACTION');
try{
 $uid=wp_insert_user(array('user_login'=>'lk_saved_test_'.wp_generate_password(10,false),'user_pass'=>wp_generate_password(32),'role'=>'subscriber'));
 $other=wp_insert_user(array('user_login'=>'lk_saved_other_'.wp_generate_password(10,false),'user_pass'=>wp_generate_password(32),'role'=>'subscriber'));
 if(is_wp_error($uid)||is_wp_error($other))throw new RuntimeException('Cannot create transactional fixtures.');
 wp_set_current_user($uid);
 $draft=wp_insert_post(array('post_type'=>'listing','post_status'=>'draft','post_title'=>'Private saved fixture'));
 $private=wp_insert_post(array('post_type'=>'listing','post_status'=>'private','post_title'=>'Private saved fixture'));
 $password=wp_insert_post(array('post_type'=>'listing','post_status'=>'publish','post_password'=>'fixture-password','post_title'=>'Password saved fixture'));
 update_user_meta($uid,'lp_saved_user_posts',array(7=>(string)$a,11=>$draft));
 $old=get_user_meta($uid,'lp_saved_user_posts',true);
 saved_check(!is_wp_error(lk_account_saved_change('save',array($b))),'Subscriber save reaches native account data');
 saved_check(array_slice(get_user_meta($uid,'lp_saved_user_posts',true),0,2,true)===$old,'Existing native saved entries and keys are preserved');
 $native=getSaved();saved_check(in_array($b,$native,true)&&in_array((string)$a,$native,true),'ListingPro getSaved reads the same new bookmark');
 lk_account_saved_change('save',array($b));saved_check(count(array_filter(lk_saved_ids(get_user_meta($uid,'lp_saved_user_posts',true)),function($id)use($b){return $id===$b;}))===1,'Repeated save is idempotent');
 $payload=lk_account_saved_payload();saved_check(in_array($a,$payload['ids'],true)&&in_array($b,$payload['ids'],true)&&!in_array($draft,$payload['ids'],true),'API exposes only public saved listings');
 saved_check(is_wp_error(lk_account_saved_change('save',array($draft)))&&is_wp_error(lk_account_saved_change('save',array($private)))&&is_wp_error(lk_account_saved_change('save',array($password))),'Draft/password/private content cannot be newly saved');
 $snapshot=get_user_meta($uid,'lp_saved_user_posts',true);saved_check(is_wp_error(lk_account_saved_change('save',array(-$a)))&&get_user_meta($uid,'lp_saved_user_posts',true)===$snapshot,'Invalid IDs cannot mutate favorites');
 lk_account_saved_change('import',array($c));saved_check(in_array($c,getSaved(),true)&&in_array($b,getSaved(),true),'Explicit import merges without replacing existing account saves');
 lk_account_saved_change('remove',array($b));saved_check(!in_array($b,getSaved(),true)&&in_array($c,getSaved(),true),'Unsave is reflected in native dashboard source');
 update_user_meta($other,'lp_saved_user_posts',array($b));wp_set_current_user($other);
 saved_check(lk_account_saved_payload()['ids']===array($b),'Another account cannot read the previous user list');
 lk_account_saved_change('save',array($a));saved_check(!in_array($b,lk_saved_ids(get_user_meta($uid,'lp_saved_user_posts',true)),true),'Account mutation cannot change another user');
 update_user_meta($other,'lp_saved_user_posts','unexpected-format');saved_check(is_wp_error(lk_account_saved_change('save',array($a)))&&get_user_meta($other,'lp_saved_user_posts',true)==='unexpected-format','Unexpected legacy metadata is not overwritten');
 wp_set_current_user(0);saved_check(is_wp_error(lk_account_saved_change('save',array($a))),'Guest cannot write account metadata');
}catch(Throwable $error){$failed=true;fwrite(STDERR,$error->getMessage()."\n");}
finally{$wpdb->query('ROLLBACK');wp_cache_flush();wp_set_current_user(0);}
saved_check($before===$fingerprint()&&$manifest===hash_file('sha256',WP_CONTENT_DIR.'/lk-layouts/manifest.json'),'Rollback preserves existing users, content and Elementor package');
exit($failed?1:0);
