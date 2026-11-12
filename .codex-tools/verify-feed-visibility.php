<?php
if(PHP_SAPI!=='cli')exit;
require dirname(__DIR__).'/wp-load.php';
$admins=get_users(array('role'=>'administrator','number'=>1));wp_set_current_user($admins[0]->ID);
$marker='LK private visibility verification 20261112';
$post=wp_insert_post(array('post_type'=>'listing','post_status'=>'publish','post_title'=>$marker,'post_name'=>'lk-feed-visibility-verification','post_password'=>'local-fixture','post_content'=>$marker,'post_author'=>get_current_user_id()));
$comment=wp_insert_comment(array('comment_post_ID'=>$post,'comment_type'=>'lk_review','comment_approved'=>1,'comment_content'=>$marker,'comment_author'=>'Visibility verification','user_id'=>get_current_user_id()));
$checks=array();
try{
 foreach(array('password','private','draft') as $state){
  if($state!=='password')wp_update_post(array('ID'=>$post,'post_status'=>$state,'post_password'=>''));
  ob_start();lk_feed(array());lk_community_authors();$html=ob_get_clean();
  $checks[$state]=!str_contains($html,$marker)&&!str_contains($html,'Visibility verification');
 }
}finally{wp_trash_comment($comment);wp_trash_post($post);}
$result=array('checks'=>$checks,'test_post_status'=>get_post_status($post),'remaining_listings'=>count(lk_posts()),'ok'=>!in_array(false,$checks,true));
file_put_contents(__DIR__.'/feed-visibility-result.json',wp_json_encode($result,JSON_PRETTY_PRINT));echo wp_json_encode($result),PHP_EOL;
exit($result['ok']?0:1);
