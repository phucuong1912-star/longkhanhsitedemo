<?php
if(PHP_SAPI!=='cli')exit;
define('WP_HTTP_BLOCK_EXTERNAL',true);
require dirname(__DIR__).'/wp-load.php';
$checks=array();$fixtures=array();$baseline=count(lk_posts());
function field_check($name,$ok){global $checks;$checks[$name]=(bool)$ok;}
function closed_week(){return array_fill_keys(array_keys(lk_weekdays()),array('mode'=>'closed'));}
$raw=closed_week();
$raw['Monday']=array('mode'=>'open','open'=>array('09:00','13:00'),'close'=>array('12:00','17:00'));
$raw['Wednesday']=array('mode'=>'24h');
$raw['Thursday']=array('mode'=>'open','open'=>array('22:00'),'close'=>array('02:00'));
$schedule=lk_parse_schedule($raw);
field_check('valid_weekly_schedule',!is_wp_error($schedule));
foreach(array('2026-11-16 08:59'=>false,'2026-11-16 09:00'=>true,'2026-11-16 12:00'=>false,'2026-11-16 13:00'=>true,'2026-11-16 17:00'=>false,'2026-11-17 11:00'=>false,'2026-11-18 03:00'=>true,'2026-11-19 23:00'=>true,'2026-11-20 01:00'=>true,'2026-11-20 02:00'=>false) as $time=>$expected)field_check('status_'.$time,lk_schedule_status($schedule,new DateTimeImmutable($time,new DateTimeZone('Asia/Bangkok')))['opened']===$expected);
field_check('roundtrip_multiple_slots',lk_read_schedule(lk_schedule_options($schedule))===$schedule);
$closed=lk_parse_schedule(closed_week());field_check('all_closed_preserved',lk_read_schedule(lk_schedule_options($closed))===$closed);
$bad=$raw;$bad['Monday']['open'][1]='11:00';field_check('reject_same_day_overlap',is_wp_error(lk_parse_schedule($bad)));
$bad=$raw;$bad['Sunday']=array('mode'=>'open','open'=>array('22:00'),'close'=>array('02:00'));$bad['Monday']=array('mode'=>'open','open'=>array('01:00'),'close'=>array('03:00'));field_check('reject_week_wrap_overlap',is_wp_error(lk_parse_schedule($bad)));
$bad=$raw;$bad['Monday']['open'][0]='25:00';field_check('reject_invalid_time',is_wp_error(lk_parse_schedule($bad)));
$bad=$raw;$bad['Monday']['open'][0]='12:00';field_check('equal_times_require_24h_mode',is_wp_error(lk_parse_schedule($bad)));
$sunday=closed_week();$sunday['Sunday']=array('mode'=>'open','open'=>array('22:00'),'close'=>array('02:00'));$sunday=lk_parse_schedule($sunday);field_check('sunday_carries_into_monday',lk_schedule_status($sunday,new DateTimeImmutable('2026-11-16 01:00',new DateTimeZone('Asia/Bangkok')))['opened']);
$legacy=lk_read_schedule(array('business_hours'=>array('Monday'=>array('open'=>'9:00 am','close'=>'5:00 pm'))));field_check('legacy_hours_and_closed_days',$legacy['Monday']['slots'][0]===array('open'=>'09:00','close'=>'17:00')&&$legacy['Tuesday']['mode']==='closed');
try{
 foreach(array('inexpensive','moderate','') as $i=>$tier){$id=wp_insert_post(array('post_type'=>'listing','post_status'=>'publish','post_title'=>'Price tier verification '.($i+1),'post_author'=>1));$fixtures[]=$id;update_post_meta($id,'_lk_managed',1);update_post_meta($id,'lp_listingpro_options',array('price_status'=>$tier,'list_price'=>20000+10000*$i,'list_price_to'=>50000+10000*$i)+lk_schedule_options($schedule));}
 $_GET=array('q'=>'Price tier verification','price_status'=>array('moderate'));field_check('price_tier_filter',wp_list_pluck(lk_filtered_posts(lk_search_state()),'ID')===array($fixtures[1]));
 $_GET['min_price']=60000;$_GET['max_price']=70000;field_check('price_tier_and_numeric_filter',wp_list_pluck(lk_filtered_posts(lk_search_state()),'ID')===array($fixtures[1]));
 $_GET['min_price']=70000;field_check('price_tier_numeric_no_match',count(lk_filtered_posts(lk_search_state()))===0);
 $_GET=array('q'=>'Price tier verification','price_status'=>array('notsay'));field_check('unclassified_price_not_guessed',wp_list_pluck(lk_filtered_posts(lk_search_state()),'ID')===array($fixtures[2]));
 $always=closed_week();foreach($always as &$day)$day=array('mode'=>'24h');unset($day);$always=lk_parse_schedule($always);
 foreach($fixtures as $index=>$id){$options=get_post_meta($id,'lp_listingpro_options',true);update_post_meta($id,'lp_listingpro_options',array_merge($options,lk_schedule_options($index===1?$always:$closed)));}
 $_GET=array('q'=>'Price tier verification','open'=>1);field_check('open_filter_uses_weekly_hours',wp_list_pluck(lk_filtered_posts(lk_search_state()),'ID')===array($fixtures[1]));
 $admins=get_users(array('role'=>'administrator','number'=>1));wp_set_current_user($admins[0]->ID);
 $_POST=array('lk_data_nonce'=>wp_create_nonce('lk_save_data'),'lk_data'=>array('price_status'=>'pricey','hours'=>$raw));wp_update_post(array('ID'=>$fixtures[0],'post_title'=>'Price tier verification admin'));
 $saved=get_post_meta($fixtures[0],'lp_listingpro_options',true);field_check('admin_saves_tier_and_week',$saved['price_status']==='pricey'&&lk_read_schedule($saved)===$schedule);
 $_POST['lk_data']=array('address'=>'Admin update preserving week');wp_update_post(array('ID'=>$fixtures[0],'post_title'=>'Price tier verification admin preserved'));
 field_check('admin_does_not_flatten_week',lk_read_schedule(get_post_meta($fixtures[0],'lp_listingpro_options',true))===$schedule);
 $_POST['lk_data']=array('hours'=>$bad);wp_update_post(array('ID'=>$fixtures[0],'post_title'=>'Price tier verification invalid week'));
 field_check('invalid_admin_hours_preserve_saved_week',lk_read_schedule(get_post_meta($fixtures[0],'lp_listingpro_options',true))===$schedule);
 $_POST=array();$_GET=array();
 ob_start();lk_form(array('kind'=>'add-place'));$form=ob_get_clean();field_check('frontend_has_price_and_7_days',str_contains($form,'name="price_status"')&&substr_count($form,'data-hours-mode')===7);
 ob_start();lk_results();$results=ob_get_clean();field_check('tier_and_slider_both_render',str_contains($results,'name="price_status[]"')&&str_contains($results,'id="lk-min-price"'));
}finally{$_POST=array();$_GET=array();foreach($fixtures as $id)wp_trash_post($id);field_check('user_listings_preserved',count(lk_posts())===$baseline);}
echo wp_json_encode(array('checks'=>$checks,'passed'=>!in_array(false,$checks,true),'listings'=>count(lk_posts())),JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE),PHP_EOL;
exit(in_array(false,$checks,true)?1:0);
