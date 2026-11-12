<?php
if(PHP_SAPI!=='cli')exit;
require dirname(__DIR__).'/wp-load.php';
$map=get_option('lk_page_map');$checks=array();$failures=array();
$pages=array('home','about','privacy','termsofuse','download','new-review','explore','promo','contact');$ids=array();foreach($pages as $slug)$ids[$slug]=$map[$slug];foreach(get_option('lk_blog_ids') as $slug=>$id)$ids[$slug]=$id;
foreach($ids as $key=>$id){$response=wp_remote_get(get_permalink($id),array('timeout'=>40));$body=is_wp_error($response)?'':wp_remote_retrieve_body($response);$status=is_wp_error($response)?0:wp_remote_retrieve_response_code($response);$ok=$status===200&&!preg_match('/(?:Fatal error|Deprecated:|Warning:|Notice:|Nội dung demo|Trải nghiệm demo|bản minh hoạ|dữ liệu minh hoạ|trình diễn giao diện)/u',$body);$checks[$key]=array('status'=>$status,'ok'=>$ok);if(!$ok)$failures[]=$key;}
$docs=get_posts(array('post_type'=>array('page','post','elementor_library'),'post_status'=>'publish','meta_key'=>'_lk_managed','meta_value'=>1,'posts_per_page'=>-1));$missing=array();$types=Elementor\Plugin::$instance->widgets_manager->get_widget_types();
function lk_phase_nodes($nodes){global $types,$missing;foreach($nodes as $node){if(($node['elType']??'')==='widget'&&!isset($types[$node['widgetType']]))$missing[]=$node['widgetType'];if(!empty($node['elements']))lk_phase_nodes($node['elements']);}}
foreach($docs as $doc)lk_phase_nodes(json_decode(get_post_meta($doc->ID,'_elementor_data',true),true)?:array());
$result=array('checks'=>$checks,'failures'=>$failures,'missing_widgets'=>array_unique($missing),'docs'=>count($docs),'ok'=>!$failures&&!$missing);file_put_contents(__DIR__.'/content-phase-result.json',wp_json_encode($result,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));echo wp_json_encode($result);exit($result['ok']?0:1);
