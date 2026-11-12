<?php
if(PHP_SAPI!=='cli')exit;
require dirname(__DIR__).'/wp-load.php';
$fixed=0;$anchor_fixes=0;
function lk_unique_nodes($nodes,&$seen,$doc,$path=''){
 global $fixed,$anchors,$anchor_fixes;
 foreach($nodes as $i=>&$node){
  $key=$path.'.'.$i;$id=$node['id'];
  if(isset($seen[$id])){$node['id']=substr(md5($doc.'-'.$key.'-'.$id),0,8);$fixed++;}
  $seen[$node['id']]=true;
  $anchor=$node['settings']['_element_id']??'';
  if($anchor){if(isset($anchors[$anchor])){unset($node['settings']['_element_id']);$anchor_fixes++;}else $anchors[$anchor]=true;}
  if(!empty($node['elements']))$node['elements']=lk_unique_nodes($node['elements'],$seen,$doc,$key);
 }
 return $nodes;
}
foreach(get_posts(array('post_type'=>array('page','post','elementor_library'),'post_status'=>'publish','meta_key'=>'_lk_managed','meta_value'=>1,'posts_per_page'=>-1)) as $post){
 $data=json_decode(get_post_meta($post->ID,'_elementor_data',true),true);if(!$data)continue;
 $seen=array();$anchors=array();$before=$fixed+$anchor_fixes;$data=lk_unique_nodes($data,$seen,$post->ID);
 if($fixed+$anchor_fixes!==$before){update_post_meta($post->ID,'_elementor_data',wp_slash(wp_json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)));delete_post_meta($post->ID,'_elementor_css');delete_post_meta($post->ID,'_elementor_element_cache');}
}
echo 'Repaired duplicate element IDs: '.$fixed.'; anchors: '.$anchor_fixes,PHP_EOL;
