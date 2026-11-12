<?php
if(PHP_SAPI!=='cli')exit;
require dirname(__DIR__).'/wp-load.php';
$admins=get_users(array('role'=>'administrator','number'=>1));wp_set_current_user($admins[0]->ID);
$types=Elementor\Plugin::$instance->widgets_manager->get_widget_types();$counts=array();$missing=array();
function audit_nodes($nodes){global $types,$counts,$missing;foreach($nodes as $n){if($n['elType']==='widget'){$type=$n['widgetType'];$counts[$type]=($counts[$type]??0)+1;if(!isset($types[$type]))$missing[]=$type;}if(!empty($n['elements']))audit_nodes($n['elements']);}}
$docs=get_posts(array('post_type'=>array('page','post','elementor_library'),'post_status'=>'publish','meta_key'=>'_lk_managed','meta_value'=>1,'posts_per_page'=>-1));
foreach($docs as $p){$data=json_decode(get_post_meta($p->ID,'_elementor_data',true),true);if($data)audit_nodes($data);else $missing[]='document:'.$p->ID;}
$probe=lk_compat_render_blocks('<p>compatibility probe</p>',array('blockName'=>'core/paragraph','attrs'=>array()));
global $wp_version;
$listings=lk_posts();$individual=0;foreach($listings as $p)if(get_post_meta($p->ID,'_elementor_data',true))$individual++;
echo wp_json_encode(array('child'=>get_option('stylesheet'),'parent'=>get_option('template'),'wordpress'=>$wp_version,'php'=>PHP_VERSION,'documents'=>count($docs),'listings'=>count($listings),'individual_listing_documents'=>$individual,'shared_listing_template'=>(int)get_option('lk_template_listing'),'shared_gallery_template'=>(int)get_option('lk_template_listing_gallery'),'widgets'=>$counts,'missing'=>array_unique($missing),'gallery_class'=>get_class($types['image-gallery']),'legacy_block_hook_ok'=>$probe==='<p>compatibility probe</p>'),JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE),PHP_EOL;
