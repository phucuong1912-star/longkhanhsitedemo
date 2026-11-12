<?php
if(PHP_SAPI!=='cli')exit;require dirname(__DIR__).'/wp-load.php';
$id=(int)get_option('page_on_front');$nodes=json_decode(get_post_meta($id,'_elementor_data',true),true);
foreach($nodes as &$node)if(str_contains($node['settings']['css_classes']??'','lk-contribute')){
 $node['elements'][1]['settings']['padding_mobile']=array('unit'=>'px','top'=>'20','right'=>'10','bottom'=>'20','left'=>'0','isLinked'=>false);
 foreach($node['elements'][1]['elements'] as &$widget)if(($widget['widgetType']??'')==='heading'){$widget['settings']['typography_typography']='custom';$widget['settings']['typography_line_height']=array('unit'=>'em','size'=>1.5715);}
 unset($widget);
}unset($node);
update_post_meta($id,'_elementor_data',wp_slash(wp_json_encode($nodes,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)));delete_post_meta($id,'_elementor_css');delete_post_meta($id,'_elementor_element_cache');
echo 'Native mobile contribution spacing updated.',PHP_EOL;
