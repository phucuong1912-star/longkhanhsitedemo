<?php
/** Native admin save hooks, including reactivating PPD; every fixture rolls back. */
if(PHP_SAPI!=='cli')exit(1);
define('WP_ADMIN',true);define('LK_LAYOUT_EXPORTING',true);require dirname(__DIR__).'/wp-load.php';
if(wp_parse_url(home_url(),PHP_URL_HOST)!=='localhost')throw new RuntimeException('Local only.');
add_filter('pre_wp_mail','__return_false');
function native_ads_check($ok,$label){if(!$ok)throw new RuntimeException($label);echo 'PASS '.$label."\n";}
global $wpdb;$tables=array($wpdb->posts,$wpdb->postmeta,$wpdb->users,$wpdb->usermeta,$wpdb->options);
$campaign_table=$wpdb->prefix.'listing_campaigns';if($wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s',$wpdb->esc_like($campaign_table)))===$campaign_table)$tables[]=$campaign_table;
foreach($tables as $table){$row=$wpdb->get_row($wpdb->prepare('SHOW TABLE STATUS WHERE Name = %s',$table));if(!$row||strcasecmp($row->Engine,'InnoDB')!==0)throw new RuntimeException('Need transactional fixtures.');}
$fingerprint=function()use($wpdb,$tables){$data=array();foreach($tables as $table)$data[$table]=hash('sha256',serialize($wpdb->get_results("SELECT * FROM `$table` ORDER BY 1,2",ARRAY_A)));return $data;};
$before=$fingerprint();$manifest=hash_file('sha256',WP_CONTENT_DIR.'/lk-layouts/manifest.json');$failed=false;
$options=array_merge(get_option('listingpro_options',array()),array('lp_random_ads_switch'=>1,'lp_detail_page_ads_switch'=>1,'lp_top_in_search_page_ads_switch'=>1));$filter=function()use($options){return $options;};add_filter('pre_option_listingpro_options',$filter);
$wpdb->query('START TRANSACTION');
try{
 $uid=wp_insert_user(array('user_login'=>'lk_ads_native_test_'.wp_generate_password(12,false),'user_pass'=>wp_generate_password(40),'role'=>'administrator'));if(is_wp_error($uid))throw new RuntimeException('Fixture user failed.');wp_set_current_user($uid);
 $listing=wp_insert_post(array('post_type'=>'listing','post_status'=>'publish','post_title'=>'Native ad save fixture','post_author'=>$uid));
 // Earlier post date avoids the native create-invoice branch; no invoice fixtures.
 $ad=wp_insert_post(array('post_type'=>'lp-ads','post_status'=>'draft','post_title'=>'Native ad fixture','post_author'=>$uid,'post_date'=>'2026-01-01 00:00:00'));
 $form=array('post_type'=>'lp-ads','lp_meta_box_nonce'=>wp_create_nonce('post-type.php'),'ads_listing'=>(string)$listing,'ad_type'=>array_keys(lk_ads_placements()),'ads_mode'=>'byduration','duration'=>'10','budget'=>'','remaining_balance'=>'','click_performed'=>'','ad_date'=>wp_date('d-m-Y'));
 $_POST=$form;wp_update_post(array('ID'=>$ad,'post_status'=>'publish'));
 native_ads_check(has_action('save_post','save_lpads_meta')!==false&&has_action('save_post','savePostMeta')!==false,'Real native and generic admin save handlers are loaded');
 native_ads_check(listing_get_metabox_by_ID('campaign_id',$listing)===$ad&&get_post_meta($listing,'campaign_status',true)==='active','Native form save establishes the correct account-independent campaign mapping');
 foreach(array_keys(lk_ads_placements()) as $placement)native_ads_check((bool)lk_ads_eligible($ad,$placement),'Native form PPD 10 days is eligible: '.$placement);
 $expired=wp_date('d-m-Y',time()-DAY_IN_SECONDS);update_post_meta($ad,'campain_expire_date',$expired);
 $reason=lk_ads_eligible($ad,'lp_random_ads',true);native_ads_check(is_wp_error($reason)&&$reason->get_error_code()==='expired','Expired native date reports the precise blocking reason');
 $_POST=array();wp_update_post(array('ID'=>$ad,'post_status'=>'publish'));
 native_ads_check(get_post_meta($ad,'campain_expire_date',true)===$expired&&!lk_ads_eligible($ad,'lp_random_ads'),'Cron/programmatic saves never silently renew an expired campaign');
 $_POST=$form;$_POST['lp_meta_box_nonce']='invalid';wp_update_post(array('ID'=>$ad,'post_status'=>'publish'));
 native_ads_check(get_post_meta($ad,'campain_expire_date',true)===$expired,'Invalid nonce cannot renew the old expiry');
 $_POST=$form;wp_update_post(array('ID'=>$ad,'post_status'=>'publish'));
 $expected=current_datetime()->setTime(0,0)->modify('+10 days')->format('d-m-Y');
 native_ads_check(get_post_meta($ad,'campain_expire_date',true)===$expected,'Explicit authorized PPD update replaces a stale expiry with the entered remaining days');
 foreach(array_keys(lk_ads_placements()) as $placement)native_ads_check((bool)lk_ads_eligible($ad,$placement),'Renewed native PPD is eligible: '.$placement);
 $future=current_datetime()->modify('+30 days')->format('d-m-Y');update_post_meta($ad,'campain_expire_date',$future);wp_update_post(array('ID'=>$ad,'post_status'=>'publish'));
 native_ads_check(get_post_meta($ad,'campain_expire_date',true)===$future,'Existing future expiry dates remain unchanged');
 update_post_meta($ad,'campain_expire_date',$expired);$_POST=$form;$_POST['ads_mode']='perclick';wp_update_post(array('ID'=>$ad,'post_status'=>'publish'));
 native_ads_check(get_post_meta($ad,'campain_expire_date',true)===$expired,'PPC saves do not renew PPD dates or modify budgets');
 ob_start();lk_ads_admin_diagnostics(get_post($ad));$html=ob_get_clean();native_ads_check(strpos($html,'Spotlight')!==false&&strpos($html,'Trang chi tiết')!==false&&strpos($html,'Chưa hiển thị')!==false,'Admin diagnostics display per-placement blocking reasons');
 wp_set_current_user(0);ob_start();lk_ads_admin_diagnostics(get_post($ad));native_ads_check(ob_get_clean()==='','Diagnostics require edit permission');
}catch(Throwable $error){$failed=true;fwrite(STDERR,$error->getMessage()."\n");}
finally{$wpdb->query('ROLLBACK');$_POST=array();remove_filter('pre_option_listingpro_options',$filter);wp_cache_flush();wp_set_current_user(0);}
native_ads_check($before===$fingerprint()&&$manifest===hash_file('sha256',WP_CONTENT_DIR.'/lk-layouts/manifest.json'),'All admin save fixtures rolled back; existing data and Elementor preserved');
exit($failed?1:0);
