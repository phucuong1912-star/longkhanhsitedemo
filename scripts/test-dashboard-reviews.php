<?php
/** Local integration: real submission/query/template hooks, rollback all fixtures. */
if(PHP_SAPI!=='cli')exit(1);
define('LK_LAYOUT_EXPORTING',true);
require_once dirname(__DIR__).'/wp-includes/plugin.php';
add_filter('pre_http_request',function(){return new WP_Error('local-test-http-disabled');},PHP_INT_MAX);
add_filter('pre_wp_mail','__return_true',PHP_INT_MAX);
require dirname(__DIR__).'/wp-load.php';
if(wp_parse_url(home_url(),PHP_URL_HOST)!=='localhost')throw new RuntimeException('Local only.');

function dashboard_review_check($ok,$label){if(!$ok)throw new RuntimeException($label);echo 'PASS '.$label."\n";}
class LK_Dashboard_Test_Redirect extends RuntimeException{public $url;function __construct($url){$this->url=$url;parent::__construct('Owned redirect');}}
global $wpdb;
$tables=array($wpdb->posts,$wpdb->postmeta,$wpdb->comments,$wpdb->commentmeta,$wpdb->users,$wpdb->usermeta,$wpdb->options,$wpdb->terms,$wpdb->term_taxonomy,$wpdb->term_relationships);
foreach($tables as $table){$engine=$wpdb->get_var($wpdb->prepare('SELECT ENGINE FROM information_schema.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=%s',$table));if($engine!=='InnoDB')throw new RuntimeException('Transactional fixtures required.');}
$fingerprint=function()use($wpdb,$tables){$rows=array();foreach($tables as $table)$rows[$table]=hash('sha256',serialize($wpdb->get_results("SELECT * FROM `$table` ORDER BY 1,2",ARRAY_A)));return $rows;};
$before=$fingerprint();$manifest=hash_file('sha256',WP_CONTENT_DIR.'/lk-layouts/manifest.json');$failed=false;
$old_query=$GLOBALS['wp_query'];$old_post=$GLOBALS['post']??null;$old_get=$_GET;$old_post_data=$_POST;$old_files=$_FILES;
$redirect=function($url){throw new LK_Dashboard_Test_Redirect($url);};
$ids=function($result){return array_map('intval',wp_list_pluck($result['items'],'comment_ID'));};
$wpdb->query('START TRANSACTION');
try{
 $suffix=strtolower(wp_generate_password(10,false));$users=array();
 foreach(array('reviewer','owner','other') as $role){$user=wp_insert_user(array('user_login'=>'lk_dashboard_'.$role.'_'.$suffix,'user_pass'=>wp_generate_password(24),'user_email'=>$role.'_'.$suffix.'@example.invalid','display_name'=>'Owned '.$role.' '.$suffix,'role'=>'subscriber'));if(is_wp_error($user))throw new RuntimeException('Cannot create owned account');$users[$role]=$user;}
 $make_listing=function($author,$status='publish',$password=''){
  $id=wp_insert_post(array('post_type'=>'listing','post_status'=>$status,'post_author'=>$author,'post_title'=>'Owned dashboard listing','post_password'=>$password),true);
  if(is_wp_error($id))throw new RuntimeException('Cannot create owned listing');update_post_meta($id,'_lk_managed',1);return $id;
 };
 $place=$make_listing($users['owner']);$other_place=$make_listing($users['other']);$private=$make_listing($users['owner'],'private');$protected=$make_listing($users['owner'],'publish','fixture-password');
 $page=wp_insert_post(array('post_type'=>'page','post_status'=>'publish','post_title'=>'Owned dashboard fixture','post_author'=>$users['owner']));
 $GLOBALS['wp_query']=new WP_Query(array('page_id'=>$page));$GLOBALS['post']=get_post($page);
 wp_set_current_user($users['reviewer']);$_GET=array('dashboard'=>'reviews');$_FILES=array();
 $_POST=array('lk_nonce'=>wp_create_nonce('lk_review'),'place_id'=>$place,'criteria'=>array_fill_keys(array_keys(lk_criteria_labels()),4),'review'=>'Owned submitted review '.$suffix,'review_title'=>'Owned title '.$suffix);
 $native_before=(int)$wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type='lp-reviews'");
 add_filter('wp_redirect',$redirect,999);
 try{lk_handle_review();throw new RuntimeException('Expected redirect');}catch(LK_Dashboard_Test_Redirect $sent){dashboard_review_check($sent->url===get_permalink($place).'#reviews','Real review form redirects to the reviewed listing');}
 remove_filter('wp_redirect',$redirect,999);$_POST=array();
 $submitted=lk_dashboard_reviews();$saved=$submitted['items'][0]??null;
 dashboard_review_check($submitted['total']===1&&$saved&&$saved->comment_type==='lk_review'&&(int)$saved->user_id===$users['reviewer']&&$saved->comment_approved==='1','Real submission is saved under the signed-in user and immediately appears in history');
 dashboard_review_check(!array_filter(getAllReviewsArray(true))&&(int)$wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type='lp-reviews'")===$native_before,'Native-only query reproduces the missing history; the fix creates no duplicate review posts');
 $comment=function($author,$listing,$status='1',$anonymous=false,$type='lk_review')use($users,$suffix){
  $u=get_userdata($users[$author]);$id=wp_insert_comment(array('comment_post_ID'=>$listing,'user_id'=>$u->ID,'comment_author'=>$u->display_name,'comment_author_email'=>$u->user_email,'comment_content'=>'Owned '.$author.' content '.$suffix,'comment_type'=>$type,'comment_approved'=>$status));
  update_comment_meta($id,'lk_rating',4);if($anonymous)update_comment_meta($id,'lk_anonymous',1);return $id;
 };
 $pending=$comment('reviewer',$place,'0');$anonymous=$comment('reviewer',$place,'1',true);$outside=$comment('other',$other_place);
 $other_received=$comment('other',$place);$spam=$comment('reviewer',$place,'spam');$trash=$comment('reviewer',$place,'trash');$normal=$comment('reviewer',$place,'1',false,'comment');
 $hidden=$comment('reviewer',$private);$password_review=$comment('reviewer',$protected);
 $own=lk_dashboard_reviews();$own_ids=$ids($own);
 dashboard_review_check($own['total']===3&&in_array($pending,$own_ids,true)&&in_array($anonymous,$own_ids,true),'Own history includes pending and anonymous reviews');
 dashboard_review_check(!array_intersect($own_ids,array($outside,$other_received,$spam,$trash,$normal,$hidden,$password_review)),'History excludes other users, spam/trash, ordinary comments and nonpublic listings');
 ob_start();do_action('get_template_part_templates/dashboard/reviews');$panel=ob_get_clean();
 dashboard_review_check(str_contains($panel,'Owned title '.$suffix)&&str_contains($panel,'Chờ duyệt')&&str_contains($panel,'data-lk-native-reviews="0"'),'Dashboard template hook renders existing comments and suppresses the native empty-state display');
 dashboard_review_check(!str_contains($panel,'delete-my-review')&&!str_contains($panel,'data-reviewid='),'Comment IDs are not passed to native post edit/delete controls');
 wp_set_current_user($users['owner']);$received=lk_dashboard_reviews('received');$received_ids=$ids($received);
 dashboard_review_check(in_array((int)$saved->comment_ID,$received_ids,true)&&in_array($anonymous,$received_ids,true)&&in_array($other_received,$received_ids,true)&&!in_array($pending,$received_ids,true)&&!in_array($outside,$received_ids,true),'Listing owner sees approved reviews only for their own public listings');
 $anonymous_only=array('items'=>array(get_comment($anonymous)),'total'=>1,'page'=>1,'pages'=>1);
 ob_start();lk_dashboard_review_cards($anonymous_only,'received');$masked=ob_get_clean();
 dashboard_review_check(str_contains($masked,'Ẩn danh')&&!str_contains($masked,get_userdata($users['reviewer'])->display_name)&&!str_contains($masked,get_userdata($users['reviewer'])->user_email),'Received anonymous reviews do not disclose the reviewer name or email');
 wp_set_current_user($users['other']);dashboard_review_check(!in_array((int)$saved->comment_ID,$ids(lk_dashboard_reviews()),true),'Switching accounts does not expose the previous user history');
 wp_set_current_user(0);ob_start();lk_dashboard_reviews_panel();$guest=ob_get_clean();dashboard_review_check(lk_dashboard_reviews()['total']===0&&$guest==='','Logged-out requests return no private dashboard history');
 wp_set_current_user($users['reviewer']);
 for($n=0;$n<11;$n++)$comment('reviewer',$place);
 $first=lk_dashboard_reviews('submitted',1);$second=lk_dashboard_reviews('submitted',2);
 dashboard_review_check(count($first['items'])===10&&count($second['items'])===4&&!array_intersect($ids($first),$ids($second))&&$first['total']===14,'History paginates without losing or duplicating reviews');
 dashboard_review_check(lk_dashboard_reviews('submitted',999)['page']===2,'Out-of-range page requests are clamped to the last populated page');
 $_GET=array('dashboard'=>'reviews','lk_sent_page'=>array('bad'));dashboard_review_check(lk_dashboard_review_page('lk_sent_page')===1,'Malformed pagination input is ignored');
 $_GET=array('dashboard'=>'reviews');ob_start();lk_dashboard_review_cards($first,'submitted');$pagination=ob_get_clean();
 dashboard_review_check(str_contains($pagination,'dashboard=reviews')&&str_contains($pagination,'lk_sent_page=2'),'Pagination stays on the current environment dashboard permalink');
 $native=wp_insert_post(array('post_type'=>'lp-reviews','post_status'=>'publish','post_author'=>$users['reviewer'],'post_title'=>'Owned native review','post_content'=>'Preserved native content'));
 listing_set_metabox('listing_id',$place,$native);listing_set_metabox('rating',4,$native);
 $native_snapshot=get_post($native,ARRAY_A);$native_meta=get_post_meta($native);
 ob_start();get_template_part('templates/dashboard/reviews');$combined=ob_get_clean();
 dashboard_review_check(str_contains($combined,'data-lk-native-reviews="1"')&&str_contains($combined,'id="reviews"')&&str_contains($combined,'Preserved native content'),'Native dashboard template and existing review actions remain available alongside comment history');
 dashboard_review_check(get_post($native,ARRAY_A)===$native_snapshot&&get_post_meta($native)===$native_meta,'Reading combined history leaves native review data unchanged');
 $_GET=array('dashboard'=>'reviews-submited');ob_start();do_action('get_template_part_templates/dashboard/reviews-submited');$alias=ob_get_clean();dashboard_review_check(str_contains($alias,'Đánh giá đã gửi'),'Legacy submitted-review route also includes the same account history');
}catch(Throwable $error){$failed=true;fwrite(STDERR,$error->getMessage()."\n");}
finally{
 $wpdb->query('ROLLBACK');wp_cache_flush();wp_set_current_user(0);remove_filter('wp_redirect',$redirect,999);
 $GLOBALS['wp_query']=$old_query;$GLOBALS['post']=$old_post;$_GET=$old_get;$_POST=$old_post_data;$_FILES=$old_files;
}
try{dashboard_review_check($before===$fingerprint()&&$manifest===hash_file('sha256',WP_CONTENT_DIR.'/lk-layouts/manifest.json'),'All existing accounts, listings, reviews, options and Elementor layouts remain unchanged after rollback');}
catch(Throwable $error){$failed=true;fwrite(STDERR,$error->getMessage()."\n");}
exit($failed?1:0);
