<?php
/** Local, read-only checks for final-feedback data binding. */
if(PHP_SAPI!=='cli')exit(1);
require dirname(__DIR__).'/wp-load.php';
if(!in_array(wp_parse_url(home_url(),PHP_URL_HOST),array('localhost','127.0.0.1','::1'),true))throw new RuntimeException('Local only.');
function final_check($test,$label){if(!$test)throw new RuntimeException($label);echo 'PASS '.$label."\n";}
final_check(lk_final_rating_label(5,1)==='Tuyệt vời'&&lk_final_rating_label(4.2,1)==='Hài lòng','Rating wording follows score');
final_check(lk_final_rating_label(5,0)==='Chưa có đánh giá','No reviews takes precedence over legacy score');
$posts=lk_posts();if(!$posts)throw new RuntimeException('A local listing is required.');$id=$posts[0]->ID;
$unsafe=false;
$fixture=function($value,$object,$key)use($id,&$unsafe){
 if((int)$object===$id&&$key==='lp_listingpro_options')return array(array('facebook'=>$unsafe?'javascript:alert(1)':'https://www.facebook.com/example','website'=>$unsafe?'data:text/html,test':'https://example.com/?a=1&b=2'));
 return $value;
};
add_filter('get_post_metadata',$fixture,100,3);
$settings=lk_resolve_listing_settings('icon-list',array('lk_binding'=>'info'),$id);
$contacts=array_slice($settings['icon_list'],3);
final_check(count($contacts)===2&&$contacts[0]['link']['url']==='https://www.facebook.com/example'&&$contacts[1]['link']['url']==='https://example.com/?a=1&b=2','Both saved contact fields reach listing output');
$unsafe=true;$settings=lk_resolve_listing_settings('icon-list',array('lk_binding'=>'info'),$id);
final_check(count($settings['icon_list'])===3,'Unsafe contact schemes are rejected');
remove_filter('get_post_metadata',$fixture,100);
$old=$_GET;$_GET=array('collections'=>array('an-sang'));$state=lk_search_state();$_GET=$old;
final_check($state['collections']===array(),'Retired collection URLs cannot silently filter results');
