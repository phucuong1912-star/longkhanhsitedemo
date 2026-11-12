<?php
if(PHP_SAPI!=='cli')exit;
require dirname(__DIR__).'/wp-load.php';
$admins=get_users(array('role'=>'administrator','number'=>1));wp_set_current_user($admins[0]->ID);
$mode=$argv[1]??'check';$fixture=get_option('lk_expansion_probe');
if($mode==='setup'){
 if($fixture&&get_post_status($fixture['post'])==='publish'){echo wp_json_encode(array('url'=>lk_link('explore'),'author'=>$fixture['user'])),PHP_EOL;exit;}
 $suffix=strtolower(wp_generate_password(8,false));
 $user=wp_insert_user(array('user_login'=>'lk_probe_'.$suffix,'user_pass'=>wp_generate_password(48,true,true),'user_email'=>'lk-probe-'.$suffix.'@example.invalid','display_name'=>'Kiểm tra theo dõi nội bộ','role'=>'subscriber'));
 if(is_wp_error($user))throw new RuntimeException($user->get_error_message());update_user_meta($user,'_lk_test_artifact',1);
 $post=wp_insert_post(array('post_type'=>'listing','post_status'=>'publish','post_title'=>'Địa điểm kiểm tra mở rộng','post_name'=>'lk-expansion-'.$suffix,'post_content'=>'Bài thử dành riêng cho kiểm tra nội bộ.','post_author'=>get_current_user_id(),'ping_status'=>'closed'));
 update_post_meta($post,'_lk_test_artifact',1);$sample=(int)get_option('lk_listing_preview');update_post_meta($post,'lp_listingpro_options',get_post_meta($sample,'lp_listingpro_options',true));set_post_thumbnail($post,get_post_thumbnail_id($sample));
 $term=wp_insert_term('Danh mục kiểm tra nội bộ','listing-category',array('slug'=>'lk-category-'.$suffix));$term=(int)$term['term_id'];update_term_meta($term,'_lk_test_artifact',1);wp_set_object_terms($post,array($term),'listing-category');
 $comment=wp_insert_comment(array('comment_post_ID'=>$post,'comment_author'=>'Kiểm tra theo dõi nội bộ','comment_author_email'=>'lk-probe-'.$suffix.'@example.invalid','user_id'=>$user,'comment_type'=>'lk_review','comment_approved'=>1,'comment_content'=>'Nội dung thử để kiểm tra mục đang theo dõi.'));
 update_comment_meta($comment,'lk_rating',4);$fixture=array('user'=>$user,'post'=>$post,'term'=>$term,'comment'=>$comment,'owner'=>get_current_user_id());update_option('lk_expansion_probe',$fixture);
 echo wp_json_encode(array('url'=>lk_link('explore'),'author'=>$user)),PHP_EOL;exit;
}
if(!$fixture)throw new RuntimeException('Missing named fixture');
$checks=array();$failures=array();
try{
 $index=lk_directory_index();$checks['new_post_is_first']=!empty($index)&&$index[0]['id']===$fixture['post'];
 $options=get_post_meta($fixture['post'],'lp_listingpro_options',true);$options['gAddress']='Địa chỉ mới sau khi làm ấm cache';update_post_meta($fixture['post'],'lp_listingpro_options',$options);
 $checks['cache_invalidates_after_edit']=lk_place_data($fixture['post'])['address']===$options['gAddress'];
 $checks['new_category_in_filter']=in_array($fixture['term'],array_map('intval',wp_list_pluck(lk_directory_terms('listing-category'),'term_id')),true);
 $checks['unfollow_saved']=!in_array($fixture['user'],lk_following_ids(),true);
 foreach(lk_posts() as $post){$response=wp_remote_get(get_permalink($post),array('timeout'=>25));$body=wp_remote_retrieve_body($response);preg_match('/<h1[^>]*>(.*?)<\/h1>/s',$body,$heading);$ok=!is_wp_error($response)&&wp_remote_retrieve_response_code($response)===200&&html_entity_decode(strip_tags($heading[1]??''))===html_entity_decode(get_the_title($post))&&!preg_match('/Fatal error|Warning:|Deprecated:/',$body);if(!$ok)$failures[]=get_permalink($post);}
}finally{
 $following=array_values(array_diff(lk_following_ids(),array($fixture['user'])));update_user_meta($fixture['owner'],'_lk_following',$following);
 if(get_post_meta($fixture['post'],'_lk_test_artifact',true))wp_trash_post($fixture['post']);
 if(get_comment($fixture['comment']))wp_trash_comment($fixture['comment']);
 if(get_term_meta($fixture['term'],'_lk_test_artifact',true))wp_delete_term($fixture['term'],'listing-category');
 if(get_user_meta($fixture['user'],'_lk_test_artifact',true)){require_once ABSPATH.'wp-admin/includes/user.php';wp_delete_user($fixture['user']);}
}
$result=array('checks'=>$checks,'url_failures'=>$failures,'ok'=>!in_array(false,$checks,true)&&!$failures,'remaining_listings'=>count(lk_posts()));file_put_contents(__DIR__.'/expansion-result.json',wp_json_encode($result,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));echo wp_json_encode($result,JSON_UNESCAPED_UNICODE),PHP_EOL;exit($result['ok']?0:1);
