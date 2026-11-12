<?php
if(PHP_SAPI!=='cli')exit;
require dirname(__DIR__).'/wp-load.php';
lk_invalidate_public_index();
$index=lk_directory_index();$checks=array();
foreach(array(home_url('/'),lk_link('search').'?categories=mua-sam',lk_link('explore').'?lk_feed=following',get_permalink((int)get_option('lk_listing_preview'))) as $url){
 $response=wp_remote_get($url,array('timeout'=>25));
 $body=is_wp_error($response)?'':wp_remote_retrieve_body($response);
 $checks[]=array('url'=>$url,'status'=>is_wp_error($response)?0:wp_remote_retrieve_response_code($response),'ok'=>!is_wp_error($response)&&wp_remote_retrieve_response_code($response)===200&&!preg_match('/(?:Fatal error|Deprecated:|Warning:|Notice:)/',$body));
}
global $wpdb,$wp_version;
$result=array('wordpress'=>$wp_version,'php'=>PHP_VERSION,'child'=>get_option('stylesheet'),'parent'=>get_option('template'),'listings'=>count($index),'title_entities'=>array_values(array_filter(wp_list_pluck($index,'title'),function($title){return preg_match('/&#[0-9]+;/',$title);})),
 'published_test_posts'=>$wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_status='publish' AND post_name LIKE 'lk-%verification%'"),
 'temporary_profiler'=>file_exists(WPMU_PLUGIN_DIR.'/lk-local-profile.php'),'temporary_mobile_harness'=>file_exists(ABSPATH.'.codex-responsive-preview.html'),
 'checks'=>$checks);
file_put_contents(__DIR__.'/final-status.json',wp_json_encode($result,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
echo wp_json_encode($result,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),PHP_EOL;
