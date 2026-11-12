<?php
if(PHP_SAPI!=='cli')exit;require dirname(__DIR__).'/wp-load.php';
$map=get_option('lk_page_map');$nodes=json_decode(get_post_meta($map['about'],'_elementor_data',true),true);$test_reviews=get_comments(array('post_id'=>27039,'type'=>'lk_review','status'=>'trash'));
$media_status=array();foreach($test_reviews as $review)foreach(lk_review_photos($review) as $id)$media_status[$id]=get_post_status($id);
$result=array('wordpress'=>$GLOBALS['wp_version'],'php'=>PHP_VERSION,'child'=>get_option('stylesheet'),'parent'=>get_option('template'),'published_listings'=>count(lk_posts()),'about_title_restored'=>$nodes[0]['elements'][1]['elements'][0]['settings']['title']==='Hệ thống Địa Điểm Long Khánh','test_post_status'=>get_post_status(27039),'test_photo_status'=>$media_status,'composer_checks'=>json_decode(file_get_contents(__DIR__.'/composer-test.json'),true)['checks'],'page_checks'=>json_decode(file_get_contents(__DIR__.'/content-phase-result.json'),true)['ok']);
file_put_contents(__DIR__.'/final-phase-summary.json',wp_json_encode($result,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));echo wp_json_encode($result,JSON_UNESCAPED_UNICODE),PHP_EOL;
