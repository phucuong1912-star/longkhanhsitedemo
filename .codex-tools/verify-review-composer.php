<?php
if(PHP_SAPI!=='cli')exit;
require dirname(__DIR__).'/wp-load.php';
$admins=get_users(array('role'=>'administrator','number'=>1));wp_set_current_user($admins[0]->ID);$file=__DIR__.'/composer-test.json';$mode=$argv[1]??'check';
if($mode==='prepare'){
 if(file_exists($file)){echo file_get_contents($file);exit;}
 $baseline=count(lk_posts());$id=wp_insert_post(array('post_type'=>'listing','post_status'=>'publish','post_title'=>'Kiểm tra biểu mẫu review Long Khánh','post_name'=>'lk-composer-verification-20261112','post_content'=>'Địa điểm dùng kiểm tra biểu mẫu và được dọn sau xác minh.','post_author'=>get_current_user_id()));
 foreach(array('listing-category','location') as $tax)wp_set_object_terms($id,wp_get_post_terms((int)get_option('lk_listing_preview'),$tax,array('fields'=>'ids')),$tax);
 set_post_thumbnail($id,get_post_thumbnail_id((int)get_option('lk_listing_preview')));
 $data=array('post_id'=>$id,'baseline_listing_count'=>$baseline,'compose_url'=>add_query_arg('place_id',$id,lk_link('new-review')));file_put_contents($file,wp_json_encode($data));echo wp_json_encode($data);exit;
}
$data=json_decode(file_get_contents($file),true);$id=(int)$data['post_id'];$comments=get_comments(array('post_id'=>$id,'type'=>'lk_review','status'=>'approve'));$comment=$comments[0]??null;
if($mode==='cleanup'){
 foreach($comments as $c){foreach(lk_review_photos($c) as $media)wp_trash_post($media);wp_trash_comment($c->comment_ID);}wp_trash_post($id);
 $data['cleanup']=array('post_status'=>get_post_status($id),'remaining_listings'=>count(lk_posts()));file_put_contents($file,wp_json_encode($data,JSON_PRETTY_PRINT));echo wp_json_encode($data['cleanup']);exit;
}
$checks=array('review_saved'=>(bool)$comment);
if($comment){$checks['title_saved']=get_comment_meta($comment->comment_ID,'lk_review_title',true)==='Kiểm tra review có ảnh và ẩn danh';$checks['criteria']=get_comment_meta($comment->comment_ID,'lk_criteria',true)===array('position'=>5,'space'=>4,'quality'=>3,'service'=>4,'price'=>4);$checks['rating']=(float)get_comment_meta($comment->comment_ID,'lk_rating',true)===4.0&&(float)get_post_meta($id,'_lk_rating',true)===4.0;$checks['photo']=count(lk_review_photos($comment))===1;$checks['anonymous']=get_comment_meta($comment->comment_ID,'lk_anonymous',true)==1&&str_contains(lk_review_public_name($comment),'*****');ob_start();lk_feed(array());$feed=ob_get_clean();$checks['anonymous_not_in_feed']=!str_contains($feed,'Nội dung kiểm tra review có ảnh và ẩn danh');}
$data['checks']=$checks;$data['ok']=!in_array(false,$checks,true);file_put_contents($file,wp_json_encode($data,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));echo wp_json_encode($data,JSON_UNESCAPED_UNICODE);exit($data['ok']?0:1);
