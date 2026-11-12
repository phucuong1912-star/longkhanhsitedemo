<?php
if(PHP_SAPI!=='cli')exit;
require dirname(__DIR__).'/wp-load.php';
$users=get_users(array('role'=>'administrator','number'=>1));wp_set_current_user($users[0]->ID);
$mode=$argv[1]??'check';$id=(int)get_option('lk_review_form_probe');
if($mode==='setup'){
 if(!$id||get_post_status($id)!=='publish'){
  $sample=(int)get_option('lk_listing_preview');
  $id=wp_insert_post(array('post_type'=>'listing','post_status'=>'publish','post_title'=>'Kiểm tra form đánh giá Long Khánh','post_name'=>'lk-review-form-check','post_content'=>'Địa điểm dùng riêng để kiểm tra biểu mẫu trong bản local.','post_author'=>get_current_user_id(),'comment_status'=>'open','ping_status'=>'closed'));
  update_post_meta($id,'_lk_test_artifact',1);update_option('lk_review_form_probe',$id);
  foreach(array('listing-category','location') as $taxonomy)wp_set_object_terms($id,wp_get_post_terms($sample,$taxonomy,array('fields'=>'ids')),$taxonomy);
  update_post_meta($id,'lp_listingpro_options',get_post_meta($sample,'lp_listingpro_options',true));set_post_thumbnail($id,get_post_thumbnail_id($sample));
 }
 echo wp_json_encode(array('id'=>$id,'url'=>get_permalink($id)),JSON_UNESCAPED_SLASHES),PHP_EOL;exit;
}
if(!$id||!get_post_meta($id,'_lk_test_artifact',true))throw new RuntimeException('Missing named test artifact');
if($mode==='cleanup'){wp_trash_post($id);echo 'Test listing moved to '.get_post_status($id),PHP_EOL;exit;}
$comments=get_comments(array('post_id'=>$id,'type'=>'lk_review','status'=>'approve'));
$result=array('review_count'=>count($comments),'overall'=>(float)get_post_meta($id,'_lk_rating',true),'criteria'=>lk_rating_criteria($id),'individual_elementor_data'=>(bool)get_post_meta($id,'_elementor_data',true));
$result['ok']=$result['review_count']===1&&$result['overall']===4.0&&$result['criteria']===array('position'=>5.0,'space'=>4.0,'quality'=>3.0,'service'=>4.0,'price'=>4.0)&&!$result['individual_elementor_data'];
// PHP round() may return integer zero/whole values; compare numeric values.
$result['ok']=$result['review_count']===1&&$result['overall']===4.0&&array_map('floatval',$result['criteria'])===array('position'=>5.0,'space'=>4.0,'quality'=>3.0,'service'=>4.0,'price'=>4.0)&&!$result['individual_elementor_data'];
file_put_contents(__DIR__.'/review-form-result.json',wp_json_encode($result,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));echo wp_json_encode($result,JSON_UNESCAPED_UNICODE),PHP_EOL;exit($result['ok']?0:1);
