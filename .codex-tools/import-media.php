<?php
if(PHP_SAPI!=='cli')exit;
require dirname(__DIR__).'/wp-load.php';
require_once ABSPATH.'wp-admin/includes/image.php';
set_time_limit(0);ini_set('memory_limit','768M');
$folder=ABSPATH.'wp-content/plugins/long-khanh-directory/assets/images/';
$manifest=json_decode(file_get_contents($folder.'manifest.json'),true);
$map=get_option('lk_media_map',array());
add_filter('intermediate_image_sizes_advanced',function($sizes){return array_intersect_key($sizes,array_flip(array('thumbnail','medium','medium_large','large')));});
foreach($manifest as $key=>$entry){
 if(isset($map[$key])&&(!empty($map[$key]['id'])&&get_post($map[$key]['id'])||empty($map[$key]['id'])))continue;
 $source=$folder.$entry['filename'];$type=wp_check_filetype($source);
 if(str_ends_with($source,'.svg')){$map[$key]=array('id'=>0,'url'=>plugins_url('long-khanh-directory/assets/images/'.$entry['filename']));continue;}
 $bits=wp_upload_bits('lk-'.$entry['filename'],null,file_get_contents($source));
 if(!empty($bits['error']))throw new RuntimeException($bits['error']);
 $id=wp_insert_attachment(array('post_title'=>'Long Khánh - '.$key,'post_mime_type'=>$type['type'],'post_status'=>'inherit'),$bits['file']);
 $metadata=wp_generate_attachment_metadata($id,$bits['file']);wp_update_attachment_metadata($id,$metadata);
 update_post_meta($id,'_wp_attachment_image_alt',str_replace(array('place-','gallery-','category-','region-','-'),array('','','','',' '),$key));
 $map[$key]=array('id'=>$id,'url'=>wp_get_attachment_url($id),'large'=>wp_get_attachment_image_url($id,'large'),'medium'=>wp_get_attachment_image_url($id,'medium_large'));
 update_option('lk_media_map',$map,false);echo $key.' '.$id.PHP_EOL;
}
update_option('lk_media_map',$map,false);file_put_contents(__DIR__.'/media-map.json',wp_json_encode($map,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
echo 'Imported '.count($map).' media assets.'.PHP_EOL;
