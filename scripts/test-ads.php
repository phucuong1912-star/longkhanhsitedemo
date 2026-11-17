<?php
/** Exercise native campaign metadata and billing locally, rolling every fixture back. */
if(PHP_SAPI!=='cli')exit(1);
define('LK_LAYOUT_EXPORTING',true);require dirname(__DIR__).'/wp-load.php';
if(wp_parse_url(home_url(),PHP_URL_HOST)!=='localhost')throw new RuntimeException('Local only.');
add_filter('pre_wp_mail','__return_false');
function ads_check($ok,$label){if(!$ok)throw new RuntimeException($label);echo 'PASS '.$label."\n";}
global $wpdb;
$tables=array($wpdb->posts,$wpdb->postmeta,$wpdb->users,$wpdb->usermeta,$wpdb->options,$wpdb->terms,$wpdb->term_taxonomy,$wpdb->term_relationships);
foreach($tables as $table){$row=$wpdb->get_row($wpdb->prepare('SHOW TABLE STATUS WHERE Name = %s',$table));if(strcasecmp($row->Engine,'InnoDB')!==0)throw new RuntimeException('Transactional fixtures require InnoDB.');}
$fingerprint=function()use($wpdb,$tables){$data=array();foreach($tables as $table)$data[$table]=hash('sha256',serialize($wpdb->get_results("SELECT * FROM `$table` ORDER BY 1,2",ARRAY_A)));return $data;};
$before=$fingerprint();$manifest=hash_file('sha256',WP_CONTENT_DIR.'/lk-layouts/manifest.json');$failed=false;
$settings=array_merge(get_option('listingpro_options',array()),array('lp_random_ads_switch'=>1,'lp_detail_page_ads_switch'=>1,'lp_top_in_search_page_ads_switch'=>1,'lp_random_ads_pc'=>10,'lp_detail_page_ads_pc'=>20,'lp_top_in_search_page_ads_pc'=>30));
$option_filter=function()use(&$settings){return $settings;};add_filter('pre_option_listingpro_options',$option_filter);
$wpdb->query('START TRANSACTION');
try{
 $owner=wp_insert_user(array('user_login'=>'lk_ads_test_'.wp_generate_password(12,false),'user_pass'=>wp_generate_password(40),'role'=>'subscriber'));if(is_wp_error($owner))throw new RuntimeException('Fixture user failed.');
 $make=function($title,$types,$mode='byduration',$balance=0)use($owner){
  $id=wp_insert_post(array('post_type'=>'listing','post_status'=>'publish','post_title'=>$title,'post_author'=>$owner));
  $ad=wp_insert_post(array('post_type'=>'lp-ads','post_status'=>'publish','post_title'=>'QA '.$title,'post_author'=>$owner));
  update_post_meta($id,'lp_listingpro_options',array('campaign_id'=>$ad,'gAddress'=>'Fixture address'));
  update_post_meta($id,'campaign_status','active');foreach($types as $type)update_post_meta($id,$type,'active');
  update_post_meta($ad,'lp_listingpro_options',array('ads_listing'=>$id,'ad_type'=>$types,'ads_mode'=>$mode,'duration'=>7,'remaining_balance'=>$balance,'ad_status'=>'active','unrelated'=>'preserve'));
  return array($id,$ad);
 };
 $spot='lp_random_ads';$search='lp_top_in_search_page_ads';$detail='lp_detail_page_ads';
 [$a,$ad]=$make('lkqaads Alpha',array($spot,$search,$detail));[$b,$bd]=$make('lkqaads Beta',array($spot));
 ads_check((bool)lk_ads_eligible($ad,$spot)&&!lk_ads_eligible($bd,$detail),'Native placement selection is enforced');
 ads_check(count(lk_ads_select($spot,6,array($a,$b)))===2&&count(lk_ads_select($search,6,array($a,$b)))===1,'Only eligible native campaigns populate each paid section');
 ads_check(lk_ads_select($spot,3,array())===array()&&lk_ads_select($detail,3,array($a),$a)===array(),'Empty search matches and the current detail listing cannot advertise');
 $settings[$spot.'_switch']=0;ads_check(!lk_ads_eligible($ad,$spot),'Disabled placement is respected');$settings[$spot.'_switch']=1;
 wp_update_post(array('ID'=>$ad,'post_status'=>'pending'));ads_check(!lk_ads_eligible($ad,$spot),'Unapproved campaigns stay hidden');wp_update_post(array('ID'=>$ad,'post_status'=>'publish'));
 listing_set_metabox('duration',0,$ad);ads_check(!lk_ads_eligible($ad,$spot),'Zero duration stays hidden');listing_set_metabox('duration',7,$ad);
 update_post_meta($ad,'campain_expire_date',wp_date('d-m-Y',time()-DAY_IN_SECONDS));ads_check(!lk_ads_eligible($ad,$spot),'Expired date is enforced even before cron runs');delete_post_meta($ad,'campain_expire_date');
 listing_set_metabox('ad_date',wp_date('d-m-Y',time()+2*DAY_IN_SECONDS),$ad);ads_check(!lk_ads_eligible($ad,$spot),'Future launches stay hidden');listing_set_metabox('ad_date',wp_date('d-m-Y'),$ad);
 foreach(array('draft','private') as $status){wp_update_post(array('ID'=>$a,'post_status'=>$status));ads_check(!lk_ads_eligible($ad,$spot),$status.' listing is excluded');}
 wp_update_post(array('ID'=>$a,'post_status'=>'publish','post_password'=>'secret'));ads_check(!lk_ads_eligible($ad,$spot),'Password-protected listing is excluded');wp_update_post(array('ID'=>$a,'post_password'=>''));
 update_post_meta($a,'campaign_status','inactive');ads_check(!lk_ads_eligible($ad,$spot),'Paused listing campaign is excluded');update_post_meta($a,'campaign_status','active');
 listing_set_metabox('campaign_id',$bd,$a);ads_check(!lk_ads_eligible($ad,$spot),'Stale campaign pointers cannot resurrect old ads');listing_set_metabox('campaign_id',$ad,$a);
 $state=lk_search_state();$state['q']='lkqaads Alpha';$filtered=wp_list_pluck(lk_filtered_posts($state),'ID');ads_check(array_column(lk_ads_select($search,6,$filtered),'id')===array($ad),'Search ads obey the same keyword and listing filters');
 $state['nearby']=true;ads_check(lk_ads_search_ids(array(get_post($a)),$state)===array(),'Nearby Ads exclude listings without coordinates like the client-side results');
 wp_set_current_user(0);$entry=lk_ads_eligible($ad,$spot);$url=lk_ads_url($entry);parse_str(wp_parse_url($url,PHP_URL_QUERY),$query);
 ads_check(($query['lk_ad_visit']??'')==='1'&&hash_equals(lk_ads_signature($ad,$spot,$query['expires'],$query['ticket']),$query['signature']),'Paid links have signed placement-specific tickets');
 ob_start();lk_card(get_post($a),'grid',$entry);$html=ob_get_clean();ads_check(strpos($html,'data-ad-id="'.$ad.'"')!==false&&substr_count($html,'lk_ad_visit')===2&&strpos($html,'Được tài trợ')!==false,'Both paid card links are tracked and clearly labeled');
 $native_url=function($url){return add_query_arg('ad_type','lp_random_ads_pc',$url);};add_filter('post_type_link',$native_url,999);lk_clear_directory_cache($a);
 ob_start();lk_card(get_post($a));$organic=ob_get_clean();ads_check(strpos(lk_place_data($a)['url'],'ad_type=')===false&&strpos($organic,'ad_type=')===false&&strpos($organic,'lk_ad_visit')===false,'Organic and JS card URLs cannot inherit native paid-click parameters');remove_filter('post_type_link',$native_url,999);
 wp_set_current_user($owner);ads_check(strpos(lk_ads_url($entry),'lk_ad_visit')===false&&!lk_ads_record_click($ad,$spot,$query['expires'],$query['ticket'],$query['signature']),'Listing owners cannot consume their campaign budget');wp_set_current_user(0);
 ads_check(lk_ads_record_click($ad,$spot,$query['expires'],$query['ticket'],$query['signature'])&&(int)listing_get_metabox_by_ID('click_performed',$ad)===1&&(int)listing_get_metabox_by_ID('duration',$ad)===7,'PPD click counts without changing days or charging fees');
 ads_check(!lk_ads_record_click($ad,$spot,$query['expires'],$query['ticket'],$query['signature'])&&(int)listing_get_metabox_by_ID('click_performed',$ad)===1,'Refreshing the same ticket does not count twice');
 $visitor_cookie=$_COOKIE['lk_ads_visitor'];unset($_COOKIE['lk_ads_visitor']);ads_check(lk_ads_record_click($ad,$spot,$query['expires'],$query['ticket'],$query['signature'])&&(int)listing_get_metabox_by_ID('click_performed',$ad)===2,'Another browser can use a shared cached card without losing its click');$_COOKIE['lk_ads_visitor']=$visitor_cookie;
 [$c,$cd]=$make('lkqaads PPC',array($spot,$detail,$search),'perclick',25);
 ads_check((bool)lk_ads_eligible($cd,$spot)&&!lk_ads_eligible($cd,$search),'PPC only displays where the remaining balance covers that placement');
 $ticket=wp_generate_password(24,false,false);$expires=time()+1000;$sig=lk_ads_signature($cd,$spot,$expires,$ticket);
 ads_check(!lk_ads_record_click($cd,$detail,$expires,$ticket,$sig)&&!lk_ads_record_click($cd,$spot,$expires,$ticket,'bad-signature'),'Tampered price placement or signature cannot charge');
 ads_check(lk_ads_record_click($cd,$spot,$expires,$ticket,$sig)&&(float)listing_get_metabox_by_ID('remaining_balance',$cd)===15.0&&(int)listing_get_metabox_by_ID('click_performed',$cd)===1,'PPC uses the native fee and updates native dashboard counters');
 $ticket=wp_generate_password(24,false,false);lk_ads_record_click($cd,$spot,$expires,$ticket,lk_ads_signature($cd,$spot,$expires,$ticket));
 ads_check((float)listing_get_metabox_by_ID('remaining_balance',$cd)===5.0&&!lk_ads_eligible($cd,$spot),'Insufficient residual credit stops further display and charging');
 ads_check(listing_get_metabox_by_ID('unrelated',$cd)==='preserve','Click bookkeeping preserves unrelated campaign metadata');
 [$d,$dd]=$make('lkqaads Last Click',array($spot),'perclick',10);$ticket=wp_generate_password(24,false,false);lk_ads_record_click($dd,$spot,$expires,$ticket,lk_ads_signature($dd,$spot,$expires,$ticket));
 ads_check((float)listing_get_metabox_by_ID('remaining_balance',$dd)===0.0&&get_post_status($dd)==='inactive'&&!lk_ads_eligible($dd,$spot),'Last affordable click expires the campaign through native logic');
 $nodes=array(array('id'=>'column','elType'=>'column','settings'=>array('css_classes'=>'custom-owner-style lk-map-column'),'elements'=>array(array('id'=>'existing','elType'=>'widget','widgetType'=>'heading','settings'=>array('title'=>'Prod custom content')))));$original=$nodes;$inserted=false;$rendered=lk_ads_detail_nodes($nodes,$inserted);
 ads_check($inserted&&count($rendered[0]['elements'])===2&&$rendered[0]['elements'][0]===$original[0]['elements'][0]&&$nodes===$original,'Detail ads append only to a render copy, preserving Elementor content and classes');
 $inserted=false;$again=lk_ads_detail_nodes($rendered,$inserted);ads_check($again===$rendered&&lk_ads_manual_nodes($rendered)===array(),'Repeated Elementor processing neither duplicates Ads nor mistakes automatic nodes for user edits');
 ads_check(lk_ads_manual_nodes(array(array('widgetType'=>'lk-ads','settings'=>array('placement'=>$detail))))===array($detail=>true),'Explicit Elementor Ads widgets replace the automatic placement');
 $widget=\Elementor\Plugin::$instance->widgets_manager->get_widget_types('lk-ads');$control=$widget?$widget->get_controls('placement'):array();
 ads_check($widget&&($control['type']??'')==='select'&&($control['default']??'')===$spot,'Elementor registers the dynamic Ads widget and placement control');
}catch(Throwable $error){$failed=true;fwrite(STDERR,$error->getMessage()."\n");}
finally{$wpdb->query('ROLLBACK');remove_filter('pre_option_listingpro_options',$option_filter);wp_cache_flush();wp_set_current_user(0);}
ads_check($before===$fingerprint()&&$manifest===hash_file('sha256',WP_CONTENT_DIR.'/lk-layouts/manifest.json'),'Rollback preserves existing users, content, options, campaign data and Elementor package');
exit($failed?1:0);
