<?php
if (PHP_SAPI !== 'cli') exit;
define('WP_HTTP_BLOCK_EXTERNAL',true);
require dirname(__DIR__).'/wp-load.php';
$mode=$argv[1]??'check';$id=(int)get_option('lk_feedback_review_fixture');
if($mode==='setup'){
 if(!$id||get_post_status($id)!=='publish'){
  $id=wp_insert_post(array('post_type'=>'listing','post_status'=>'publish','post_title'=>'Kiểm tra popup đánh giá — Feedback 1','post_author'=>1));
  update_post_meta($id,'_lk_managed',1);update_post_meta($id,'_lk_feedback_fixture',1);update_option('lk_feedback_review_fixture',$id,false);
 }
 echo wp_json_encode(array('id'=>$id,'url'=>get_permalink($id)),JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);exit;
}
if(!$id||!get_post_meta($id,'_lk_feedback_fixture',true))throw new RuntimeException('Missing review fixture');
$comments=get_comments(array('post_id'=>$id,'type'=>'lk_review','status'=>'approve'));
if($mode==='cleanup'){
 global $wpdb;
 $fixture_comments=$wpdb->get_col($wpdb->prepare("SELECT comment_ID FROM {$wpdb->comments} WHERE comment_post_ID=%d AND comment_type='lk_review'",$id));
 foreach($fixture_comments as $comment_id){$review=get_comment($comment_id);foreach(lk_review_photos($review) as $photo){if((int)get_post_field('post_parent',$photo)===$id)wp_delete_attachment($photo,true);}wp_trash_comment($comment_id);}wp_trash_post($id);
 echo wp_json_encode(array('fixture_status'=>get_post_status($id),'remaining_listings'=>count(lk_posts())));exit;
}
$review=$comments[0]??null;$checks=array('one_review_saved'=>count($comments)===1);
if($review){
 $checks['criteria']=get_comment_meta($review->comment_ID,'lk_criteria',true)===array('position'=>3,'space'=>5,'quality'=>5,'service'=>5,'price'=>5);
 $checks['score_4_6']=(float)get_comment_meta($review->comment_ID,'lk_rating',true)===4.6&&(float)get_post_meta($id,'_lk_rating',true)===4.6;
 $checks['one_photo']=count(lk_review_photos($review))===1;
 $checks['anonymous']=(bool)get_comment_meta($review->comment_ID,'lk_anonymous',true);
}
echo wp_json_encode($checks,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);exit(in_array(false,$checks,true)?1:0);
