<?php
if(PHP_SAPI!=='cli')exit;
require dirname(__DIR__).'/wp-load.php';
$users=get_users(array('role'=>'administrator','number'=>1));wp_set_current_user($users[0]->ID);
$file=LK_DIR.'assets/images/manifest.json';$manifest=json_decode(file_get_contents($file),true);$manifest['promo-illustration']=array('filename'=>'promo-illustration.svg','source'=>'https://diadiemlongkhanh.com/images/ddlk-discount.svg');file_put_contents($file,wp_json_encode($manifest,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
$id=get_option('lk_page_map')['promo'];$data=json_decode(get_post_meta($id,'_elementor_data',true),true);
if(count($data[0]['elements'])===1){
 $data[0]['elements'][0]['settings']['_column_size']=67;
 $data[0]['elements'][]=array('id'=>'de785a01','elType'=>'column','settings'=>array('_column_size'=>33,'css_classes'=>'lk-promo-art'),'elements'=>array(array('id'=>'de785a02','elType'=>'widget','widgetType'=>'image','settings'=>array('image'=>array('id'=>0,'url'=>lk_asset('promo-illustration')),'image_size'=>'full'),'elements'=>array())));
}
foreach($data[1]['elements'] as &$column){
 if(!str_contains($column['settings']['css_classes'],'lk-expired'))$column['settings']['css_classes'].=' lk-expired';
 $column['elements'][2]['settings']['_css_classes']='lk-promo-date';
 $column['elements'][2]['settings']['editor']=preg_replace('/<br><strong>.*?<\/strong>/s',' <span class="lk-ended-badge">Đã kết thúc</span>',$column['elements'][2]['settings']['editor']);
 $column['elements'][4]['settings']['_css_classes']='lk-promo-address';
}unset($column);
update_post_meta($id,'_elementor_data',wp_slash(wp_json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)));delete_post_meta($id,'_elementor_css');delete_post_meta($id,'_elementor_element_cache');Elementor\Plugin::$instance->files_manager->clear_cache();
echo 'Promo layout and historical states updated.',PHP_EOL;
