<?php
if(PHP_SAPI!=='cli')exit;
require dirname(__DIR__).'/wp-load.php';
$users=get_users(array('role'=>'administrator','number'=>1));wp_set_current_user($users[0]->ID);
$checks=array();$failures=array();
function check_url($url,$expected){
 global $checks,$failures;
 $response=wp_remote_get($url,array('timeout'=>25));
 if(is_wp_error($response)){$failures[]=$url.': '.$response->get_error_message();return;}
 $body=wp_remote_retrieve_body($response);$status=wp_remote_retrieve_response_code($response);
 preg_match('/<h1[^>]*>(.*?)<\/h1>/s',$body,$title);
 $good=$status===200&&str_contains(html_entity_decode($body),html_entity_decode($expected))&&!preg_match('/(?:Fatal error|Deprecated:|Warning:|Notice:)/',$body);
 $checks[]=array('url'=>$url,'status'=>$status,'h1'=>html_entity_decode(strip_tags($title[1]??'')),'ok'=>$good);
 if(!$good)$failures[]=$url;
}
foreach(lk_posts() as $p){check_url(get_permalink($p),get_the_title($p));check_url(get_permalink($p).'photo/','elementor-'.get_option('lk_template_listing_gallery'));check_url(get_permalink($p).'menu/','Menu của '.get_the_title($p));}
$sample=(int)get_option('lk_listing_preview');
$id=wp_insert_post(array('post_type'=>'listing','post_status'=>'publish','post_title'=>'Kiểm tra template tự động Long Khánh','post_name'=>'lk-template-verification','post_content'=>'Nội dung kiểm tra template tự động.','post_author'=>get_current_user_id()),true);
if(is_wp_error($id))throw new RuntimeException($id->get_error_message());
try{
 foreach(array('listing-category','location') as $taxonomy)wp_set_object_terms($id,wp_get_post_terms($sample,$taxonomy,array('fields'=>'ids')),$taxonomy);
 update_post_meta($id,'lp_listingpro_options',array('gAddress'=>'Địa chỉ kiểm tra template','phone'=>'','list_price'=>10000,'list_price_to'=>20000));
 set_post_thumbnail($id,get_post_thumbnail_id($sample));
 if(!get_post_meta($id,'_lk_managed',true)||get_post_meta($id,'_elementor_data',true))$failures[]='New listing did not inherit the data-only model';
 check_url(get_permalink($id),'Kiểm tra template tự động Long Khánh');
 check_url(get_permalink($id).'photo/','Hình ảnh Kiểm tra template tự động Long Khánh');
 check_url(get_permalink($id).'menu/','Menu đang được cập nhật');
 wp_update_post(array('ID'=>$id,'post_password'=>'lk-local-probe-password'));
 check_url(get_permalink($id),'name="post_password"');
 $protected=wp_remote_get(get_permalink($id),array('timeout'=>25));
 if(str_contains(wp_remote_retrieve_body($protected),'Địa chỉ kiểm tra template'))$failures[]='Password-protected listing data leaked';
 wp_update_post(array('ID'=>$id,'post_password'=>''));
 $review=wp_insert_comment(array('comment_post_ID'=>$id,'comment_type'=>'lk_review','comment_approved'=>1,'comment_content'=>'Kiểm tra tính điểm đánh giá.','user_id'=>get_current_user_id(),'comment_author'=>'Kiểm tra tự động'));
 update_comment_meta($review,'lk_rating',4);
 if((float)get_post_meta($id,'_lk_rating',true)!==4.0)$failures[]='Review summary not updated';
 wp_trash_comment($review);
 if((float)get_post_meta($id,'_lk_rating',true)!==0.0)$failures[]='Review moderation did not update summary';
}finally{wp_trash_post($id);}
foreach(get_option('lk_page_map',array()) as $page_id)check_url(get_permalink($page_id),'lk-site');
foreach(get_option('lk_blog_ids',array()) as $blog_id)check_url(get_permalink($blog_id),'Bài viết liên quan');
foreach(get_posts(array('post_type'=>'page','post_status'=>'publish','post_parent'=>get_option('lk_page_map')['promo'],'meta_key'=>'_lk_managed','meta_value'=>1,'posts_per_page'=>-1)) as $promo)check_url(get_permalink($promo),'Chương trình đã kết thúc');
$result=array('checks'=>$checks,'failures'=>$failures,'new_listing_check_id'=>$id,'test_post_status'=>get_post_status($id));
file_put_contents(__DIR__.'/verification-result.json',wp_json_encode($result,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
echo wp_json_encode(array('checked'=>count($checks),'failures'=>$failures,'test_post_status'=>get_post_status($id))),PHP_EOL;
exit($failures?1:0);
