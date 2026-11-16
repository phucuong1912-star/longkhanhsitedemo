<?php
/** Local WordPress integration: exercise real handlers, roll back fixture writes. */
if(PHP_SAPI!=='cli')exit(1);
define('LK_LAYOUT_EXPORTING',true);
require dirname(__DIR__).'/wp-load.php';
if(!in_array(wp_parse_url(home_url(),PHP_URL_HOST),array('localhost','127.0.0.1','::1'),true))throw new RuntimeException('Local only.');
function form_redirect_check($ok,$message){if(!$ok)throw new RuntimeException($message);echo 'PASS '.$message."\n";}
function form_redirect_fingerprint(){
 global $wpdb;$result=array();
 foreach(array($wpdb->posts,$wpdb->postmeta,$wpdb->options,$wpdb->terms,$wpdb->term_taxonomy,$wpdb->term_relationships,$wpdb->comments,$wpdb->commentmeta,$wpdb->users,$wpdb->usermeta) as $table)
  $result[$table]=hash('sha256',serialize($wpdb->get_results("SELECT * FROM `$table` ORDER BY 1, 2",ARRAY_A)));
 return $result;
}
final class Form_Test_Redirect extends RuntimeException {
 public $url;
 function __construct($url){parent::__construct('Captured redirect');$this->url=$url;}
}
$map=get_option('lk_page_map',array());$page=(int)($map['add-place']??0);
if(!$page||get_post_type($page)!=='page'||get_post_status($page)!=='publish')throw new RuntimeException('Need the existing local add-place page.');
$users=get_users(array('role'=>'administrator','number'=>1));
if(!$users)throw new RuntimeException('Need an existing local administrator.');
wp_set_current_user($users[0]->ID);
$categories=get_terms(array('taxonomy'=>'listing-category','hide_empty'=>false,'number'=>1));
$regions=get_terms(array('taxonomy'=>'location','hide_empty'=>false,'number'=>1));
if(is_wp_error($categories)||is_wp_error($regions)||!$categories||!$regions)throw new RuntimeException('Need existing local category and location.');
global $wpdb;
foreach(array($wpdb->posts,$wpdb->postmeta,$wpdb->options,$wpdb->terms,$wpdb->term_taxonomy,$wpdb->term_relationships) as $table){
 $row=$wpdb->get_row($wpdb->prepare('SHOW TABLE STATUS WHERE Name = %s',$table));
 if(!$row||strcasecmp($row->Engine,'InnoDB')!==0)throw new RuntimeException('Transactional tables required.');
}
// Simulate the permalink returned on prod after the owner renames the page.
// Filters only affect this CLI process; no slug, route or option is saved.
$fixture_url=home_url('/them-dia-diem/');
$permalink_filter=function($url,$id)use($page,&$fixture_url){return (int)$id===$page?$fixture_url:$url;};
$redirect_filter=function($url){throw new Form_Test_Redirect($url);};
add_filter('page_link',$permalink_filter,999,2);add_filter('wp_redirect',$redirect_filter,999);
$hours=array();foreach(lk_weekdays() as $day=>$label)$hours[$day]=array('mode'=>'closed');
$submission=array('lk_nonce'=>wp_create_nonce('lk_add-place'),'name'=>'Local form redirect regression','address'=>'Local test address','category'=>$categories[0]->term_id,'region'=>$regions[0]->term_id,'hours'=>$hours,'price_status'=>'moderate','return_to'=>'https://external.invalid/');
$submit=function($values){$_POST=$values;try{lk_handle_add_place();}catch(Form_Test_Redirect $redirect){return $redirect->url;}throw new RuntimeException('Handler did not redirect.');};
$created=0;$created_hook=function($id)use(&$created){$created=$id;};add_action('lk_place_created',$created_hook);
$before=form_redirect_fingerprint();$failed=false;$started=false;
try{
 if($wpdb->query('START TRANSACTION')===false)throw new RuntimeException('Cannot start test transaction.');$started=true;
 $count=(int)$wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_type='listing'");
 $invalid=$submission;$invalid['name']='';
 form_redirect_check($submit($invalid)===add_query_arg('lk_error','invalid',$fixture_url),'Validation failure returns to the renamed page');
 $invalid=$submission;$invalid['latitude']='100';$invalid['longitude']='107';
 form_redirect_check($submit($invalid)===add_query_arg('lk_error','location',$fixture_url),'Coordinate failure returns to the renamed page');
 $invalid=$submission;$invalid['hours']=array();
 form_redirect_check($submit($invalid)===add_query_arg('lk_error','hours',$fixture_url),'Schedule failure returns to the renamed page');
 form_redirect_check((int)$wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_type='listing'")===$count,'Failed submissions do not create listings');
 form_redirect_check($submit($submission)===add_query_arg('lk_success',1,$fixture_url),'Successful submission uses the renamed page and ignores an external return_to');
 form_redirect_check($created&&get_post_status($created)==='pending'&&get_post_meta($created,'_lk_managed',true)&&(int)$wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_type='listing'")===$count+1,'Success creates exactly one pending listing');
 $fixture_url=home_url('/dia-diem-moi/');
 form_redirect_check(lk_form_page_url('add-place')===$fixture_url,'A later permalink change is picked up without code changes');
 ob_start();lk_account();$account=ob_get_clean();
 form_redirect_check(strpos($account,'href="'.esc_url($fixture_url).'"')!==false,'Account contribution button follows the same page permalink');
 $fallback_filter=function()use($map){unset($map['add-place']);return $map;};add_filter('pre_option_lk_page_map',$fallback_filter);
 try{form_redirect_check(lk_form_page_url('add-place')===lk_link('add-place'),'Legacy installations without a page mapping retain their original fallback');}
 finally{remove_filter('pre_option_lk_page_map',$fallback_filter);}
}catch(Throwable $error){$failed=true;fwrite(STDERR,$error->getMessage()."\n");}
finally{
 if($started)$wpdb->query('ROLLBACK');
 remove_filter('page_link',$permalink_filter,999);remove_filter('wp_redirect',$redirect_filter,999);remove_action('lk_place_created',$created_hook);
 wp_cache_flush();$_POST=array();$_FILES=array();wp_set_current_user(0);
}
if($before!==form_redirect_fingerprint()){fwrite(STDERR,"Database fingerprint changed after rollback.\n");$failed=true;}
else echo "PASS rollback preserves all existing content, options, users and layouts\n";
exit($failed?1:0);
