<?php
if(PHP_SAPI!=='cli')exit;
require dirname(__DIR__).'/wp-load.php';
$missing=array();$libraries=array();
foreach(array('solid','regular','brands') as $library)$libraries['fa-'.$library]=json_decode(file_get_contents(ELEMENTOR_ASSETS_PATH.'lib/font-awesome/json/'.$library.'.json'),true)['icons'];
function lk_check_icons($data){global $missing,$libraries;foreach($data as $key=>$value){if($key==='selected_icon'&&is_array($value)&&!empty($value['value'])&&is_string($value['value'])&&str_starts_with($value['value'],'fa')){$icon=preg_replace('/^fa\w* fa-/','',$value['value']);if(!isset($libraries[$value['library']??''][$icon]))$missing[$value['value']]=true;}elseif(is_array($value))lk_check_icons($value);}}
foreach(get_posts(array('post_type'=>array('page','post','elementor_library'),'meta_key'=>'_lk_managed','meta_value'=>1,'post_status'=>'publish','posts_per_page'=>-1)) as $post){$data=json_decode(get_post_meta($post->ID,'_elementor_data',true),true);if($data)lk_check_icons($data);}
foreach(lk_posts() as $p)foreach(array('links','info','benefits','directions') as $binding)lk_check_icons(lk_resolve_listing_settings('icon-list',array('lk_binding'=>$binding),$p->ID));
echo wp_json_encode(array_keys($missing)),PHP_EOL;
