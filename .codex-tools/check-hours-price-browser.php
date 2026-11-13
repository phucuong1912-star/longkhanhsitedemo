<?php
if(PHP_SAPI!=='cli')exit;
define('WP_HTTP_BLOCK_EXTERNAL',true);
require dirname(__DIR__).'/wp-load.php';
global $wpdb;
$id=(int)$wpdb->get_var($wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE post_type='listing' AND post_title=%s ORDER BY ID DESC LIMIT 1",'Kiểm tra mức giá và lịch theo thứ 20261113'));
if(!$id)throw new RuntimeException('Missing browser-submitted fixture');
$options=get_post_meta($id,'lp_listingpro_options',true);
if(($options['gAddress']??'')!=='Địa chỉ kiểm thử nội bộ')throw new RuntimeException('Fixture guard failed');
$mode=$argv[1]??'check';
if($mode==='cleanup'){wp_trash_post($id);echo wp_json_encode(array('fixture_status'=>get_post_status($id),'listings'=>count(lk_posts())));exit;}
$schedule=lk_read_schedule($options,$id);
$checks=array(
 'tier_saved'=>($options['price_status']??'')==='moderate',
 'numeric_price_saved'=>(int)$options['list_price']===25000&&(int)$options['list_price_to']===100000,
 'two_monday_shifts'=>$schedule['Monday']['slots']===array(array('open'=>'09:00','close'=>'12:00'),array('open'=>'13:00','close'=>'17:00')),
 'tuesday_closed'=>$schedule['Tuesday']['mode']==='closed',
 'wednesday_24h'=>$schedule['Wednesday']['mode']==='24h',
 'thursday_overnight'=>$schedule['Thursday']['slots']===array(array('open'=>'22:00','close'=>'02:00')),
 'friday_closed'=>$schedule['Friday']['mode']==='closed',
 'week_copy_saved'=>$schedule['Saturday']===$schedule['Monday']&&$schedule['Sunday']===$schedule['Monday'],
);
if($mode==='publish'&&!in_array(false,$checks,true))wp_update_post(array('ID'=>$id,'post_status'=>'publish'));
echo wp_json_encode(array('checks'=>$checks,'id'=>$id,'status'=>get_post_status($id),'url'=>get_permalink($id)),JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
exit(in_array(false,$checks,true)?1:0);
