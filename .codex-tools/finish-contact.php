<?php
if(PHP_SAPI!=='cli')exit;
require dirname(__DIR__).'/wp-load.php';
$users=get_users(array('role'=>'administrator','number'=>1));wp_set_current_user($users[0]->ID);
$folder=LK_DIR.'assets/images/';$manifest=json_decode(file_get_contents($folder.'manifest.json'),true);
$manifest['contact-illustration']=array('filename'=>'contact-illustration.svg','source'=>'https://diadiemlongkhanh.com/images/ddlk-contact-us.svg');
$manifest['footer-texture']=array('filename'=>'footer-texture.jpg','source'=>'https://ik.imagekit.io/reviewcafe/bg-dark_XOhLpLNAe0S.jpg');
file_put_contents($folder.'manifest.json',wp_json_encode($manifest,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
$id=get_option('lk_page_map')['contact'];$nodes=json_decode(get_post_meta($id,'_elementor_data',true),true);$section=&$nodes[0];
if(!str_contains($section['settings']['css_classes'],'lk-contact-card'))$section['settings']['css_classes'].=' lk-contact-card';
$items=&$section['elements'][0]['elements'];
foreach($items as &$copy)if(($copy['widgetType']??'')==='text-editor')$copy['settings']['editor']=str_replace('</p><p>Hoặc','<br>Hoặc',$copy['settings']['editor']);unset($copy);
foreach($items as $index=>$item){
 if(($item['widgetType']??'')!=='lk-form')continue;
 $form=$item;
 $items[$index]=array('id'=>'lkcont01','elType'=>'section','isInner'=>true,'settings'=>array('css_classes'=>'lk-contact-form-row','gap'=>'no'),'elements'=>array(
  array('id'=>'lkcont02','elType'=>'column','settings'=>array('_column_size'=>60),'elements'=>array($form)),
  array('id'=>'lkcont03','elType'=>'column','settings'=>array('_column_size'=>40),'elements'=>array(array('id'=>'lkcont04','elType'=>'widget','widgetType'=>'image','settings'=>array('image'=>array('id'=>0,'url'=>lk_asset('contact-illustration')),'image_size'=>'full'),'elements'=>array()))),
 ));
}
update_post_meta($id,'_elementor_data',wp_slash(wp_json_encode($nodes,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)));delete_post_meta($id,'_elementor_css');delete_post_meta($id,'_elementor_element_cache');
Elementor\Plugin::$instance->files_manager->clear_cache();
echo 'Contact layout and footer assets ready.',PHP_EOL;
